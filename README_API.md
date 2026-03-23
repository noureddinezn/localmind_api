# LocalMind API - REST API Documentation

A modern REST API built with Laravel for the LocalMind application. This API enables users to ask questions, provide answers, and manage favorites with a secure authentication system powered by Laravel Sanctum.

## 📋 Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database](#database)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)
- [User Roles](#user-roles)
- [Project Structure](#project-structure)
- [Architecture](#architecture)
- [Testing](#testing)
- [Error Handling](#error-handling)
- [Performance Optimization](#performance-optimization)
- [Security](#security)
- [Deployment](#deployment)

## ✨ Features

### Authentication & User Management
- User registration with email validation
- Secure login with token-based authentication (Laravel Sanctum)
- User profile management
- Role-based access control (User/Admin)
- Logout with token revocation

### Questions Module
- Create, read, update, and delete questions
- Filter questions by location
- Search questions by title or content
- Geolocation-based search with radius filtering
- View questions with all associated responses
- Only question authors or admins can edit/delete

### Responses Module
- Add responses to questions
- View all responses for a question with pagination
- Delete responses (owner or admin only)
- Automatic association with user and question

### Favorites Module
- Add/remove questions from favorites
- View user's favorite questions with pagination
- Quick toggle endpoint for favorite management

### Statistics & Dashboard
- Global statistics for all users
- Personal statistics (my questions, responses, favorites)
- Admin-only features:
  - Most popular questions
  - Most active users
  - Total favorites count
  - Global response statistics

## 🛠 Technology Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/MariaDB
- **ORM**: Eloquent
- **API**: RESTful architecture
- **Validation**: Laravel Request Validation
- **HTTP Client**: JSON/REST

## 📦 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.2+
- Laravel 11
- Node.js & npm (for frontend if applicable)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd localmindApi
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Create Environment File
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Configure Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=localmind
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Seed Database (Optional)
```bash
php artisan db:seed
```

## ⚙️ Configuration

### Sanctum Configuration
Sanctum is configured in `config/sanctum.php`. The API uses stateless token authentication.

Key settings:
- Token expiration: Configurable via Sanctum
- CORS handling: Configure in `.env` if needed
- Middleware: Applied to protected routes in `routes/api.php`

### CORS Configuration
If you need to allow requests from a frontend application, configure CORS in `config/cors.php`:
```php
'allowed_origins' => ['http://localhost:3000', 'http://localhost:8080'],
```

## 📊 Database

### Migrations
The database includes the following tables:

1. **users** - User accounts with roles
2. **questions** - Questions asked by users
3. **responses** - Answers to questions
4. **favorites** - User's favorite questions
5. **personal_access_tokens** - Sanctum tokens

### Models
- `App\Models\User` - User model with authentication
- `App\Models\Question` - Question model with relationships
- `App\Models\Response` - Response/Answer model
- `App\Models\Favorite` - Favorite relationship model

### Relationships
```
User
├── questions (1:N)
├── responses (1:N)
└── favorites (1:N)

Question
├── user (N:1)
├── responses (1:N)
└── favorites (1:N)

Response
├── user (N:1)
└── question (N:1)

Favorite
├── user (N:1)
└── question (N:1)
```

## 🏃 Running the Application

### Development Server
```bash
php artisan serve
```
The API will be available at `http://localhost:8000/api`

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Run Tests
```bash
php artisan test
```

### Queue (if implemented)
```bash
php artisan queue:work
```

## 🔌 API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints
```
POST   /register                 - Register new user
POST   /login                    - Login user
POST   /logout                   - Logout (authenticated)
```

### Question Endpoints
```
GET    /questions                - List questions (paginated)
GET    /questions/{id}           - Get question details
POST   /questions                - Create question (authenticated)
PUT    /questions/{id}           - Update question (authenticated)
DELETE /questions/{id}           - Delete question (authenticated)
```

### Response Endpoints
```
GET    /questions/{id}/responses - List responses for question
POST   /questions/{id}/responses - Add response (authenticated)
DELETE /responses/{id}           - Delete response (authenticated)
```

### Favorite Endpoints
```
GET    /favorites                - List user's favorites (authenticated)
POST   /questions/{id}/favorite  - Toggle favorite (authenticated)
```

### User Endpoints
```
GET    /user                     - Get user profile (authenticated)
GET    /dashboard/stats          - Get dashboard statistics (authenticated)
```

## 🔐 Authentication

### Token-Based Authentication
The API uses Laravel Sanctum for stateless API authentication using tokens.

### Registration
```bash
POST /register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```bash
POST /login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

Response includes a token:
```json
{
  "message": "Connexion réussie",
  "user": { ... },
  "token": "1|abcdef..."
}
```

### Using the Token
Include the token in all authenticated requests:
```
Authorization: Bearer {token}
```

### Logout
```bash
POST /logout
Authorization: Bearer {token}
```

## 👥 User Roles

### Regular User
- Create own questions
- Edit/delete own questions
- Respond to any question
- Delete own responses
- Manage favorite questions
- View personal statistics

### Admin User
- All user permissions plus:
- Delete any question
- Delete any response
- View global statistics
- See most popular questions
- See most active users

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── QuestionController.php
│   │   │   ├── ResponseController.php
│   │   │   ├── FavoriteController.php
│   │   │   └── UserController.php
│   │   └── Controller.php
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── Question.php
│   ├── Response.php
│   └── Favorite.php
└── Providers/

database/
├── migrations/
│   ├── *_create_users_table.php
│   ├── *_create_questions_table.php
│   ├── *_create_responses_table.php
│   ├── *_create_favorites_table.php
│   └── *_create_personal_access_tokens_table.php
├── factories/
│   └── UserFactory.php
└── seeders/
    └── DatabaseSeeder.php

routes/
├── api.php          - API routes
├── web.php          - Web routes
└── console.php      - Console commands

config/
├── app.php          - Application configuration
├── auth.php         - Authentication configuration
├── database.php     - Database configuration
└── sanctum.php      - Sanctum configuration

storage/
├── logs/            - Application logs
└── app/             - File storage

tests/
├── Feature/         - Feature tests
└── Unit/            - Unit tests
```

## 🏗 Architecture

### Design Patterns Used

1. **MVC Pattern**
   - Controllers: Handle HTTP requests and business logic
   - Models: Data models with Eloquent ORM
   - Views: Not applicable for API (JSON responses)

2. **Repository Pattern** (Implicit via Eloquent)
   - Models serve as data access layer
   - Easy to swap implementations

3. **Active Record Pattern**
   - Eloquent ORM provides this pattern
   - Models contain both data and behavior

### Middleware Stack
```
1. Route Middleware
   └── auth:sanctum (for protected routes)
   └── CORS middleware
   └── Request validation
```

### Error Handling
- Validation errors: 422 Unprocessable Entity
- Not found: 404 Not Found
- Unauthorized: 401 Unauthorized
- Forbidden: 403 Forbidden
- Server error: 500 Internal Server Error

### Response Format
All responses are JSON with consistent structure:
```json
{
  "message": "Success message or error",
  "data": { ... },
  "errors": { }
}
```

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage
```

### Using Postman
Import the provided `LocalMind_API_Postman_Collection.json` file into Postman to test all endpoints.

Steps:
1. Open Postman
2. Click "Import"
3. Select `LocalMind_API_Postman_Collection.json`
4. Set `base_url` variable to `http://localhost:8000/api`
5. Set `token` variable after login
6. Test endpoints

## 🔍 Error Handling

### HTTP Status Codes
- **200** - OK (successful GET, PUT, DELETE)
- **201** - Created (successful POST)
- **400** - Bad Request (malformed request)
- **401** - Unauthorized (missing/invalid token)
- **403** - Forbidden (insufficient permissions)
- **404** - Not Found (resource doesn't exist)
- **422** - Unprocessable Entity (validation error)
- **500** - Internal Server Error

### Error Response Example
```json
{
  "message": "Validation failed",
  "errors": {
    "title": ["The title field is required."],
    "email": ["The email must be a valid email address."]
  }
}
```

## ⚡ Performance Optimization

### Eager Loading
Queries use `with()` to prevent N+1 problems:
```php
Question::with(['user', 'responses.user', 'favorites'])->get();
```

### Pagination
All list endpoints return paginated results (10 items per page):
```
GET /questions?page=2
```

### Indexing
Database indexes on frequently queried fields:
- `users.email` (for login)
- `questions.user_id` (for filtering)
- `responses.question_id` (for associations)
- `favorites.user_id` (for user favorites)

### Caching (Optional)
Can be implemented for statistics:
```php
Cache::remember('popular_questions', 3600, function () {
    return Question::withCount('responses')
        ->orderBy('responses_count', 'desc')
        ->limit(5)
        ->get();
});
```

## 🔒 Security

### Password Security
- Passwords are hashed using bcrypt (Laravel default)
- Passwords never returned in API responses (hidden in model)

### Token Security
- Tokens generated by Laravel Sanctum
- Tokens are revoked on logout
- Tokens should be stored securely on client (not localStorage without consideration)

### CORS Protection
- Configure allowed origins in `config/cors.php`
- Prevents cross-origin request attacks

### Input Validation
- All inputs validated on server side
- Request validation in controllers
- Prevents injection attacks

### Authorization Checks
- All protected routes require authentication
- Resource ownership verified before modifications
- Admin role required for sensitive operations

### Best Practices Implemented
- ✅ HTTPS in production (configure in load balancer)
- ✅ Input validation and sanitization
- ✅ Role-based access control
- ✅ Secure password hashing
- ✅ CORS configuration
- ✅ Rate limiting (can be added if needed)

## 🚀 Deployment

### Production Checklist
- [ ] Update `.env` with production database
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Generate new `APP_KEY`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear cache: `php artisan config:cache`
- [ ] Optimize autoloader: `composer install --optimize-autoloader`
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up SSL certificate
- [ ] Configure database backups
- [ ] Set up logging and monitoring

### Web Server Configuration
The API runs on the standard Laravel server. Configure your web server (Nginx/Apache) to point to the `public` directory.

### .env Production Settings
```env
APP_ENV=production
APP_DEBUG=false
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=yourdomain.com
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Eloquent Documentation](https://laravel.com/docs/eloquent)
- [API Documentation](./API_DOCUMENTATION.md)
- [UML Diagrams](./UML_DIAGRAMS.md)

## 📝 License

This project is part of an educational exercise.

## 👤 Author

LocalMind API - March 2026

---

**Last Updated**: March 15, 2026
**Status**: Production Ready ✅
