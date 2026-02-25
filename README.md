## Pizza Square API Documentation
#Introduction
The Pizza Square API allows developers to integrate product listings, categories, deals, order processing, user authentication, and geolocation services into websites and mobile applications.
All requests must be made over HTTPS.

## Base URL
https://pizzasquare.ng/api/v1/

## Headers
All protected endpoints require a valid JWT token.

Authorization: Bearer <token>
Origin: https://pizzasquare.ng/
Content-Type: application/json
Accept: application/json

## Authentication Overview
Login returns a JWT token.
Include the token in the Authorization header for secured routes.
Tokens must not be shared or exposed publicly.
Some routes allow guest/walk-in operations.

## Standard Response Format
All successful API responses follow this structure:
{
  "success": true,
  "message": "Descriptive message",
  "data": {...}
}

#Error responses:
{
  "success": false,
  "message": "Error message",
  "errors": {...} // optional
}

## API Versioning
Current version: v1
Future versions will follow /api/v2/, /api/v3/, etc.

## Table of Contents
- [Authentication](#authentication)
- [Categories](#categories)
- [Products](#products)
- [Extras](#extras)
- [News Update](#news_update)
- [Cities](#cities)
- [Coupons](#coupons)
- [Orders](#orders)
- [Geocoding](#geocoding)
- [Payments](#payments)
- [Profile](#users)
- [Promotion](#promotions)
- [BestSellers](#BestSellers)
- [ContactUs](#ContactUs)
- [ProductIngredients](#ProductIngredients)

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
  "address": "123 Main Street", 
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

### Get Product Full Details
Retrieve product information with all sizes.

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

## News Update

### Get All News updates
Retrieve all active news update.

**Endpoint:** `GET /news_updates`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "news update found",
  "data": [
    {
      "id": 1,
      "news_id": "001",
      "title": "Weekend Special",
      "description": "Buy 2 Get 1 Free",
      "image": "https://pizzasquare.ng/public/UPLOADS/news_updates/weekend.jpg",
      "status": "active", // or inactive
      "created_at": "2024-01-15"
    }
  ]
}
```

---

### Get News Update by ID
Retrieve a specific deal.

**Endpoint:** `GET /news_updates/{id}`

**Parameters:**
- `id` (integer|string) - News ID or news_id

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "news update found",
  "data": {
    "id": 1,
    "news_id": "001",
    "title": "Weekend Special",
    "description": "Buy 2 Get 1 Free",
    "image": "https://pizzasquare.ng/public/UPLOADS/news_updates/weekend.jpg",
    "status": "active" // or inactive
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
  "order_id": "ps12345", 
  "userid": "user_123", // Optional for walk-in
  "customer_name": "John Doe",
  "customer_phone": "08012345678",
  "email_address": "john@example.com",
  "customer_type": "online", // or "walk_in" or "mobile_app" Note: Use mobile_app for mobile app orders
  "order_note": "Extra napkins please", // or null
  "delivery_type": "delivery", // or "pickup"
  "delivery_address": "123 Main Street",
  "city": "Abakpa Nike", // or null for pickup order
  "total_amount": 15000.00,
  "attendant": "Staff_01", // or null for website and mobile orders
  "cart": [
    {
      "id": 1,
      "size_id": 2,
      "size":"M", // Product size label
      "price": 4500.00,
      "qty": 2,
      "barbecueSauce": "yes",// or NULL
      "toppings": [
        {
          "extras": "Extra Cheese",
          "price": 500
        }
      ],
      "removed_ingredients": [  // or [] for empty item
         {
          "ingredient_name": "",
        }
      ],
      "type":"" // Regular(normal order), custom(customized order) or the name of the promo for promo order 

    }
  ],
  "payment": {
    "total_paid": 15000.00,
    "payment_type": "single", // or "split" for POS
    "cash": 15000.00,
    "card": 0, // 0 for website and mobile order
    "transfer": 0, // 0 for website and mobile order
    "online": 0, //total_paid minus the delivery_fee for websites or mobile_app order
    "item_amount": 14000.00, //total_paid minus the delivery_fee
    "delivery_fee": 1000.00, 
    "vat": 100,  //The amount calculated from vat
    "discount": 100,  //The amount calculated from the discount if coupon applies
     "vat": 0, // The calculated vat for this order
    "discount": 0, // the promo code discounted amount if valid 
    
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

### Get Order by USERID
Retrieve order details.

**Endpoint:** `GET /orders/users/{userid}`
Authorization: Bearer <token>

**Parameters:**
- `userid` (string) - userid

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Order found",
  "data": [
    {
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
  ]
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

### User profile
Get customer profile information.

**Endpoint:** `GET /users/{id}`
**Parameters:**
- `id` (integer|string) - Userid

**Authorisation:**
- Bearer token

**Response:** `200 OK`
```json
{
  "success": true,
  "status": 200,
  "message": "User information",
  "data": [
    {
      "id": "1",
      "userid": "user_id",
      "fullname": "John Doe",
      "email_address": "you@email.com",
      "phone": "1234567890",
      "user_password": encrypted_password,
      "address": "User_address",
      "city": "User_city", // or null
      "city_state": "enugu",
      "avatar": "http://localhost/pizzasquare_latest/public/UPLOADS/avatar/692dada62075d4.37102194.png",
      "status": "active",
      "role_id": "user_role_id",
      "created_at": "2025-11-24",
      "reset_token": "",
      "reset_token_expiration": "",
      "role": "role_name" //user or admin or cashier
    }
  ]
}
```
---

## Promotions

### Get All Promotions
Retrieve all available promotional activities

**Endpoint:** `GET /promotions`

**Response:** `200 OK`
```json
{
  "success": true,
  "status": 200,
  "message": "promotion found",
  "data": [
    {
      "id": "4",
      "code": "xtra_thursday_offer",
      "title": "Xtra Thursday Offer",
      "banner": "http://localhost/pizzasq/public/UPLOADS/promotions/696e02a9516741.95376765.png",
      "description": "Order any Xtra Large (XL) Pizza and get a Medium (M) Pizza Free!! Added automatically to your order",
      "status": "active",
      "active_day": "thursday",
      "created_at": "2026-01-22"
    }
  ]
}
```

---

### Get Promotion by ID
Retrieve a specific promotion.

**Endpoint:** `GET /promotions/{id}` 

**Parameters:**
- `id`  - Promo ID or CODE

**Response:** `200 OK`
```json
{
  "success": true,
  "status": 200,
  "message": "promotion found",
  "data": [
    {
      "id": "4",
      "code": "xtra_thursday_offer",
      "title": "Xtra Thursday Offer",
      "banner": "http://localhost/pizzasq/public/UPLOADS/promotions/696e02a9516741.95376765.png",
      "description": "Order any Xtra Large (XL) Pizza and get a Medium (M) Pizza Free!! Added automatically to your order",
      "status": "active",
      "active_day": "thursday",
      "created_at": "2026-01-22"
    }
  ]
}
```

## ProductIngredients
---
### Get Ingredients by Product ID
Retrieve a specific promotion.

**Endpoint:** `GET /products/ingredients/{id}` 

**Parameters:**
- `id`  - Product ID

**Response:** `200 OK`
```json
{
  "success": true,
  "status": 200,
  "message": "Ingredients for product retrieved successfully",
  "data": [
    {
      "id": "1",
      "product_id": "1",
      "ingredient_id": "1",
      "ingredient_name": "Barbecue sauce",
      "ingredient_category_id": "1",
      "product_name": "Mega Beef Deluxe"
    },
   
  ]
}
```
## Error Responses
{
  "success": false,
  "status": 404,
  "message": "No ingredients found for this product",
  "data": null
}


## BestSellers contact

### Get All Best Selling Products
Retrieve a list of best selling products

**Endpoint:** `GET /analytics/top-dishes`

**Response:** `200 OK`
```json
{
  "success": true,
  "status": 200,
  "message": "Top dishes retrieved successfully",
  "data": [
    {
      "product_id": "5",
      "name": "Italian Xtra Special",
      "image_url": "http://localhost/pizzasq/public/UPLOADS/products/69355f0b1e58b9.74587175.png",
      "total_qty": 207
    },
    
  ]
}
```

## ContactUs 

### Contact us
Send a contact us message to the server

**Endpoint:** `POST /contact`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "subject_of_the_message",
  "message": "", // message
 
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "status": 200,
  "message": "Message Sent",
  "data": []
}
```

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
5. **Order Status:** Can be `pending`, `preparing`,`delivered`, or `cancelled`
6. **Customer Type:** Either `online` or `walk_in` or `mobile_app`
7. **Delivery Type:** Either `delivery` or `pickup`

---

## Support

For API support, contact: info@pizzasquare.ng