# LocalMind API Documentation

## Base URL
```
http://localhost:8000/api
```

## Table of Contents
1. [Authentication](#authentication)
2. [Questions](#questions)
3. [Responses](#responses)
4. [Favorites](#favorites)
5. [User Profile](#user-profile)
6. [Statistics](#statistics)
7. [Error Handling](#error-handling)

---

## Authentication

### Register
**POST** `/register`

Create a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
  "message": "Inscription réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "created_at": "2026-03-15T10:00:00Z",
    "updated_at": "2026-03-15T10:00:00Z"
  },
  "token": "1|abcdef..."
}
```

### Login
**POST** `/login`

Authenticate and get access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  },
  "token": "1|abcdef..."
}
```

### Logout
**POST** `/logout` (Authenticated)

Revoke the current access token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Déconnexion réussie"
}
```

---

## Questions

### List Questions
**GET** `/questions`

Get paginated list of questions with optional filtering and search.

**Query Parameters:**
- `location` (optional) - Filter by location (partial match)
- `search` (optional) - Search in title and content (partial match)
- `latitude` (optional) - Latitude for radius search
- `longitude` (optional) - Longitude for radius search
- `radius` (optional) - Radius in kilometers (requires latitude & longitude)
- `page` (optional) - Page number (default: 1)

**Examples:**
```
GET /questions?location=Paris&page=1
GET /questions?search=python
GET /questions?latitude=48.8566&longitude=2.3522&radius=10
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "title": "How to learn PHP?",
      "content": "I want to learn PHP...",
      "location": "Paris",
      "latitude": 48.8566,
      "longitude": 2.3522,
      "created_at": "2026-03-15T10:00:00Z",
      "updated_at": "2026-03-15T10:00:00Z",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      }
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/questions?page=1",
    "last": "http://localhost:8000/api/questions?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/questions?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 10,
    "to": 10,
    "total": 50
  }
}
```

### Get Question Details
**GET** `/questions/{id}`

Get a specific question with all responses and metadata.

**Response (200 OK):**
```json
{
  "question": {
    "id": 1,
    "user_id": 1,
    "title": "How to learn PHP?",
    "content": "I want to learn PHP programming...",
    "location": "Paris",
    "latitude": 48.8566,
    "longitude": 2.3522,
    "created_at": "2026-03-15T10:00:00Z",
    "updated_at": "2026-03-15T10:00:00Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "responses": [
      {
        "id": 1,
        "user_id": 2,
        "question_id": 1,
        "content": "Try the official PHP documentation...",
        "created_at": "2026-03-15T11:00:00Z",
        "updated_at": "2026-03-15T11:00:00Z",
        "user": {
          "id": 2,
          "name": "Jane Doe",
          "email": "jane@example.com"
        }
      }
    ],
    "favorites": []
  },
  "is_favorited": false,
  "response_count": 1,
  "favorite_count": 0
}
```

### Create Question
**POST** `/questions` (Authenticated)

Create a new question.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "How to learn Laravel?",
  "content": "I'm interested in learning Laravel framework for web development...",
  "location": "Paris",
  "latitude": 48.8566,
  "longitude": 2.3522
}
```

**Response (201 Created):**
```json
{
  "message": "Question créée avec succès",
  "question": {
    "id": 2,
    "user_id": 1,
    "title": "How to learn Laravel?",
    "content": "I'm interested in learning Laravel...",
    "location": "Paris",
    "latitude": 48.8566,
    "longitude": 2.3522,
    "created_at": "2026-03-15T12:00:00Z",
    "updated_at": "2026-03-15T12:00:00Z"
  }
}
```

### Update Question
**PUT** `/questions/{id}` (Authenticated)

Update your own question. Only the owner or admin can update.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "Updated title",
  "content": "Updated content",
  "location": "Lyon"
}
```

**Response (200 OK):**
```json
{
  "message": "Question mise à jour avec succès",
  "question": {
    "id": 2,
    "user_id": 1,
    "title": "Updated title",
    "content": "Updated content",
    "location": "Lyon",
    "created_at": "2026-03-15T12:00:00Z",
    "updated_at": "2026-03-15T13:00:00Z"
  }
}
```

### Delete Question
**DELETE** `/questions/{id}` (Authenticated)

Delete a question. Only the owner or admin can delete.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Question supprimée avec succès"
}
```

---

## Responses

### List Responses for Question
**GET** `/questions/{id}/responses`

Get paginated list of responses for a specific question.

**Query Parameters:**
- `page` (optional) - Page number (default: 1)

