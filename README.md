# Translation Management Service

A Laravel-based application designed for efficient translation management.

## 🚀 Setup Instructions

Follow the steps below to get the project up and running on your local machine:

### 1. Clone the Repository
```bash
git clone https://github.com/ar207/translation-management-service.git
cd translation-management-service
```

### 2. Install Composer Dependencies
```bash
composer install
```

### 3. Configure Environment File
Copy the example environment file:
```bash
cp .env.example .env
```
Or manually create a `.env` file and paste the contents from `.env.example`.

### 4. Setup the Database
- Create a new database using **phpMyAdmin** or another preferred tool.
- Update your `.env` file with your database details:
```env
DB_DATABASE=your_database_name
```

### 5. Run Migrations and Seeders
Run the following to create database tables and insert sample data:
```bash
php artisan migrate:fresh --seed
```
Alternatively, run these commands separately:
```bash
php artisan migrate
php artisan db:seed
```

### 6. Generate Swagger API Documentation
```bash
php artisan l5-swagger:generate
```
Access the documentation at:  
`http://127.0.0.1:8000/api/documentation`

### 7. Run Tests
> ⚠️ To avoid unique constraint violations during testing, run migrations fresh before executing tests:
```bash
php artisan migrate:fresh
php artisan test
```

---

## ✅ Ready to Go!

Your development environment is all set! Visit  
`http://127.0.0.1:8000`  
to start using the Translation Management Service.
