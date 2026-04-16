const path = require('path');
const Database = require('better-sqlite3');
const bcrypt = require('bcryptjs');

const dbFile = process.env.DB_FILE || './task_manager.db';
const dbPath = path.resolve(process.cwd(), dbFile);
const db = new Database(dbPath);

db.pragma('journal_mode = WAL');
db.pragma('foreign_keys = ON');

db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    status TEXT NOT NULL CHECK(status IN ('todo', 'in-progress', 'done')),
    due_date TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
  );

  CREATE TABLE IF NOT EXISTS activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action TEXT NOT NULL,
    entity_type TEXT,
    entity_id INTEGER,
    description TEXT,
    ip_address TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
  );
`);

const existingUser = db.prepare('SELECT id FROM users WHERE email = ?').get('admin@example.com');
if (!existingUser) {
  const passwordHash = bcrypt.hashSync('admin123', 10);
  db.prepare(
    'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
  ).run('Admin Internal', 'admin@example.com', passwordHash);
}

function writeLog({ userId = null, action, entityType = null, entityId = null, description = null, ipAddress = null }) {
  db.prepare(`
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address)
    VALUES (?, ?, ?, ?, ?, ?)
  `).run(userId, action, entityType, entityId, description, ipAddress);
}

module.exports = { db, writeLog };
