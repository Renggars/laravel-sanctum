# Todolist API Documentation

API ini menyediakan layanan untuk membuat dan mengelola todolist. Seluruh endpoint yang berhubungan dengan todolist memerlukan autentikasi menggunakan `sanctum`.

## Daftar Endpoint

### Autentikasi

---

### Register

- **Endpoint:** `/api/register`
- **Method:** `POST`
- **Deskripsi:** Mendaftarkan user baru.
- **Body Request:**
  - `name` (string, required, max: 100): Nama user.
  - `email` (string, required, email format, max: 255, unique): Email user.
  - `password` (string, required, min: 8, max: 100): Password user.
- **Response:**
  - **Status Code:** `201 Created`
  - **Body Response:**
```json
{
    "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com"
    },
    "access_token": "user-authentication-token"
}
```

### Login
- **Endpoint:** `/api/login`
- **Method:** `POST`
- **Deskripsi:** Login user yang sudah terdaftar.
- **Body Request:**
  - `email` (string, required): Email user.
  - `password` (string, required): Password user.
- **Response:**
  - **Status Code:** `201 Created`
  - **Body Response:**
```json
{
    "access_token": "user-authentication-token"
}
```

### Logout
- **Endpoint:** `/api/logout`
- **Method:** `POST`
- **Deskripsi:** Logout user saat ini.
- **Header:** `Authorization: Bearer <token>`
- **Response:**
```json
{
  "message": "Successfully logged out"
}
```

---

### Todos API
**Note:  Semua endpoint /todos memerlukan autentikasi menggunakan token Sanctum.**

---
