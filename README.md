# Digitālā Grāmatu Bibliotēka (Digital Book Library)

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2-purple)

A specialized web platform for self-improvement book organization, reading, and sharing. The platform offers books in categories such as health, finance, psychology, and other personal development areas.

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js (v16+)
- MySQL database
- Ghostscript (10.05.1)

## Installation

1. Clone the repository:
   ```
   git clone [repository-url]
   cd [project-folder]
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Create and configure environment file:
   ```
   cp .env.example .env
   ```

5. Update the database configuration in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Import the database structure:
   ```
   # Option 1: Import the SQL file directly
   # Import datubaze.sql into your MySQL database
   
   # Option 2: Use Laravel migrations
   php artisan migrate
   ```

7. Generate application key:
   ```
   php artisan key:generate
   ```


9. Start the development server:
   ```
   php artisan serve
   ```

10. Visit `http://localhost:8000` in your browser

## Technologies Used

### Backend
- **PHP** with **Laravel** framework
- **Livewire** (for dynamic interfaces)
- **MySQL** (database management)

### Frontend
- **HTML, CSS, JavaScript**
- **Alpine.js** (minimalist JavaScript framework)
- **PDF.js** (for online book reading)
- **Ghostscript** (for PDF to image conversion)

## Project Structure


