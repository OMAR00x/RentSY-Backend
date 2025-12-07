# RentSY API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
استخدم Bearer Token في الـ Header:
```
Authorization: Bearer {token}
```

---

## 🔐 Auth Endpoints

### Register
```
POST /register
```
**Body:**
```json
{
  "first_name": "أحمد",
  "last_name": "محمد",
  "phone": "0912345678",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "renter", // or "owner"
  "birthdate": "1990-01-15",
  "avatar": "file",
  "id_front": "file",
  "id_back": "file"
}
```

### Login
```
POST /login
```
**Body:**
```json
{
  "phone": "0912345678",
  "password": "password123"
}
```

### Logout
```
POST /logout
```
**Auth Required:** Yes

---

## 👤 User Profile Endpoints

### Get Profile
```
GET /profile
```
**Auth Required:** Yes

### Update Profile
```
PUT /profile
```
**Body:**
```json
{
  "first_name": "أحمد",
  "last_name": "محمد",
  "password": "newpassword123",
  "password_confirmation": "newpassword123",
  "avatar": "file"
}
```
**Auth Required:** Yes

---

## 🏠 Apartments Endpoints

### Get All Apartments (with filters)
```
GET /apartments?city_id=1&area_id=2&min_price=100&max_price=500&rooms=2&amenities=1,2&search=keyword
```

### Get Apartment Details
```
GET /apartments/{id}
```

### Get My Apartments (Owner)
```
GET /my-apartments
```
**Auth Required:** Yes

### Create Apartment (Owner)
```
POST /apartments
```
**Body:**
```json
{
  "title": "شقة فاخرة",
  "description": "وصف الشقة",
  "address": "العنوان",
  "city_id": 1,
  "area_id": 2,
  "rooms": 3,
  "price": 250,
  "price_type": "daily", // daily, weekly, monthly
  "amenities": [1, 2, 3],
  "images": ["file1", "file2"]
}
```
**Auth Required:** Yes

### Update Apartment (Owner)
```
PUT /apartments/{id}
```
**Body:** (نفس Create)
**Auth Required:** Yes

### Delete Apartment (Owner)
```
DELETE /apartments/{id}
```
**Auth Required:** Yes

---

## ❤️ Favorites Endpoints

### Toggle Favorite
```
POST /apartments/{id}/favorite
```
**Auth Required:** Yes

### Get Favorites
```
GET /favorites
```
**Auth Required:** Yes

---

## 📅 Bookings Endpoints

### Create Booking
```
POST /bookings
```
**Body:**
```json
{
  "apartment_id": 1,
  "start_date": "2024-02-01",
  "end_date": "2024-02-05",
  "payment_method": "card", // cash or card
  "payment_card": "4111111111111111"
}
```
**Auth Required:** Yes

### Get My Bookings
```
GET /my-bookings?status=upcoming
```
**Query Params:** upcoming, past, cancelled
**Auth Required:** Yes

### Get Apartment Bookings (Owner)
```
GET /apartment-bookings?apartment_id=1
```
**Auth Required:** Yes

### Update Booking Status (Owner)
```
PUT /bookings/{id}/status
```
**Body:**
```json
{
  "status": "approved" // or "rejected"
}
```
**Auth Required:** Yes

### Reschedule Booking
```
PUT /bookings/{id}/reschedule
```
**Body:**
```json
{
  "start_date": "2024-02-10",
  "end_date": "2024-02-15"
}
```
**Auth Required:** Yes

### Cancel Booking
```
DELETE /bookings/{id}
```
**Auth Required:** Yes

---

## ⭐ Reviews Endpoints

### Create/Update Review
```
POST /reviews
```
**Body:**
```json
{
  "booking_id": 1,
  "rating": 5,
}
```
**Auth Required:** Yes

### Get Apartment Reviews
```
GET /apartments/{id}/reviews
```

---

## 🔍 Search History Endpoints

### Get Search History
```
GET /search-history
```
**Auth Required:** Yes

### Add to Search History
```
POST /search-history
```
**Body:**
```json
{
  "query": "شقة في دمشق"
}
```
**Auth Required:** Yes

### Delete Search Item
```
DELETE /search-history/{id}
```
**Auth Required:** Yes

### Clear All Search History
```
DELETE /search-history
```
**Auth Required:** Yes

---

## 💬 Messages Endpoints

### Get Conversations
```
GET /conversations
```
**Auth Required:** Yes

### Get Chat with User
```
GET /chat/{userId}
```
**Auth Required:** Yes

### Send Message
```
POST /messages
```
**Body:**
```json
{
  "to_user_id": 2,
  "body": "مرحبا، هل الشقة متاحة؟",
  "apartment_id": 1
}
```
**Auth Required:** Yes

---

## 🔔 Notifications Endpoints

### Get Notifications
```
GET /notifications
```
**Auth Required:** Yes

### Mark as Read
```
PUT /notifications/{id}/read
```
**Auth Required:** Yes

### Mark All as Read
```
PUT /notifications/read-all
```
**Auth Required:** Yes

### Get Unread Count
```
GET /notifications/unread-count
```
**Auth Required:** Yes

---

## 🏙️ Filter Data Endpoints

### Get Cities
```
GET /cities
```

### Get Areas by City
```
GET /cities/{id}/areas
```

### Get Amenities
```
GET /amenities
```

---

## Response Format

### Success Response
```json
{
  "status": "success",
  "statuscode": 200,
  "message": "رسالة النجاح",
  "data": {}
}
```

### Error Response
```json
{
  "status": "failure",
  "statuscode": 400,
  "message": "رسالة الخطأ"
}
```

---

## Status Codes
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict
- `422` - Validation Error
- `500` - Server Error
