# Real-time Stock Price Aggregator

This repository contains a full-stack application built with Laravel for the backend and React for the frontend. The project uses Docker (via Laravel Sail) for containerized development.

## Table of Contents
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Setup Instructions](#setup-instructions)
- [Frontend Setup](#frontend-setup)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [Documentation](#documentation)
- [Troubleshooting](#troubleshooting)

---

## Features

1. **Backend**:
    - Laravel 10/11 backend with Sail for Docker support.
    - API endpoints for managing stock data.
    - Caching implementation for stock data.

2. **Frontend**:
    - React-based UI to display stock data.
    - Visual indicators for stock performance.

3. **Database**:
    - MySQL database containerized with Docker.

4. **Development Environment**:
    - Fully containerized using Laravel Sail for consistent development.

---

## Prerequisites

Before setting up the project, ensure you have the following installed on your machine:

- **Docker**: [Install Docker](https://docs.docker.com/get-docker/)
- **Node.js**: [Install Node.js](https://nodejs.org/) (for frontend setup)
- **Composer**: [Install Composer](https://getcomposer.org/)
- **Git**: [Install Git](https://git-scm.com/)

---

## Setup Instructions

### Backend Setup (Laravel)

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/PavlosP30/stock-price-aggregator.git
   cd stock-price-aggregator
   ```

2. **Install Laravel Sail**:
   Run the following command to install Sail:
   ```bash
   composer require laravel/sail --dev
   ```

3. **Build and Start Docker Containers**:
   Start the application using Laravel Sail:
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Run Migrations**:
   Create the database tables using:
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

5. **Seed the Database**:
   Seed the `stocks` table:
   ```bash
   ./vendor/bin/sail artisan db:seed --class=StockSeeder
   ```

6. **Set Up Environment Variables**:
   Copy the example environment file and update as needed (including the ALPHA_VANTAGE_API_KEY):
   ```bash
   cp .env.example .env
   ```

7. **Install Dependencies**:
   Install Laravel dependencies:
   ```bash
   ./vendor/bin/sail composer install
   ```

---

### Frontend Setup (React)

1. **Navigate to the Frontend Directory**:
   ```bash
   cd frontend
   ```

2. **Install Node Modules**:
   ```bash
   npm install
   ```

3. **Start the Development Server**:
   ```bash
   npm start
   ```
   The React app will start on `http://localhost:3000`.

---

## Running the Application

### Using Laravel Sail
Start all containers using Laravel Sail:
```bash
./vendor/bin/sail up -d
```

Access the application:

- **API**: `http://localhost`
- **Frontend**: `http://localhost:3000`

---

## Testing

### Run Backend Tests
1. **Prepare the Test Environment**:
   Ensure the `testing` database is properly configured in `.env.testing`.

2. **Run the Tests**:
   ```bash
   ./vendor/bin/sail artisan test
   ```

---

## Documentation

### API Endpoints
1. **Get Latest Stock Prices**:
   ```
   GET /api/reports/stocks
   ```
    - Fetches all time-series data for all stocks.

2. **Get Latest Stock Prices**:
   ```
   GET /reports/stocks/{symbol}
   ```
    - Fetches all time-series data for the specified stock symbol.

---

### Caching Implementation
- **Duration**: Stock data is cached for 1 minute.

---

### Cron Job Setup

To ensure the `stocks:fetch` command runs automatically every minute, set up a cron job on your server:

1. Open the crontab editor:
   ```bash
   crontab -e

2. Add the following line to the crontab file:
   ```bash
   * * * * * /path/to/php /path/to/your-project/artisan schedule:run >> /dev/null 2>&1

3. Save and exit the editor. Verify the cron job is active:
    ```bash
   crontab -l
   
---

### Docker Services
- **PHP and Laravel**: `http://localhost`
- **MySQL**: Managed via Docker and accessible only internally.

---

## Troubleshooting

1. **Container Issues**:
    - Restart all containers:
      ```bash
      ./vendor/bin/sail down
      ./vendor/bin/sail up -d
      ```

2. **Database Connection Issues**:
    - Ensure the `.env` file has the correct database configuration.

3. **Frontend Not Loading**:
    - Check that the React development server is running:
      ```bash
      npm start
      ```

4. **API Not Responding**:
    - Check that Laravel Sail is running:
      ```bash
      ./vendor/bin/sail up -d
      ```

---
