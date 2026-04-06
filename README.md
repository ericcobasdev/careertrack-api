# 🚀 CareerTrack API

A RESTful API built with Laravel to manage and track job applications in a structured, scalable, and efficient way.

---

## 📌 Features

- 🔐 User authentication using Laravel Sanctum
- 📄 Full CRUD for job applications
- ✅ Request validation with Form Requests
- 🎯 API Resources for standardized responses
- 👤 User-based data isolation (multi-user ready)
- 📊 Application tracking (status, salary, source, etc.)

---

## 🛠️ Tech Stack

- Laravel 13.3.0
- PHP 8.3
- SQLite (development)
- Laravel Sanctum

---

## 📡 API Endpoints

### 🔐 Auth
- `POST /api/auth/register`
- `POST /api/auth/login`

### 📄 Job Applications
- `GET /api/applications`
- `POST /api/applications`
- `GET /api/applications/{id}`
- `PUT /api/applications/{id}`
- `PATCH /api/applications/{id}`
- `DELETE /api/applications/{id}`

### 📊 Stats
- `GET /api/stats`

---

## 📥 Example Request

```json
{
  "company_name": "Google",
  "position_title": "Backend Developer",
  "status": "applied",
  "source": "LinkedIn",
  "salary_min": 35000,
  "salary_max": 45000,
  "location": "Remote",
  "notes": "First application",
  "applied_at": "2026-04-05"
}
```

---

#### ⚙️ Installation


git clone https://github.com/ericcobasdev/careertrack-api.git
cd careertrack-api

composer install
cp .env.example .env
php artisan key:generate

php artisan migrate

php artisan serve

🔐 Authentication

This API uses Laravel Sanctum.
After login, include the token in headers:
Authorization: Bearer YOUR_TOKEN

📈 Project Status

🚧 This project is actively under development.

Upcoming features:
🔒 Authorization with Policies
📄 Pagination & filtering
📊 Advanced statistics
🐳 Docker setup
🧪 Automated testing

👨‍💻 Author

Eric Cobas
Full-Stack Developer (Laravel / React / Vue)