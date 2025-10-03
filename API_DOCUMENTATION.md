# On the Night CMS - API Documentation

**Version**: 1.0  
**Base URL**: `http://localhost:8000/api/v1`  
**Authentication**: Laravel Sanctum (for protected endpoints)

## 🚀 Quick Start

### 1. Basic API Access
All endpoints are publicly accessible except where noted.

### 2. Authentication Flow
For authenticated features (creating reviews, favorites), mobile apps will need to:
1. **Register/Login** → Get Sanctum token
2. **Include token** in Authorization header: `Bearer {token}`
3. **Use token** for protected endpoints

---

## 📍 Venues API

### Get Venues (with advanced filtering)
```http
GET /venues
```

**Query Parameters:**
- `search` (string) - Search venues by name, description, address
- `location_id` (int) - Filter by location ID
- `location_name` (string) - Filter by location name
- `city` (string) - Filter by city
- `status` (string) - Filter by status: `featured`, `sponsored`, `new`, `none`
- `music_genres` (string|array) - Filter by music genres (comma-separated)
- `drink_types` (string|array) - Filter by drink types (comma-separated)
- `featured_only` (boolean) - Show only featured venues
- `new_only` (boolean) - Show only new venues
- `sponsored_only` (boolean) - Show only sponsored venues
- `user_lat` (decimal) - User latitude for distance calculation
- `user_lon` (decimal) - User longitude for distance calculation
- `radius` (decimal) - Search radius in km (default: 10)
- `per_page` (int) - Results per page (default: 20)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Heebie Jeebies",
      "description": "Liverpool's premier alternative club...",
      "address": "86-88 Seel St",
      "city": "Liverpool",
      "latitude": 53.4084,
      "longitude": -2.9916,
      "full_address": "86-88 Seel St, Liverpool, Merseyside, L1 4BH, UK",
      "opening_hours": {
        "Thursday": "10:00 PM - 3:00 AM",
        "Friday": "10:00 PM - 4:00 AM"
      },
      "status": "featured",
      "is_active": true,
      "images": [
        {
          "url": "http://localhost:8000/storage/venues/image1.jpg",
          "thumbnail": "http://localhost:8000/storage/conversions/image1-thumb.jpg",
          "preview": "http://localhost:8000/storage/conversions/image1-preview.jpg",
          "collection": "venue_images"
        }
      ],
      "location": {
        "id": 1,
        "name": "Liverpool",
        "city": "Liverpool"
      },
      "drink_types": [
        {"id": 1, "name": "Draught Beer", "category": "Beer"}
      ],
      "music_genres": [
        {"id": 1, "name": "Rock", "category": "Rock"}
      ],
      "reviews": {
        "count": 23,
        "average_rating": 4.5,
        "approved_reviews": [...]
      },
      "distance": 2.3,
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45
  }
}
```

### Get Featured Venues
```http
GET /venues/featured?limit=10
```

### Get Nearby Venues
```http
GET /venues/nearby?latitude=53.4084&longitude=-2.9916&radius=5
```

### Get Venues by Music Genre
```http
GET /venues/by-music-genre/Rock
```

### Get Venues by Drink Type
```http
GET /venues/by-drink-type/Draught%20Beer
```

### Get Single Venue
```http
GET /venues/{id}
```

---

## 🎟️ Deals API

### Get Current Deals
```http
GET /deals
```

**Query Parameters:**
- `venue_id` (int) - Filter by venue
- `venue_name` (string) - Filter by venue name
- `location_id` (int) - Filter by location
- `deal_type` (string) - Filter by deal type
- `featured_only` (boolean) - Show only featured deals
- `search` (string) - Search deals
- `per_page` (int) - Results per page

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "2-4-1 Cocktails",
      "description": "Buy one cocktail, get one free!",
      "deal_type": "2-4-1",
      "start_date": "2024-01-01T18:00:00.000000Z",
      "end_date": "2024-01-08T23:00:00.000000Z",
      "is_active": true,
      "is_current": true,
      "time_remaining": "6 days left",
      "concert": {
        "id": 1,
        "name": "Heebie Jeebies",
        "venue_name": "Heebie Jeebies"
      },
      "images": [...]
    }
  ]
}
```

### Get Featured Deals
```http
GET /deals/current?limit=5
```

