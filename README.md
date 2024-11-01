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

### Get All Todos
- **Endpoint:** `/api/todos`
- **Method:** `GET`
- **Deskripsi:** Mendapatkan semua item Todo untuk pengguna yang sedang login. Logout user saat ini.
- **Header:** `Authorization: Bearer <token>`
- **Response:**
```json
{
  "data": [
    {
      "id": 9,
      "title": "create todo 2",
      "description": "desc create todo 2",
      "is_completed": 1
    }
  ]
}
```

### Get Todo by ID
- **Endpoint:** `/api/todos/{id}`
- **Method:** `GET`
- **Deskripsi:** Mendapatkan detail item Todo berdasarkan ID.
- **Header:** `Authorization: Bearer <token>`
- **Response:**
```json
{
  "data": {
    "id": 9,
    "title": "create todo 2",
    "description": "desc create todo 2",
    "is_completed": 1
  }
}
```

### Create a New Todo
- **Endpoint:** `/api/todos`
- **Method:** `POST`
- **Deskripsi:** Membuat item Todo baru.
- **Header:** `Authorization: Bearer <token>`
- **Request Body:**
```json
{
    "title" : "create todo 2",
    "description": "desc create todo 2",
    "is_completed": true
}
```
- **Response:**
```json
{
  "data": {
    "id": 9,
    "title": "create todo 2",
    "description": "desc create todo 2",
    "is_completed": true
  }
}
```

### Update a Todo
- **Endpoint:** `/api/todos/{id}`
- **Method:** `PUT`
- **Deskripsi:** Memperbarui item Todo berdasarkan ID.
- **Header:** `Authorization: Bearer <token>`
- **Request Body :**
  - `title` (string, optional): Judul Todo yang diperbarui.
  - `description` (string, optional): Deskripsi Todo yang diperbarui.
  - `is_completed` (boolean, optional): Status Todo (true jika sudah selesai, false jika belum).
- **Response:**
```json
{
  "data": {
    "id": 9,
    "title": "create todo 2",
    "description": "desc create todo 2",
    "is_completed": 0
  }
}
```

### Delete a Todo
- **Endpoint:** `/api/todos/{id}`
- **Method:** `DELETE`
- **Deskripsi:** Menghapus item Todo berdasarkan ID.
- **Header:** `Authorization: Bearer <token>`
- **Response:**
```json
{
  "message": "Todolist deleted successfully"
}
```
