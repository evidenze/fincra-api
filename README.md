# Wallet System

A basic wallet system that allows for crediting and debiting operations. The system is secure and robust against financial frauds and technical issues like race conditions and deadlocks.

## Features

- User registration and login with JWT authentication via Laravel Passport
- Wallet functionalities: credit and debit
- Transaction history retrieval
- Admin functionalities: credit and debit users' wallets
- Security measures against race conditions, deadlocks, and ensuring transactional integrity

## Requirements

- PHP >= 8.2
- Composer
- Laravel 11.x
- SQLite (for development)

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/evidenze/fincra-api.git
cd fincra-api
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

Copy the `.env.example` file to `.env` and update the environment variables as needed.

```bash
cp .env.example .env
```

Update the `.env` file to configure the database and Passport settings:

```ini
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 4. Create SQLite Database File

```bash
touch database/database.sqlite
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Create a Personal Access Client

```bash
php artisan passport:client --personal
```

Update the `.env` file with the Passport client ID and secret provided by the above command:

```ini
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 7. Clear Configuration Cache

```bash
php artisan config:cache
php artisan config:clear
```

### 8. Seed the database with test data:

```bash
php artisan db:seed
```

### 9. Run the Application

```bash
php artisan serve
```


## API Endpoints

### Authentication

- `POST /api/register` - Register a new user
- `POST /api/login` - Login and obtain a JWT token

### User Wallet Operations

- `POST /api/credit` - Credit the user's wallet
- `POST /api/debit` - Debit the user's wallet
- `GET /api/transactions` - Retrieve the user's transaction history

### Admin Operations

- `POST /api/admin/credit` - Admin credit to a user's wallet
- `POST /api/admin/debit` - Admin debit from a user's wallet
- `POST /api/admin/transactions` - Admin get all transactions

## Testing the API

Use a tool like Postman or cURL to interact with the API. Example cURL commands:

```sh
# Register a new user
curl -X POST http://localhost:8000/api/register -d "username=essien" -d "email=evidenzeekanem@gmail.com" -d "password=123secret"

# Login to get a token
curl -X POST http://localhost:8000/api/login -d "email=evidenzeekanem@gmail.com" -d "password=123secret"

# Use the token to access protected routes
TOKEN="your-access-token-here"
curl -H "Authorization: Bearer $TOKEN" http://localhost:8000/api/transactions
```

## Technical Report

For detailed information about the design and implementation decisions, please refer to the [Technical Report](TECHNICAL_REPORT.md).
