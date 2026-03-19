# GR Tech Technical Test 1

## Setup

### Requirements

1. PHP >= v8.2
2. NodeJS >= V22
3. Docker

### Run postgreSQL in docker

```bash
docker compose up -d
```

### Download dependencies

```bash
composer install 
npm install
```

### Configuring laravel

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

## Running in development

### Start a dev server

```bash
composer run dev
```

### Login to dashboard

[http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)

Credentials:

1. Email: <admin@grtech.com> | Password : password
2. Email: <user@grtech.com> | Password : password
