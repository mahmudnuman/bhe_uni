# Mini CRM for BHE UNI

This is a MINI CRM application built with Vue.js and Laravel. The app allows users to manage leads, track conversions, and visualize counselor performance.

## Installation and Setup

### Prerequisites

- Node.js and npm installed ([Download Node.js](https://nodejs.org/))
- PHP 8.0+ installed ([Download PHP](https://www.php.net/downloads))
- Composer installed ([Get Composer](https://getcomposer.org/))
- MySQL or any compatible database installed

### Backend (Laravel) Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/mahmudnuman/bhe_uni
   cd your-repository
   ```

2. Install backend dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file and update configuration:
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database credentials.

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Run database migrations and seed data:
   ```bash
   php artisan migrate --seed
   ```

6. Start the Laravel server:
   ```bash
   php artisan serve
   ```



1. Navigate to the project  directory:


2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the Vue.js development server:
   ```bash
   npm run dev
   ```

The app should now be running at `http://localhost:8000` for the backend and `http://localhost:5173` for the frontend.

## API Documentation

You can find the API documentation and test endpoints using Postman:  
[Postman Collection](https://documenter.getpostman.com/view/8199471/2sAYXFhcUE)

## Usage

- Login with your credentials.
- Manage leads using the Kanban board.
- Drag and drop leads to update their status.
- View counselor statistics and performance.

## License

This project is licensed under the MIT License.

