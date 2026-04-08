# Medical System API

REST API for a medical management system built with **Laravel 11**, **PostgreSQL**, **JWT Auth**, and **Spatie Permission**.

---

## Stack

| Layer        | Technology                   |
|-------------|------------------------------|
| Framework    | Laravel 11                   |
| Database     | PostgreSQL 16                |
| Auth         | JWT (`tymon/jwt-auth`)        |
| Permissions  | Spatie Laravel Permission 6  |
| PDF          | barryvdh/laravel-dompdf      |
| Cache        | Redis 7                      |
| Web server   | Nginx + PHP-FPM 8.2          |
| Container    | Docker / Docker Compose      |

---

## Quick Start

### Prerequisites
- Docker ≥ 24
- Docker Compose ≥ 2

### 1. Clone & configure

```bash
cp .env.example .env
# Edit .env if you want custom DB credentials / ports
```

### 2. Start containers

```bash
docker-compose up -d
```

The entrypoint script will automatically:
- Generate `APP_KEY` and `JWT_SECRET`
- Run all migrations
- Seed roles, permissions, and demo users

### 3. Access the API

```
http://localhost:8000/api/v1
```

---

## Demo Credentials

| Role         | Email                            | Password        |
|-------------|----------------------------------|-----------------|
| Admin        | admin@medicalsystem.com          | Admin@123456    |
| Doctor       | doctor@medicalsystem.com         | Doctor@123456   |
| Receptionist | receptionist@medicalsystem.com   | Recep@123456    |

---

## API Endpoints

### Authentication
| Method | Endpoint               | Description        |
|--------|------------------------|--------------------|
| POST   | /api/v1/auth/login     | Login → JWT token  |
| POST   | /api/v1/auth/logout    | Logout (revoke)    |
| POST   | /api/v1/auth/refresh   | Refresh token      |
| GET    | /api/v1/auth/me        | Current user       |

### Users
| Method | Endpoint                       | Description     |
|--------|--------------------------------|-----------------|
| GET    | /api/v1/users                  | List users      |
| POST   | /api/v1/users                  | Create user     |
| GET    | /api/v1/users/{id}             | Get user        |
| PUT    | /api/v1/users/{id}             | Update user     |
| DELETE | /api/v1/users/{id}             | Delete user     |
| POST   | /api/v1/users/{id}/assign-role | Assign roles    |

### Patients
| Method | Endpoint                              | Description          |
|--------|---------------------------------------|----------------------|
| GET    | /api/v1/patients                      | List patients        |
| POST   | /api/v1/patients                      | Create + auto MR     |
| GET    | /api/v1/patients/{id}                 | Get patient          |
| PUT    | /api/v1/patients/{id}                 | Update patient       |
| DELETE | /api/v1/patients/{id}                 | Soft delete          |
| GET    | /api/v1/patients/{id}/medical-record  | Patient's record     |

### Medical Records
| Method | Endpoint                          | Description    |
|--------|-----------------------------------|----------------|
| GET    | /api/v1/medical-records/{id}      | Get record     |
| PUT    | /api/v1/medical-records/{id}      | Update record  |

### Consultations
| Method | Endpoint                   | Description        |
|--------|----------------------------|--------------------|
| GET    | /api/v1/consultations      | List               |
| POST   | /api/v1/consultations      | Create             |
| GET    | /api/v1/consultations/{id} | Get                |
| PUT    | /api/v1/consultations/{id} | Update             |
| DELETE | /api/v1/consultations/{id} | Soft delete        |

### Prescriptions
| Method | Endpoint                                       | Description      |
|--------|------------------------------------------------|------------------|
| POST   | /api/v1/consultations/{id}/prescriptions       | Create           |
| GET    | /api/v1/prescriptions/{id}                     | Get              |
| PUT    | /api/v1/prescriptions/{id}                     | Update           |
| DELETE | /api/v1/prescriptions/{id}                     | Delete           |
| GET    | /api/v1/prescriptions/{id}/pdf                 | Download PDF     |

### Appointments
| Method | Endpoint                    | Description |
|--------|-----------------------------|-------------|
| GET    | /api/v1/appointments        | List        |
| POST   | /api/v1/appointments        | Create      |
| GET    | /api/v1/appointments/{id}   | Get         |
| PUT    | /api/v1/appointments/{id}   | Update      |
| DELETE | /api/v1/appointments/{id}   | Soft delete |

### Files
| Method | Endpoint                       | Description      |
|--------|--------------------------------|------------------|
| GET    | /api/v1/files                  | List files       |
| POST   | /api/v1/files                  | Upload file      |
| GET    | /api/v1/files/{id}             | Get file info    |
| DELETE | /api/v1/files/{id}             | Delete file      |
| GET    | /api/v1/files/{id}/download    | Download file    |

---

## Authentication

All protected endpoints require a `Bearer` token in the `Authorization` header:

```
Authorization: Bearer <your-jwt-token>
```

### Login example

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@medicalsystem.com","password":"Admin@123456"}'
```

---

## Roles & Permissions

| Permission         | admin | doctor | receptionist |
|--------------------|:-----:|:------:|:------------:|
| users.*            |  ✓   |        |              |
| patients.view      |  ✓   |   ✓   |      ✓       |
| patients.create    |  ✓   |        |      ✓       |
| patients.update    |  ✓   |        |      ✓       |
| patients.delete    |  ✓   |        |              |
| medical_records.*  |  ✓   |   ✓   |   view only  |
| consultations.*    |  ✓   |   ✓   |  create/view |
| prescriptions.*    |  ✓   |   ✓   |              |
| appointments.*     |  ✓   |   ✓   |      ✓       |
| files.*            |  ✓   |   ✓   |      ✓       |

> Admins bypass **all** policy checks via `Gate::before`.

---

## Database Schema

```
users
  └── roles (via spatie)

patients
  ├── medical_records (1:1)
  │   └── consultations (1:N)
  │       ├── prescriptions (1:1)
  │       └── files (1:N)
  ├── appointments (1:N)
  └── files (1:N)
```

---

## Useful Commands

```bash
make up          # Start containers
make down        # Stop containers
make shell       # SSH into app container
make fresh       # migrate:fresh --seed
make logs        # Follow logs
make clear       # Clear all caches
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/    # 9 controllers
│   ├── Middleware/         # JwtMiddleware
│   ├── Requests/           # 12 FormRequests
│   └── Resources/          # 7 API Resources
├── Models/                 # 7 Eloquent models
├── Policies/               # 7 authorization policies
└── Providers/
    └── AppServiceProvider  # Policy registration + Gate::before

database/
├── migrations/             # 10 migration files
└── seeders/                # Roles + demo users

resources/views/
└── prescriptions/pdf.blade.php

docker/
├── nginx/default.conf
├── php/local.ini
└── entrypoint.sh
```
