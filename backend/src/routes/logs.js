const express = require('express');
const { db } = require('../db');
const authMiddleware = require('../middleware/auth');

const router = express.Router();
router.use(authMiddleware);

router.get('/', (req, res) => {
  const logs = db.prepare(`
    SELECT id, action, entity_type, entity_id, description, ip_address, created_at
    FROM activity_logs
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 50
  `).all(req.user.id);

  return res.json({ data: logs });
});

module.exports = router;