### Get Expiring Deals
```http
GET /deals/expiring/24  // Deals expiring in 24 hours
```

---

## 🎉 Events API

### Get Events
```http
GET /events
```

**Query Parameters:**
- `venue_id` (int) - Filter by venue
- `event_type` (string) - Filter by event type
- `featured_only` (boolean) - Show only featured events
- `show_all` (boolean) - Include past events
- `per_page` (int) - Results per page

**Response:**
```json le:
{
  "data": [
    {
      "id": 1,
      "title": "Friday Night Live Music",
      "description": "Amazing live music performance...",
      "event_type": "Live Music",
      "start_date": "2024-01-05T21:00:00.000000Z",
      "end_date": "2024-01-06T02:00:00.000000Z",
      "ticket_price": 15.00,
      "is_active": true,
      "sold_out": false,
      "is_upcoming": true,
      "time_until_start": "2 days",
      "venue": {
        "id": 1,
        "name": "Heebie Jeebies"
      }
    }
  ]
}
```

### Get Upcoming Events
```http
GET /events/upcoming?limit=10
```

### Get Today's Events
```http
GET /events/today
```

---

## ⭐ Reviews API

### Get Reviews by Venue
```http
GET /reviews/venue/{venue_id}
```

**Query Parameters:**
- `rating` (int) - Filter by rating (1-5)
- `search` (string) - Search review text
- `per_page` (int) - Results per page

### Create Review (Authenticated)
```http
POST /reviews
Authorization: Bearer {token}
```

**Body:**
```json
{
  "venue_id": 1,
  "review_text": "Amazing atmosphere and music!",
  "rating": 5,
  "reviewer_name": "John Doe",
  "reviewer_email": "john@example.com"
}
```

---

## 📊 Supporting Data API

### Get Locations
```http
GET /locations
```

### Get Drink Types
```http
GET /drink-types
GET /drink-types/popular
```

### Get Music Genres
```http
GET /music-genres
GET /music-genres/popular
```

---

## 🔐 Authentication Endpoints

### Login/Register (Mobile App)
```http
POST /login
POST /register
```

**Login Body:**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Register Body:**
```json
{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "access_token": "1|abcdef123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

---

## 🎯 Mobile App Integration Guide

### 1. Venue Search Flow
```
1. Get user location
2. Call GET /venues/nearby with lat/lng
3. Display venues with distance
4. Allow filtering by music/drinks
```

### 2. Venue Detail Flow
```
1. Call GET /venues/{id} with full details
2. Show reviews, deals, events
3. Allow reviews creation (authenticated)
```

### 3. Browse Content Flow
```
1. GET /deals/current for current deals
2. GET /events/upcoming for upcoming events
3. Filter by location/music/drinks
```

### 4. User Reviews Flow
```
1. Authenticate user (login/register)
2. POST /reviews to create review
3. Show user's reviews in profile
```

---

## 🚀 Advanced Features

### Geolocation Search
All venue endpoints support geolocation:
- Include `user_lat` and `user_lon` parameters
- Venues will include `distance` field
- Results sorted by proximity when location provided

### Search & Filtering
- Full-text search across names and descriptions
- Multi-criteria filtering (location + music + drinks)
- Pagination with consistent meta format

### Image Handling
- Automatic thumbnail/preview generation
- Consistent image URLs across all endpoints
- Multiple collections supported (main images, galleries)

---

## 🔒 Security & Performance

### Rate Limiting
- 60 requests per minute per IP for public endpoints
- 1000 requests per minute for authenticated endpoints

### Data Optimization
- Includes only necessary relationships
- Pagination on all list endpoints
- Compact JSON responses

### Error Handling
Standard HTTP status codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## 📱 Example Mobile Implementation

### React Native/Fetch Example
```javascript
// Get nearby venues
const fetchNearbyVenues = async (lat, lng, radius = 10) => {
  const response = await fetch(
    `http://localhost:8000/api/v1/venues/nearby?latitude=${lat}&longitude=${lng}&radius=${radius}`
  );
  return await response.json();
};

// Create review (authenticated)
const createReview = async (token, venueId, reviewText, rating) => {
  const response = await fetch('http://localhost:8000/api/v1/reviews', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      venue_id: venueId,
      review_text: reviewText,
      rating: rating,
    }),
  });
  return await response.json();
};
```

The API is now ready for mobile app integration! 🎉
