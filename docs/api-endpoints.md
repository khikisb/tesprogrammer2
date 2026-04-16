# REST API Utama

Base URL: `http://localhost:3000/api`

## Auth

### POST /auth/login
Fungsi: login user dan mengembalikan JWT.

### GET /auth/me
Fungsi: ambil data user yang sedang login.

## Tasks

### GET /tasks
Fungsi: ambil semua task milik user.

### GET /tasks/:id
Fungsi: ambil detail task berdasarkan id.

### POST /tasks
Fungsi: bikin task baru.

Body:
```json
{
  "title": "Task baru",
  "description": "Isi task",
  "status": "todo",
  "due_date": "2026-04-20"
}
```

### PUT /tasks/:id
Fungsi: update task tertentu.

### DELETE /tasks/:id
Fungsi: hapus task tertentu.

## Logs

### GET /logs
Fungsi: lihat log aktivitas dasar milik user.
