const express = require('express');
const { db, writeLog } = require('../db');
const authMiddleware = require('../middleware/auth');

const router = express.Router();
const allowedStatus = ['todo', 'in-progress', 'done'];

function validateTaskPayload(payload) {
  const errors = [];

  if (!payload.title || String(payload.title).trim() === '') {
    errors.push('Title wajib diisi.');
  }

  if (!payload.status || !allowedStatus.includes(payload.status)) {
    errors.push('Status harus salah satu dari: todo, in-progress, done.');
  }

  if (payload.due_date && !/^\d{4}-\d{2}-\d{2}$/.test(payload.due_date)) {
    errors.push('Format due_date harus YYYY-MM-DD.');
  }

  return errors;
}

router.use(authMiddleware);

router.get('/', (req, res) => {
  const tasks = db.prepare(`
    SELECT id, title, description, status, due_date, created_at, updated_at
    FROM tasks
    WHERE user_id = ?
    ORDER BY id DESC
  `).all(req.user.id);

  return res.json({ data: tasks });
});

router.get('/:id', (req, res) => {
  const task = db.prepare(`
    SELECT id, title, description, status, due_date, created_at, updated_at
    FROM tasks
    WHERE id = ? AND user_id = ?
  `).get(req.params.id, req.user.id);

  if (!task) {
    return res.status(404).json({ message: 'Task tidak ditemukan.' });
  }

  return res.json({ data: task });
});

router.post('/', (req, res) => {
  const { title, description = '', status, due_date = null } = req.body || {};
  const errors = validateTaskPayload({ title, status, due_date });

  if (errors.length > 0) {
    return res.status(400).json({ message: 'Validasi gagal.', errors });
  }

  const result = db.prepare(`
    INSERT INTO tasks (user_id, title, description, status, due_date)
    VALUES (?, ?, ?, ?, ?)
  `).run(req.user.id, title.trim(), description, status, due_date);

  const taskId = result.lastInsertRowid;
  const task = db.prepare('SELECT * FROM tasks WHERE id = ?').get(taskId);

  writeLog({
    userId: req.user.id,
    action: 'CREATE_TASK',
    entityType: 'task',
    entityId: Number(taskId),
    description: `Bikin task baru: ${title}`,
    ipAddress: req.ip,
  });

  return res.status(201).json({ message: 'Task berhasil dibuat.', data: task });
});

router.put('/:id', (req, res) => {
  const taskId = Number(req.params.id);
  const existingTask = db.prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?').get(taskId, req.user.id);

  if (!existingTask) {
    return res.status(404).json({ message: 'Task tidak ditemukan.' });
  }

  const title = req.body.title ?? existingTask.title;
  const description = req.body.description ?? existingTask.description;
  const status = req.body.status ?? existingTask.status;
  const due_date = req.body.due_date ?? existingTask.due_date;

  const errors = validateTaskPayload({ title, status, due_date });
  if (errors.length > 0) {
    return res.status(400).json({ message: 'Validasi gagal.', errors });
  }

  db.prepare(`
    UPDATE tasks
    SET title = ?, description = ?, status = ?, due_date = ?, updated_at = CURRENT_TIMESTAMP
    WHERE id = ? AND user_id = ?
  `).run(String(title).trim(), description, status, due_date, taskId, req.user.id);

  const updatedTask = db.prepare('SELECT * FROM tasks WHERE id = ?').get(taskId);

  writeLog({
    userId: req.user.id,
    action: 'UPDATE_TASK',
    entityType: 'task',
    entityId: taskId,
    description: `Update task: ${title}`,
    ipAddress: req.ip,
  });

  return res.json({ message: 'Task berhasil diupdate.', data: updatedTask });
});

router.delete('/:id', (req, res) => {
  const taskId = Number(req.params.id);
  const existingTask = db.prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?').get(taskId, req.user.id);

  if (!existingTask) {
    return res.status(404).json({ message: 'Task tidak ditemukan.' });
  }

  db.prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?').run(taskId, req.user.id);

  writeLog({
    userId: req.user.id,
    action: 'DELETE_TASK',
    entityType: 'task',
    entityId: taskId,
    description: `Hapus task: ${existingTask.title}`,
    ipAddress: req.ip,
  });

  return res.json({ message: 'Task berhasil dihapus.' });
});

module.exports = router;
