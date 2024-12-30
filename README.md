# Mini E-Wallet System

A Laravel-based application for managing user wallets with features such as deposits, withdrawals, transaction history,
and role-based access for admins and users.

---

## Features

- **Authentication**:
    - Session-based login/logout.
    - API authentication with Laravel Sanctum.
- **Role-Based Access**:
    - Admin: Manage users and wallets.
    - User: Perform wallet operations (deposit/withdraw).
- **Wallet Management**:
    - Track balance, deposits, and withdrawals.
    - AJAX-based transaction updates.
- **Admin REST API**:
    - Endpoint: `/api/admin/users` for user details and wallet balances.
- **Clean Code Structure**:
    - Follows best practices with migrations, seeders, and factories.

---

## Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd mini-e-wallet
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Set Up Environment Configuration

```bash
cp .env.example .env
```

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mini_e_wallet
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=database
SESSION_LIFETIME=120

```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed the Database

```bash
php artisan db:seed
```
