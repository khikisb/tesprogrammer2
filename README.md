# Task Manager - Submission Test Programmer

Halo, ini jawaban saya untuk soal  Task Manager .  
Saya bikin project ini dengan pembagian seperti ini:

- Frontend: PHP native + HTML + CSS sederhana
- Backend API: Node.js + Express
-  Database relasional:  SQLite
-  Autentikasi:  JWT sederhana
-  Log aktivitas:  dicatat ke tabel `activity_logs`

Jadi walaupun front-end saya pakai PHP, alur aplikasinya tetap rapi: PHP cuma jadi tampilan yang konsumsi API dari backend Node.js.

---

## 1. Gambaran singkat aplikasi

Aplikasi ini dipakai buat internal tim untuk ngatur task. User bisa:

- login dulu
- lihat daftar task
- tambah task
- edit task
- hapus task
- lihat detail singkat status task

Setiap task punya atribut:

- `title`
- `description`
- `status` (`todo`, `in-progress`, `done`)
- `due_date`

Selain itu, semua aksi penting seperti login, create task, update task, dan delete task bakal masuk ke log aktivitas dasar.

---

## 2. Desain arsitektur aplikasi

### Komponen utama

#### a. Frontend PHP
Bagian ini menangani tampilan halaman, form login, form tambah/edit task, dan tabel daftar task.  
Frontend  tidak akses database langsung . Semua data diambil dari REST API backend.

#### b. Backend Node.js
Bagian ini jadi pusat logika aplikasi. Tugasnya:

- validasi request
- autentikasi user
- CRUD task
- simpan log aktivitas
- komunikasi ke database

#### c. Database SQLite
Dipakai buat menyimpan:

- data user
- data task
- data log aktivitas

### Cara mereka saling komunikasi

Alurnya seperti ini:

1. User buka halaman PHP.
2. Saat login, frontend PHP kirim request ke endpoint login di backend Node.js.
3. Backend cek email dan password.
4. Kalau valid, backend kirim JWT token.
5. Token disimpan di session PHP.
6. Saat user buka dashboard / tambah / edit / hapus task, frontend PHP kirim request ke API sambil bawa token.
7. Backend validasi token, proses request, simpan ke database, lalu balikin response JSON.

### Ringkas arsitektur

```text
User Browser
   ↓
Frontend PHP (render halaman + kirim request)
   ↓ HTTP/JSON
Backend Node.js REST API
   ↓
SQLite Database
```

---

## 3. Desain model data / schema database

### Tabel `users`
Dipakai buat user yang boleh login ke aplikasi.

### Tabel `tasks`
Dipakai buat simpan task.


### Tabel `activity_logs`
Dipakai buat log aktivitas dasar.


### Relasi utama

- 1 user bisa punya banyak task
- 1 user bisa punya banyak activity log

---

## 4. Desain endpoint REST API utama

Base URL:

```text
http://localhost:3000/api
```

### Auth

#### `POST /auth/login`
Buat login user.

Request body:

```json
{
  "email": "admin@example.com",
  "password": "admin123"
}
```

Response:
- token JWT
- data user

#### `GET /auth/me`
Ambil data user yang sedang login.

---

### Task

#### `GET /tasks`
Ambil semua task milik user yang login.

#### `GET /tasks/:id`
Ambil detail 1 task.

#### `POST /tasks`
Buat task baru.

Request body:

```json
{
  "title": "Siapin report mingguan",
  "description": "Rekap task minggu ini",
  "status": "todo",
  "due_date": "2026-04-20"
}
```

#### `PUT /tasks/:id`
Update task.

#### `DELETE /tasks/:id`
Hapus task.

---

### Log

#### `GET /logs`
Ambil log aktivitas user yang sedang login.  
Endpoint ini saya tambahkan supaya fitur log dasarnya benar-benar kelihatan waktu dicek.

---

## 5. Pilihan implementasi: Node.js vs Go

Di soal disebut backend bisa pakai Node.js dan/atau Go.  
Untuk project ini saya  pilih full backend di Node.js , karena:

1.  Lebih cepat buat dikerjain  untuk scope test seperti ini.
2. Ekosistem Express sederhana dan cocok buat REST API CRUD.
3. Lebih enak kalau mau sinkron dengan frontend yang butuh iterasi cepat.
4. Buat fitur basic seperti auth, CRUD task, dan logging, Node.js sudah lebih dari cukup.

Kalau mau dibagi dengan Go, menurut saya Go lebih cocok buat:

- background worker
- service logging yang traffic-nya tinggi
- service yang butuh concurrency lebih berat

Tapi untuk kebutuhan tes ini, pakai full Node.js jauh lebih efisien, lebih simpel di-review, dan tetap memenuhi requirement.

---

## 6. Strategi dasar testing

Minimal endpoint penting yang harus dites menurut saya:

### a. Login
Yang dites:
- email & password valid → harus dapat token
- password salah → harus gagal
- email tidak ada → harus gagal

### b. Create task
Yang dites:
- request valid + token valid → task berhasil dibuat
- tanpa token → harus ditolak
- title kosong → harus ditolak
- status di luar enum → harus ditolak

### c. Update task
Yang dites:
- task milik user sendiri → boleh update
- task tidak ditemukan → gagal
- data invalid → gagal

### d. Delete task
Yang dites:
- task ada → berhasil dihapus
- task tidak ada → gagal
- setelah dihapus, task tidak muncul lagi di list

### e. Log aktivitas
Yang dites:
- setelah login, create, update, delete → harus ada catatan log baru

Untuk file ini saya juga sertakan  Postman collection  biar endpoint penting bisa dites cepat.

---

## 7. Struktur folder project

```text
task-manager-submission/
├── README.md
├── docs/
│   ├── architecture.md
│   ├── api-endpoints.md
│   ├── schema.sql
│   ├── testing.md
│   └── task-manager.postman_collection.json
├── backend/
│   ├── package.json
│   ├── .env.example
│   └── src/
│       ├── app.js
│       ├── server.js
│       ├── db.js
│       ├── middleware/
│       │   └── auth.js
│       └── routes/
│           ├── auth.js
│           └── tasks.js
└── frontend/
    ├── index.php
    ├── dashboard.php
    ├── edit.php
    ├── save_task.php
    ├── delete_task.php
    ├── logout.php
    ├── config.php
    ├── functions.php
    └── assets/
        └── style.css
```

---

## 8. Cara jalanin project

### Backend
Masuk ke folder backend:

```bash
cd backend
npm install
npm run dev
```

Backend jalan di:

```text
http://localhost:3000
```

### Frontend PHP
Masuk ke folder frontend lalu jalankan PHP built-in server:

```bash
cd frontend
php -S localhost:8000
```

Frontend jalan di:

```text
http://localhost:8000
```

---

## 9. Akun login default

Supaya gampang dites, saya seed 1 user default:

-  Email:  admin@example.com
-  Password:  admin123

---

## 10. Catatan penutup

Saya sengaja bikin versi yang simpel tapi tetap rapi dan enak dicek.  
Fokus saya di sini bukan bikin UI yang terlalu rame, tapi memastikan requirement utama di soal benar-benar jalan:

- CRUD task
- REST API
- autentikasi sederhana
- database relasional
- log aktivitas dasar
- penjelasan arsitektur, schema, endpoint, pilihan teknologi, dan testing

Kalau repo ini mau dikembangin lagi, next step yang paling masuk akal menurut saya:

- tambah register user
- tambah filter task per status
- pagination
- unit test otomatis
- refresh token / logout token blacklist
- role admin

