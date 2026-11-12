# API Request/Response Examples

## 1. Student Registration

### Request
**POST** `/api/tickets`

**Headers:**
```
Content-Type: multipart/form-data
Accept: application/json
```

**Body (Form Data):**
```
name: John Doe
email: john@example.com
phone: 0300-1234567
cnic: 12345-1234567-1
event_id: 1
transaction_screenshot: [file]
```

### Response (Success - 201)
```json
{
    "success": true,
    "message": "Ticket registration submitted successfully. Your ticket is pending verification.",
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "pending",
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

### Response (Error - 422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "cnic": [
            "A ticket with this CNIC already exists."
        ],
        "transaction_screenshot": [
            "Transaction screenshot must be a JPEG or PNG file."
        ]
    }
}
```

---

## 2. Check Ticket Status

### Request
**GET** `/api/tickets/TKT-ABC123XYZ/status`

**Headers:**
```
Accept: application/json
```

### Response (Success - 200)
```json
{
    "success": true,
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "verified",
        "name": "John Doe",
        "email": "john@example.com",
        "event": {
            "name": "Film & TV Society Festival",
            "venue": "IAC Amphitheatre"
        },
        "verified_at": "2024-01-15 10:30:00",
        "checked_in_at": null
    }
}
```

### Response (Not Found - 404)
```json
{
    "success": false,
    "message": "Ticket not found."
}
```

---

## 3. QR Code Verification (Event Day)

### Request
**GET** `/api/verify-ticket/550e8400-e29b-41d4-a716-446655440000`

**Headers:**
```
Accept: application/json
```

### Response - Verified (200)
```json
{
    "success": true,
    "status": "VERIFIED",
    "message": "Ticket verified successfully.",
    "data": {
        "id": 1,
        "ticket_id": "TKT-ABC123XYZ",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "0300-1234567",
        "event": {
            "id": 1,
            "name": "Film & TV Society Festival",
            "venue": "IAC Amphitheatre",
            "start_date": "2025-12-10 10:00:00"
        },
        "checked_in_at": "2024-01-15 14:30:00"
    }
}
```

### Response - Already Used (409)
```json
{
    "success": false,
    "status": "ALREADY_USED",
    "message": "This ticket has already been used.",
    "data": null
}
```

### Response - Invalid Token (404)
```json
{
    "success": false,
    "status": "INVALID",
    "message": "Invalid ticket token.",
    "data": null
}
```

### Response - Not Verified (404)
```json
{
    "success": false,
    "status": "INVALID",
    "message": "Ticket is not verified yet.",
    "data": null
}
```

---

## 4. Get Ticket Details (Without Check-in)

### Request
**GET** `/api/ticket/550e8400-e29b-41d4-a716-446655440000`

**Headers:**
```
Accept: application/json
```

### Response (200)
```json
{
    "success": true,
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "0300-1234567",
        "status": "verified",
        "event": {
            "id": 1,
            "name": "Film & TV Society Festival",
            "venue": "IAC Amphitheatre",
            "start_date": "2025-12-10 10:00:00"
        },
        "verified_at": "2024-01-15 10:30:00",
        "checked_in_at": null
    }
}
```

---

## 5. Admin - Dashboard Statistics

### Request
**GET** `/api/admin/dashboard`

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

### Response (200)
```json
{
    "success": true,
    "data": {
        "total_tickets": 150,
        "pending_tickets": 25,
        "verified_tickets": 100,
        "checked_in_tickets": 25,
        "total_events": 1
    }
}
```

### Response - Unauthorized (403)
```json
{
    "success": false,
    "message": "Unauthorized. Admin access required."
}
```

---

## 6. Admin - Get Pending Tickets

### Request
**GET** `/api/admin/tickets/pending?event_id=1`

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

