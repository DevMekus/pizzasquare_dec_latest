# Pizza Square API Documentation

## Base URL
```
https://pizzasquare.ng/api/v1/
```

## Table of Contents
- [Authentication](#authentication)
- [Categories](#categories)
- [Products](#products)
- [Extras](#extras)
- [Deals](#deals)
- [Cities](#cities)
- [Coupons](#coupons)
- [Orders](#orders)
- [Geocoding](#geocoding)
- [Payments](#payments)

---

## Authentication

### Register User
Creates a new user account.

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "fullname": "John Doe",
  "email_address": "john@example.com",
  "phone": "08012345678",
  "user_password": "securePassword123",
  "address": "123 Main Street", // Optional
  "city": "Enugu", // Optional
  "city_state": "Enugu", // Optional
  "profileImage": "<file>" // Optional
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "userid": "unique_user_id"
  }
}
```

---

### Login
Authenticate user and receive JWT token.

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "login_cred": "john@example.com", // Email or phone
  "user_password": "securePassword123"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

---

### Logout
End user session.

**Endpoint:** `POST /auth/logout`

**Request Body:**
```json
{
  "userid": "unique_user_id"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "User logged out"
}
```

---

### Recover Account
Initiate password reset process.

**Endpoint:** `POST /auth/recover`

**Request Body:**
```json
{
  "email_address": "john@example.com"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "A reset link has been sent to your registered email."
}
```

---

### Reset Password
Reset password using token.

**Endpoint:** `POST /auth/reset`

**Request Body:**
```json
{
  "token": "reset_token_from_email",
  "new_password": "newSecurePassword123"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Password has been reset successfully."
}
```

---

## Categories

### Get All Categories
Retrieve all product categories.

**Endpoint:** `GET /categories`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Categories fetched successfully",
  "data": [
    {
      "id": 1,
      "name": "Pizza",
      "slug": "pizza",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  ]
}
```

---

### Get Category by ID
Retrieve a specific category.

**Endpoint:** `GET /categories/{id}`

**Parameters:**
- `id` (integer) - Category ID

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Category fetched successfully",
  "data": {
    "id": 1,
    "name": "Pizza",
    "slug": "pizza"
  }
}
```

---

## Products

### Get All Products
Retrieve all products with category information.

**Endpoint:** `GET /products`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "category": "Pizza",
      "name": "Mega Beef Pizza",
      "sku": "UNIQUE_SKU_123",
      "description": "Delicious beef pizza",
      "image": "https://pizzasquare.ng/public/UPLOADS/products/image.jpg",
      "is_active": 1
    }
  ]
}
```

---

### Get Product by ID
Retrieve a specific product.

**Endpoint:** `GET /products/{id}`

**Parameters:**
- `id` (integer|string) - Product ID, name, or SKU

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Product retrieved",
  "data": {
    "id": 1,
    "name": "Mega Beef Pizza",
    "sku": "UNIQUE_SKU_123",
    "category": "Pizza",
    "size_id": 2,
    "size_price": 4500.00
  }
}
```

---

### Get Product Full Details
Retrieve product with all sizes.

**Endpoint:** `GET /products/full/{id}`

**Parameters:**
- `id` (integer) - Product ID

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Product retrieved",
  "data": {
    "product": {
      "id": 1,
      "name": "Mega Beef Pizza",
      "description": "Delicious beef pizza"
    },
    "sizes": [
      {
        "id": 1,
        "size_name": "Small",
        "price": 3000.00
      },
      {
        "id": 2,
        "size_name": "Medium",
        "price": 4500.00
      }
    ]
  }
}
```

---

### Get Pizzas with Sizes
Retrieve all pizza products with their available sizes.

**Endpoint:** `GET /pizzas-with-sizes`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Pizza products with sizes loaded",
  "data": [
    {
      "id": 1,
      "name": "Mega Beef",
      "sku": "PIZZA_001",
      "description": "Premium beef pizza",
      "image": "https://pizzasquare.ng/public/UPLOADS/products/megabeef.jpg",
      "category_id": 1,
      "category": "Pizza",
      "is_active": 1,
      "sizes": [
        {
          "id": 1,
          "product_id": 1,
          "size_name": "Small",
          "price": 3000.00
        },
        {
          "id": 2,
          "product_id": 1,
          "size_name": "Medium",
          "price": 4500.00
        }
      ]
    }
  ]
}
```

---

## Extras

### Get All Extras
Retrieve all available toppings and extras.

**Endpoint:** `GET /extras`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Extras found",
  "data": [
    {
      "id": 1,
      "extras": "Extra Cheese",
      "price": 500.00
    },
    {
      "id": 2,
      "extras": "Pepperoni",
      "price": 700.00
    }
  ]
}
```

---

### Get Extra by ID
Retrieve a specific extra/topping.

**Endpoint:** `GET /extras/{id}`

**Parameters:**
- `id` (integer) - Extra ID

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Extra found",
  "data": {
    "id": 1,
    "extras": "Extra Cheese",
    "price": 500.00
  }
}
```

---

## Deals

### Get All Deals
Retrieve all active deals.

**Endpoint:** `GET /deals`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "deals found",
  "data": [
    {
      "id": 1,
      "deal_id": "DEAL_001",
      "title": "Weekend Special",
      "description": "Buy 2 Get 1 Free",
      "image": "https://pizzasquare.ng/public/UPLOADS/deals/weekend.jpg",
      "status": "active",
      "created_at": "2024-01-15"
    }
  ]
}
```

---

### Get Deal by ID
Retrieve a specific deal.

**Endpoint:** `GET /deals/{id}`

**Parameters:**
- `id` (integer|string) - Deal ID or deal_id

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "deal found",
  "data": {
    "id": 1,
    "deal_id": "DEAL_001",
    "title": "Weekend Special",
    "description": "Buy 2 Get 1 Free",
    "image": "https://pizzasquare.ng/public/UPLOADS/deals/weekend.jpg",
    "status": "active"
  }
}
```

---

## Cities

### Get All Cities
Retrieve all delivery cities with fees.

**Endpoint:** `GET /city`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "cities found",
  "data": [
    {
      "id": 1,
      "city": "Enugu",
      "delivery_price": 1000
    },
    {
      "id": 2,
      "city": "Port Harcourt",
      "delivery_price": 1500
    }
  ]
}
```

---

### Get City by ID
Retrieve a specific city.

**Endpoint:** `GET /city/{id}`

**Parameters:**
- `id` (integer|string) - City ID or name

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "city found",
  "data": {
    "id": 1,
    "city": "Enugu",
    "delivery_price": 1000
  }
}
```

---

## Coupons

### Get All Coupons
Retrieve all available coupons.

**Endpoint:** `GET /coupon`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "coupons found",
  "data": [
    {
      "id": 1,
      "coupon": "SAVE10",
      "discount": 0.10
    }
  ]
}
```

**Note:** Discount is stored as decimal (0.10 = 10%)

---

### Get Coupon by ID
Retrieve a specific coupon.

**Endpoint:** `GET /coupon/{id}`

**Parameters:**
- `id` (integer|string) - Coupon ID or code

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "coupon found",
  "data": {
    "id": 1,
    "coupon": "SAVE10",
    "discount": 0.10
  }
}
```

---

## Orders

### Create Order
Create a new order.

**Endpoint:** `POST /orders/create`

**Request Body:**
```json
{
  "order_id": "ORD_20240115_001",
  "userid": "user_123", // Optional for walk-in
  "customer_name": "John Doe",
  "customer_phone": "08012345678",
  "email_address": "john@example.com",
  "customer_type": "online", // or "walk_in" or "mobile_app"
  "order_note": "Extra napkins please",
  "delivery_type": "delivery", // or "pickup"
  "delivery_address": "123 Main Street",
  "city": "Enugu",
  "total_amount": 15000.00,
  "attendant": "Staff_01",
  "cart": [
    {
      "id": 1,
      "size_id": 2,
      "price": 4500.00,
      "qty": 2,
      "barbecueSauce": "yes",// or NULL
      "toppings": [
        {
          "extras": "Extra Cheese",
          "price": 500
        }
      ]
    }
  ],
  "payment": {
    "total_paid": 15000.00,
    "payment_type": "single", // or "split" for POS
    "cash": 15000.00,
    "card": 0,
    "transfer": 0,
    "online": 0, //online for websites or mobile_app
    "item_amount": 14000.00, //total_paid minus the delivery_fee
    "delivery_fee": 1000.00
  }
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "order_id": 123
  }
}
```

---

### Get Order by ID
Retrieve order details.

**Endpoint:** `GET /orders/{id}`

**Parameters:**
- `id` (string) - Order ID or user ID

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Order found",
  "data": {
    "id": 1,
    "order_id": "ORD_20240115_001",
    "customer_name": "John Doe",
    "customer_phone": "08012345678",
    "customer_email": "john@example.com",
    "delivery": "delivery",
    "delivery_address": "123 Main Street, Enugu",
    "status": "pending",
    "total": 15000.00,
    "payment_type": "single",
    "total_paid": 15000.00,
    "delivery_fee": 1000.00,
    "item_amount": 14000.00,
    "items": [
      {
        "product_id": 1,
        "product_name": "Mega Beef Pizza",
        "size_id": 2,
        "unit_price": 4500.00,
        "qty": 2,
        "subtotal": 9000.00,
        "barbecue_sauce": "yes",
        "toppings": [
          {
            "topping": "Extra Cheese",
            "unit_price": 500,
            "qty": 2,
            "subtotal": 1000
          }
        ]
      }
    ]
  }
}
```