**Response (200 OK):**
```json
{
  "question_id": 1,
  "responses": {
    "data": [
      {
        "id": 1,
        "user_id": 2,
        "question_id": 1,
        "content": "Try the official PHP documentation...",
        "created_at": "2026-03-15T11:00:00Z",
        "updated_at": "2026-03-15T11:00:00Z",
        "user": {
          "id": 2,
          "name": "Jane Doe",
          "email": "jane@example.com"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 10,
      "total": 1
    }
  }
}
```

### Create Response
**POST** `/questions/{id}/responses` (Authenticated)

Add a response to a question.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "content": "I recommend checking out Laravel's official documentation and tutorials..."
}
```

**Response (201 Created):**
```json
{
  "message": "Réponse ajoutée avec succès",
  "response": {
    "id": 2,
    "user_id": 1,
    "question_id": 1,
    "content": "I recommend checking out Laravel's official documentation...",
    "created_at": "2026-03-15T14:00:00Z",
    "updated_at": "2026-03-15T14:00:00Z"
  }
}
```

### Delete Response
**DELETE** `/responses/{id}` (Authenticated)

Delete a response. Only the owner or admin can delete.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Réponse supprimée avec succès"
}
```

---

## Favorites

### List Favorites
**GET** `/favorites` (Authenticated)

Get paginated list of user's favorite questions.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional) - Page number (default: 1)

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "question_id": 2,
      "created_at": "2026-03-15T15:00:00Z",
      "updated_at": "2026-03-15T15:00:00Z",
      "question": {
        "id": 2,
        "user_id": 3,
        "title": "How to learn Laravel?",
        "content": "I'm interested in learning Laravel...",
        "location": "Paris"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 5
  }
}
```

### Toggle Favorite
**POST** `/questions/{id}/favorite` (Authenticated)

Add or remove a question from favorites.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (201 Created) - When adding:**
```json
{
  "message": "Ajouté aux favoris"
}
```

**Response (200 OK) - When removing:**
```json
{
  "message": "Retiré des favoris"
}
```

---

## User Profile

### Get Profile
**GET** `/user` (Authenticated)

Get current user's profile and statistics.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "created_at": "2026-03-15T10:00:00Z",
    "updated_at": "2026-03-15T10:00:00Z",
    "questions": [],
    "favorites": [],
    "responses": []
  },
  "is_admin": false,
  "stats": {
    "total_questions": 2,
    "total_responses": 1,
    "total_favorites": 3
  }
}
```

---

## Statistics

### Dashboard Stats
**GET** `/dashboard/stats` (Authenticated)

Get statistics based on user role.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK) - Regular User:**
```json
{
  "stats": {
    "total_questions": 50,
    "total_users": 20,
    "total_responses": 150,
    "my_questions": 3,
    "my_responses": 8,
    "my_favorites": 12
  },
  "is_admin": false
}
```

**Response (200 OK) - Admin User:**
```json
{
  "stats": {
    "total_questions": 50,
    "total_users": 20,
    "total_responses": 150,
    "my_questions": 5,
    "my_responses": 15,
    "my_favorites": 10,
    "total_favorites": 200,
    "popular_questions": [
      {
        "id": 1,
        "title": "How to learn PHP?",
        "responses_count": 15,
        "created_at": "2026-03-10T10:00:00Z"
      }
    ],
    "active_users": [
      {
        "id": 2,
        "name": "Jane Doe",
        "questions_count": 10,
        "responses_count": 25
      }
    ]
  },
  "is_admin": true
}
```

---

## Error Handling

### Error Response Format
All errors follow this format:

```json
{
  "message": "Error message",
  "errors": {
    "field_name": ["Error details"]
  }
}
```

### HTTP Status Codes

| Status | Description |
|--------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request parameters |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Access denied |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Server error |

### Common Errors

**Authentication Error (401):**
```json
{
  "message": "Les identifiants fournis sont incorrects"
}
```

**Unauthorized Action (403):**
```json
{
  "message": "Non autorisé à modifier cette question"
}
```

**Resource Not Found (404):**
```json
{
  "message": "No query results for model [App\\Models\\Question]."
}
```

**Validation Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "email": ["The email must be a valid email address."]
  }
}
```

---

## Security

- All endpoints except `/register`, `/login`, and public `/questions` endpoints require authentication
- Use Bearer token in Authorization header: `Authorization: Bearer {token}`
- Tokens are revoked when user logs out
- Admin users (role='admin') have additional privileges:
  - Can delete any question
  - Can delete any response
  - Can view global statistics

---

## Rate Limiting

Currently no rate limiting is enforced. This may be added in future versions.

---

## Pagination

All list endpoints return paginated results:
- Default page size: 10 items
- Use `?page=N` to navigate pages
- Response includes `meta` object with pagination details

---

## Timestamps

All timestamps are in UTC ISO 8601 format:
- Example: `2026-03-15T10:00:00Z`

---

## API Version

Current API Version: **1.0**

Last Updated: March 15, 2026
