# LocalMind API - UML Diagrams

## Entity Relationship Diagram (ERD)

```
+─────────────────────┐
│      Users          │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ email (UNIQUE)      │
│ password            │
│ role (user/admin)   │
│ created_at          │
│ updated_at          │
└──────────┬──────────┘
           │
           │ 1:N
           ├──────────────────────────┐
           │                          │
           ▼                          ▼
    +─────────────────────┐    +──────────────────┐
    │    Questions        │    │    Responses     │
    ├─────────────────────┤    ├──────────────────┤
    │ id (PK)             │    │ id (PK)          │
    │ user_id (FK)        │    │ user_id (FK)     │
    │ title               │    │ question_id (FK) │
    │ content             │    │ content          │
    │ location            │    │ created_at       │
    │ latitude (NULL)     │    │ updated_at       │
    │ longitude (NULL)    │    └──────────────────┘
    │ created_at          │
    │ updated_at          │
    └──────────┬──────────┘
               │
               │ 1:N
               ▼
    +─────────────────────┐
    │    Favorites        │
    ├─────────────────────┤
    │ id (PK)             │
    │ user_id (FK)        │
    │ question_id (FK)    │
    │ created_at          │
    │ updated_at          │
    └─────────────────────┘
```

## Class Diagram

```
┌────────────────────────────────────────────┐
│           User (Authenticatable)            │
├────────────────────────────────────────────┤
│ - id: int                                  │
│ - name: string                             │
│ - email: string                            │
│ - password: string                         │
│ - role: enum(user, admin)                  │
│ - created_at: timestamp                    │
│ - updated_at: timestamp                    │
├────────────────────────────────────────────┤
│ + questions(): HasMany                     │
│ + favorites(): HasMany                     │
│ + responses(): HasMany                     │
│ + isAdmin(): bool                          │
└────────────────────────────────────────────┘
           ▲         ▲          ▲
           │         │          │
    1:N relationship│ 1:N       │ 1:N
           │         │          │
           ▼         ▼          ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │  Question    │ │  Favorite    │ │  Response    │
    ├──────────────┤ ├──────────────┤ ├──────────────┤
    │ - id: int    │ │ - id: int    │ │ - id: int    │
    │ - title      │ │ - user_id    │ │ - content    │
    │ - content    │ │ - question_id│ │ - user_id    │
    │ - location   │ │ - created_at │ │ - question_id│
    │ - latitude   │ │ - updated_at │ │ - created_at │
    │ - longitude  │ └──────────────┘ │ - updated_at │
    │ - user_id    │                  └──────────────┘
    │ - created_at │                          ▲
    │ - updated_at │                          │ 1:N
    ├──────────────┤                          │
    │ + user()     │ (inverse)      ┌─────────┴────────┐
    │ + responses()│─────────────────│ Question         │
    │ + favorites()│                │ + responses()    │
    └──────────────┘                └──────────────────┘
```

