# Desain Arsitektur

## Overview
Aplikasi dibagi jadi 3 lapisan utama:

1.  Frontend PHP 
2.  Backend REST API Node.js 
3.  Database SQLite 

## Flow utama

```text
Browser
  -> PHP Frontend
  -> REST API Node.js
  -> SQLite
```

## Penjelasan tiap lapisan

### Frontend PHP
Frontend dipakai buat:
- halaman login
- dashboard task
- form tambah task
- form edit task
- aksi hapus task

Frontend menyimpan token JWT di session PHP. Jadi user experience tetap simpel, tapi akses API tetap aman.

### Backend Node.js
Backend menangani:
- login
- validasi JWT
- CRUD task
- validasi field input
- pencatatan activity log

### Database SQLite
Database menyimpan 3 data inti:
- user
- task
- activity_logs

## Komunikasi antar komponen

- User isi form login di PHP
- PHP kirim request ke `POST /api/auth/login`
- Node.js validasi user
- Jika valid, API kirim JWT
- JWT disimpan di session PHP
- Untuk request berikutnya, PHP mengirim JWT ke backend lewat header `Authorization: Bearer <token>`

## Kenapa desain ini dipilih

- gampang dipahami reviewer
- tetap memisahkan tanggung jawab frontend dan backend
- scalable kalau nanti frontend mau diganti jadi SPA
- cocok buat test coding dengan waktu terbatas
