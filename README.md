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


## API Endpoints

### Health Check

**GET** `/api/health`

Returns the API status.

```json
{
  "status": "ok",
  "timestamp": 1234567890,
  "service": "Vending Machine API"
}
```

### Insert money

**POST** `/api/money/insert`

Request example:
```json
{
  "coin": 1
}
```

Response example:
```json
{
  "success": true,
  "coin_inserted": 1,
  "current_balance": 1.25
}
```


## Stop containers

```bash
docker-compose down
```