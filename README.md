# Vending Machine API - Symfony

A RESTful API built with Symfony framework for managing a vending machine system. This API provides basic CRUD operations and purchase functionality.

## Features

- ✅ Basic GET and POST operations
- ✅ RESTful API endpoints
- ✅ Docker support for easy deployment
- ✅ JSON responses
- ✅ Health check endpoint

## Requirements

- Docker Desktop
- Docker Compose

## Quick Start

### 1. Install dependencies

```bash
composer install
```

### 2. Start with Docker

```bash
docker-compose up -d
```

The API will be available at: `http://localhost:8000`

### 3. Stop Docker containers

```bash
docker-compose down
```


## API Endpoints

### Health Check

**GET** `/api/health`

Returns the API status.

```json
{
  "timestamp": 1234567890,
  "service": "Vending Machine API"
}
```

### Insert money

**POST** `/api/money/insert`

Inserts a coin into the vending machine. Valid coins are: 0.05, 0.10, 0.25, 1.00.

Request example:
```json
{
  "coin": 1
}
```

Response example:
```json
{
  "coin_inserted": 1,
  "current_balance": 1.25
}
```

### Return money

**POST** `/api/money/return`

Returns all coins inserted by the user without making a purchase.

Response example:
```json
{
    "coins_returned": [
        1
    ]
}
```

### Purchase product

**POST** `/api/purchase`

Purchases a product from the vending machine. Requires sufficient balance and stock availability. Returns change if applicable.

Request example:
```json
{
    "product": "WATER"
}
```

Response example:
```json
{
    "product_name": "WATER",
    "change_returned": [
        0.25,
        0.1
    ]
}
```

### Service (Technician)

**POST** `/api/service`

Allows technicians to set product stock and replenish change coins.

Request example:
```json
{
  "items": [
    {
      "code": "WATER",
      "count": 10
    },
    {
      "code": "JUICE",
      "count": 8
    },
    {
      "code": "SODA",
      "count": 5
    }
  ],
  "change": [
    { "value": 0.05, "count": 20 },
    { "value": 0.10, "count": 15 },
    { "value": 0.25, "count": 10 }
  ]
}
```

Response example:
```json
{
    "items_updated": [
        {
            "code": "WATER",
            "count": 10
        },
        {
            "code": "JUICE",
            "count": 8
        },
        {
            "code": "SODA",
            "count": 5
        }
    ],
    "change_updated": [
        {
            "value": 0.05,
            "count": 20
        },
        {
            "value": 0.1,
            "count": 15
        },
        {
            "value": 0.25,
            "count": 10
        }
    ]
}
```