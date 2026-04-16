require('dotenv').config();
const express = require('express');
const cors = require('cors');
require('./db');

const authRoutes = require('./routes/auth');
const taskRoutes = require('./routes/tasks');
const logRoutes = require('./routes/logs');

const app = express();

app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
  res.json({
    app: 'Task Manager API',
    status: 'ok',
    message: 'API jalan normal.',
  });
});

app.use('/api/auth', authRoutes);
app.use('/api/tasks', taskRoutes);
app.use('/api/logs', logRoutes);

app.use((req, res) => {
  res.status(404).json({ message: 'Endpoint tidak ditemukan.' });
});

app.use((error, req, res, next) => {
  console.error(error);
  res.status(500).json({ message: 'Terjadi error di server.' });
});

module.exports = app;
