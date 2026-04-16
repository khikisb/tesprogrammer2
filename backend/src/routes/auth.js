const express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { db, writeLog } = require('../db');
const authMiddleware = require('../middleware/auth');

const router = express.Router();

router.post('/login', (req, res) => {
  const { email, password } = req.body || {};

  if (!email || !password) {
    return res.status(400).json({ message: 'Email dan password wajib diisi.' });
  }

  const user = db.prepare('SELECT * FROM users WHERE email = ?').get(email);

  if (!user) {
    return res.status(401).json({ message: 'Email atau password salah.' });
  }

  const isMatch = bcrypt.compareSync(password, user.password_hash);
  if (!isMatch) {
    return res.status(401).json({ message: 'Email atau password salah.' });
  }

  const token = jwt.sign(
    { userId: user.id, email: user.email },
    process.env.JWT_SECRET || 'task-manager-rahasia',
    { expiresIn: '8h' }
  );

  writeLog({
    userId: user.id,
    action: 'LOGIN',
    description: `User ${user.email} login`,
    ipAddress: req.ip,
  });

  return res.json({
    message: 'Login berhasil.',
    token,
    user: {
      id: user.id,
      name: user.name,
      email: user.email,
    },
  });
});

router.get('/me', authMiddleware, (req, res) => {
  return res.json({ user: req.user });
});

module.exports = router;
