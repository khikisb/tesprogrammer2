const jwt = require('jsonwebtoken');
const { db } = require('../db');

function authMiddleware(req, res, next) {
  const authHeader = req.headers.authorization || '';
  const [type, token] = authHeader.split(' ');

  if (type !== 'Bearer' || !token) {
    return res.status(401).json({ message: 'Akses ditolak. Token tidak ada atau format salah.' });
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'task-manager-rahasia');
    const user = db.prepare('SELECT id, name, email FROM users WHERE id = ?').get(decoded.userId);

    if (!user) {
      return res.status(401).json({ message: 'User tidak ditemukan.' });
    }

    req.user = user;
    next();
  } catch (error) {
    return res.status(401).json({ message: 'Token tidak valid atau sudah expired.' });
  }
}

module.exports = authMiddleware;