## API Controller Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  Routing (api.php)                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Public Routes (No Auth Required)                    │   │
│  ├─────────────────────────────────────────────────────┤   │
│  │ POST   /register → AuthController@register          │   │
│  │ POST   /login → AuthController@login                │   │
│  │ GET    /questions → QuestionController@index        │   │
│  │ GET    /questions/{id} → QuestionController@show    │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Protected Routes (Auth:Sanctum)                     │   │
│  ├─────────────────────────────────────────────────────┤   │
│  │ Authentication:                                     │   │
│  │   POST   /logout → AuthController@logout            │   │
│  │                                                     │   │
│  │ Questions:                                          │   │
│  │   POST   /questions → QuestionController@store      │   │
│  │   PUT    /questions/{id} → QuestionController@update│   │
│  │   DELETE /questions/{id} → QuestionController@destroy
│  │                                                     │   │
│  │ Responses:                                          │   │
│  │   GET    /questions/{id}/responses                  │   │
│  │           → ResponseController@index                │   │
│  │   POST   /questions/{id}/responses                  │   │
│  │           → ResponseController@store                │   │
│  │   DELETE /responses/{id} → ResponseController@destroy
│  │                                                     │   │
│  │ Favorites:                                          │   │
│  │   GET    /favorites → FavoriteController@index      │   │
│  │   POST   /questions/{id}/favorite                   │   │
│  │           → FavoriteController@toggle               │   │
│  │                                                     │   │
│  │ User Profile:                                       │   │
│  │   GET    /user → UserController@profile             │   │
│  │   GET    /dashboard/stats → UserController@stats    │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
         ▼              ▼             ▼           ▼       ▼
    ┌─────────┐  ┌──────────┐  ┌──────────┐ ┌────────┐ ┌─────────┐
    │  Auth   │  │Question  │  │ Response │ │Favorite│ │  User   │
    │Controller│  │Controller│  │Controller│ │Contr.  │ │Controller│
    └─────────┘  └──────────┘  └──────────┘ └────────┘ └─────────┘
```

## Authentication Flow

```
User Registration/Login
        │
        ▼
   ┌─────────┐
   │ AuthController
   │  register()
   │  login()
   └────┬────┘
        │
        ▼
   Create/Find User
   Hash Password
        │
        ▼
   Laravel Sanctum
   Generate Token
        │
        ▼
   Return Token to Client
        │
        ▼
   Client stores token
        │
        ▼
   Include in Authorization Header
   for protected requests
        │
        │ Authorization: Bearer {token}
        │
        ▼
   Sanctum Middleware validates
        │
        ├─ Valid ──────────────────► Allow Request
        │
        └─ Invalid ────────────────► Return 401
```

## Request/Response Lifecycle

```
┌──────────────────┐
│ HTTP Request     │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────┐
│ Route Matching           │
│ (routes/api.php)         │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Middleware Stack:        │
│ 1. Auth:Sanctum (token)  │
│ 2. Other middleware      │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Controller Action        │
│ (Business Logic)         │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Model Queries            │
│ (Eloquent ORM)           │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│ JSON Response            │
│ with HTTP Status Code    │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────┐
│ HTTP Response    │
└──────────────────┘
```

## Authorization Pattern

```
Request comes in with token
        │
        ▼
Middleware validates token
        │
    ├── Valid ──────┐
    │               │
    └── Invalid     ▼ Return 401
                 Extract User
                 from Token
                    │
                    ▼
            auth()->user()
            available in
            controller
                    │
                    ▼
            Check Authorization:
            - Is owner?
            - Is admin?
                    │
        ┌───────────┼───────────┐
        │           │           │
      Yes          No          Unauthorized
        ▼           ▼              ▼
   Allow       Check Again    Return 403
   Action     (admin check)
```

## Error Handling Flow

```
Request Processing
        │
        ▼
    ┌─────────────────┐
    │ Exception Occurs│
    └────────┬────────┘
             │
             ├──────── Validation Error ──────────┐
             │                                    ▼
             │                            Return 422 with
             │                            field errors
             │
             ├─────── Model Not Found ───────────┐
             │                                   ▼
             │                           Return 404
             │
             ├─────── Authentication Error ──────┐
             │                                   ▼
             │                           Return 401
             │
             ├─────── Authorization Error ───────┐
             │                                   ▼
             │                           Return 403
             │
             └─────── Server Error ──────────────┐
                                                 ▼
                                         Return 500
```

---

## Summary Statistics

- **Models**: 4 (User, Question, Response, Favorite)
- **Controllers**: 5 (Auth, Question, Response, Favorite, User)
- **Routes**: 20+ endpoints
- **Relationships**: 
  - User: 1:N with Questions, Responses, Favorites
  - Question: 1:N with Responses, Favorites
- **Authentication**: Laravel Sanctum (Token-based)
- **Authorization**: Role-based (user/admin)
