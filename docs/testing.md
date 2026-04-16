# Strategi Testing Dasar

## 1. Login
Skenario:
- login sukses dengan akun valid
- login gagal kalau password salah
- login gagal kalau email tidak terdaftar

## 2. GET /tasks
Skenario:
- berhasil ambil task kalau token valid
- gagal kalau tanpa token

## 3. POST /tasks
Skenario:
- berhasil bikin task dengan data valid
- gagal kalau title kosong
- gagal kalau status tidak sesuai enum
- gagal kalau tidak kirim token

## 4. PUT /tasks/:id
Skenario:
- berhasil update task kalau id valid
- gagal kalau task tidak ditemukan
- gagal kalau payload salah

## 5. DELETE /tasks/:id
Skenario:
- berhasil hapus task
- gagal kalau task tidak ditemukan
- pastikan task hilang dari hasil GET /tasks

## 6. GET /logs
Skenario:
- setelah login / create / update / delete harus muncul log baru
