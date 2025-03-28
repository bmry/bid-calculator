# Bid Calculation Tool

A full-stack web application for calculating the total cost of a vehicle bid at auction.

This tool uses a **Symfony (PHP 8.2)** backend and a **Vue 3** frontend, containerized using **Docker**. It follows **Clean Architecture**, **Domain-Driven Design (DDD)** principles, and is fully testable and extensible.

---

## Features

- Calculate real-time bid totals including:
    - Basic Buyer Fee (min/max capped)
    - Special Seller Fee
    - Association Tiered Fees
    - Fixed Storage Fee
- Supports **Common** and **Luxury** vehicle types
- Fees are **data-driven**, configurable via YAML
- API versioned under `/api/v1`
- Auto-updating frontend as inputs change
- Fully dockerized: no local PHP needed

---

## 🚀 How to Start the Application

> Make sure you have **Docker** and **Docker Compose** installed on your system.

### 1. Clone the Repository

```bash
git clone git@github.com:bmry/bid-calculator.git
cd bid-calculator
```

### 2. Start the App

To build and start all services (frontend, backend, nginx), run:

```bash
docker-compose up -d --build
```

This will:
- Install PHP and Node dependencies
- Build the Vue frontend
- Set up PHP-FPM with Symfony
- Serve everything using Nginx

### 3. Access the App

- **Frontend:** [http://localhost:8080](http://localhost:8080)
- **API (v1):** [http://localhost:8081/api/v1/bid/calculate](http://localhost:8081/api/v1/bid/calculate)

---

## 🧠 Architecture Overview

The application is designed using **Clean Architecture** and **Domain-Driven Design (DDD)** principles. Code is separated into distinct layers:

```
backend/
│
├── Domain/
│   ├── Model/         → Entities, Value Objects
│   ├── Exception/     → Domain-specific exceptions
│   ├── Repository/    → Repository interfaces (ports)
│   └── Service/       → Domain services / policies
│
├── Application/
│   ├── UseCase/       → Orchestrates domain logic for scenarios
│   └── DTO/           → Transfers data between layers
│
├── Infrastructure/
│   ├── Controller/    → HTTP input/output (Symfony controllers)
│   └── Repository/    → Implementation of data access (e.g. YAML-based)
│
├── config/
│   ├── fee_policies.yaml → Dynamic fee rules
│   └── routes.yaml    → Versioned route definitions
```

## 🧪 Running Tests

### Backend Unit and Integration Tests

```bash
docker-compose exec php-fpm bash
php vendor/bin/phpunit
```

## Developer Notes

- **API versioning:** Routes are prefixed with `/api/v1`
- **No hardcoded URLs:** Frontend uses `VITE_API_URL` from `.env`
- **Configurable fee rules:** Modify `backend/config/fee_policies.yaml` for different calculation logic
- **Logs & debugging:** Use `docker-compose logs -f` to monitor containers

---