### Response (200)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "ticket_id": "TKT-ABC123XYZ",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "0300-1234567",
            "cnic": "12345-1234567-1",
            "event": {
                "id": 1,
                "name": "Film & TV Society Festival"
            },
            "transaction_screenshot": "http://example.com/storage/tickets/screenshot.jpg",
            "created_at": "2024-01-15 09:00:00"
        },
        {
            "id": 2,
            "ticket_id": "TKT-DEF456UVW",
            "name": "Jane Smith",
            "email": "jane@example.com",
            "phone": "0300-7654321",
            "cnic": "54321-7654321-2",
            "event": {
                "id": 1,
                "name": "Film & TV Society Festival"
            },
            "transaction_screenshot": "http://example.com/storage/tickets/screenshot2.jpg",
            "created_at": "2024-01-15 09:15:00"
        }
    ]
}
```

---

## 7. Admin - Verify Ticket

### Request
**POST** `/api/admin/tickets/1/verify`

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
    "ticket_id": 1
}
```

### Response (200)
```json
{
    "success": true,
    "message": "Ticket verified successfully. Email sent to student.",
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "verified",
        "verification_token": "550e8400-e29b-41d4-a716-446655440000",
        "verified_at": "2024-01-15 10:30:00"
    }
}
```

### Response - Not Pending (400)
```json
{
    "success": false,
    "message": "Ticket is not in pending status."
}
```

---

## 8. Admin - Reject Ticket

### Request
**POST** `/api/admin/tickets/1/reject`

**Headers:**
```
Authorization: Bearer {sanctum_token}
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
    "reason": "Invalid transaction screenshot. Please upload a clear image of your payment receipt."
}
```

### Response (200)
```json
{
    "success": true,
    "message": "Ticket rejected successfully.",
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "rejected"
    }
}
```

---

## 9. Admin - Get All Tickets

### Request
**GET** `/api/admin/tickets?status=verified&event_id=1&page=1`

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

### Response (200)
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "event_id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "phone": "0300-1234567",
                "cnic": "12345-1234567-1",
                "ticket_id": "TKT-ABC123XYZ",
                "status": "verified",
                "verified_at": "2024-01-15 10:30:00",
                "checked_in_at": null,
                "event": {
                    "id": 1,
                    "name": "Film & TV Society Festival",
                    "venue": "IAC Amphitheatre"
                }
            }
        ],
        "first_page_url": "http://example.com/api/admin/tickets?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "http://example.com/api/admin/tickets?page=5",
        "links": [...],
        "next_page_url": "http://example.com/api/admin/tickets?page=2",
        "path": "http://example.com/api/admin/tickets",
        "per_page": 20,
        "prev_page_url": null,
        "to": 20,
        "total": 100
    }
}
```

---

## 10. Admin - Get Events

### Request
**GET** `/api/admin/events`

**Headers:**
```
Authorization: Bearer {sanctum_token}
Accept: application/json
```

### Response (200)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Film & TV Society Festival",
            "description": "Annual Film & TV Society Festival featuring screenings, workshops, and Qawali Night.",
            "start_date": "2025-12-10 10:00:00",
            "end_date": "2025-12-11 23:00:00",
            "venue": "IAC Amphitheatre",
            "price": "2000.00",
            "max_tickets": null,
            "is_active": true,
            "created_at": "2024-01-01 00:00:00",
            "updated_at": "2024-01-01 00:00:00"
        }
    ]
}
```

---

## Authentication

### Get Sanctum Token (Login)

**POST** `/api/login` (if using Sanctum)

**Body:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "token": "1|abcdef1234567890..."
}
```

Use this token in subsequent requests:
```
Authorization: Bearer 1|abcdef1234567890...
```

---

## Rate Limiting

The QR verification endpoint (`/api/verify-ticket/{token}`) is rate-limited to:
- **60 requests per minute** per IP address

If exceeded, you'll receive:
```json
{
    "message": "Too Many Attempts."
}
```

---

## Error Responses

All errors follow this format:

```json
{
    "success": false,
    "message": "Error message here",
    "error": "Detailed error (only in debug mode)"
}
```

Common HTTP Status Codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict (e.g., ticket already used)
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Server Error