---

### Get VAT
Retrieve VAT information.

**Endpoint:** `GET /vat`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "vat found",
  "data": [
    {
      "id": 1,
      "vat": 7.5
    }
  ]
}
```

---

## Geocoding

### Reverse Geocode
Convert coordinates to address and get delivery fee.

**Endpoint:** `POST /geocode`

**Request Body:**
```json
{
  "lat": "6.4541",
  "lon": "7.5492"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Location detected",
  "data": {
    "lat": "6.4541",
    "lon": "7.5492",
    "area": "Independence Layout",
    "delivery_fee": 1000,
    "raw": {
      "address": {
        "suburb": "Independence Layout",
        "city": "Enugu",
        "state": "Enugu State"
      }
    }
  }
}
```

---

## Payments

### Confirm Payment
Verify Paystack payment.

**Endpoint:** `POST /payment/confirm`

**Request Body:**
```json
{
  "reference": "paystack_reference_code"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Payment verified successfully"
}
```

---

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error message"
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Invalid login credentials"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 409 Conflict
```json
{
  "success": false,
  "message": "Resource already exists"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "An error has occurred"
}
```

---

## Notes

1. **Authentication:** Most endpoints require JWT token in Authorization header: `Bearer <token>`
2. **File Uploads:** Use `multipart/form-data` for endpoints accepting images
3. **Date Format:** All dates are in `Y-m-d H:i:s` format (e.g., `2024-01-15 10:30:00`)
4. **Currency:** All prices are in Nigerian Naira (NGN)
5. **Order Status:** Can be `pending`, `processing`, `ready`, `delivered`, or `cancelled`
6. **Customer Type:** Either `online` or `walk_in` or `mobile_app`
7. **Delivery Type:** Either `delivery` or `pickup`

---

## Support

For API support, contact: info@pizzasquare.ng