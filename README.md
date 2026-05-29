# 🌻 QuizBloom — Laravel Dynamic Quiz System

A production-style, extensible Quiz Management System built with Laravel. Supports multiple question types, media uploads, guest-based attempts, progressive question display, leaderboard, and clean evaluation logic using the Strategy Pattern.

---

## Tech Stack

| Layer      | Technology                                  |
|------------|---------------------------------------------|
| Backend    | Laravel 11 (PHP 8.2+)                       |
| Database   | MySQL                                       |
| Frontend   | Blade + TailwindCSS (CDN) + Alpine.js       |
| Storage    | Laravel Local Storage (public disk)         |
| Deployment | Render.com                                  |

---

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (optional)

---

## Local Installation

### 1. Clone the repository
```bash
git clone https://github.com/your-username/quiz-system.git
cd quiz-system
```

### 2. Install dependencies
```bash
composer install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database in .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run migrations
```bash
mysql -u root -p -e "CREATE DATABASE quiz_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

### 6. Link storage
```bash
php artisan storage:link
```

### 7. Serve the app
```bash
php artisan serve
```
Visit: http://localhost:8000

---

## Deploying to Render.com

### Step 1 — Create a MySQL database
Use PlanetScale, Aiven, or Railway for a hosted MySQL instance. Copy the host, port, database name, username, and password.

### Step 2 — Create a Web Service on Render
- Connect your GitHub repo
- Runtime: **Docker** (Dockerfile included)
- Build Command:
```bash
composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan storage:link
```
- Start Command:
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

### Step 3 — Set Environment Variables on Render
```
APP_NAME=QuizBloom
APP_ENV=production
APP_KEY=<run: php artisan key:generate --show>
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
DB_CONNECTION=mysql
DB_HOST=<your mysql host>
DB_PORT=3306
DB_DATABASE=quiz_system
DB_USERNAME=<username>
DB_PASSWORD=<password>
```

### Step 4 — Deploy
Push to your connected branch — Render auto-deploys on every push.

> **Storage Note:** Render has an ephemeral filesystem. For persistent media uploads, mount a Render Disk at `/var/www/html/storage/app/public` or migrate to Cloudflare R2.

---

## Features

- Create/edit/delete quizzes with title, description, publish toggle
- 5 question types: Binary, Single Choice, Multiple Choice, Number Input, Text Input
- HTML-supported question content
- Image upload for questions and answer options
- YouTube video embed per question
- Guest name entry before attempting
- One-question-at-a-time progressive attempt flow
- Strategy Pattern evaluation engine — fully extensible
- Score, percentage, pass/fail on results page
- Per-quiz leaderboard with gold/silver/bronze podium
- Fully responsive — mobile, tablet, desktop
- Sunflower SVG aesthetic — yellow + violet color scheme

---

## Project Structure

```
app/
├── Contracts/QuestionEvaluatorInterface.php
├── Evaluators/{Binary,SingleChoice,MultipleChoice,NumberInput,TextInput}Evaluator.php
├── Services/QuizEvaluationService.php
├── Services/MediaUploadService.php
├── Models/{Quiz,Question,Option,Attempt,Answer}.php
└── Http/Controllers/{Quiz,Attempt,Answer,Leaderboard}Controller.php

resources/views/
├── layouts/app.blade.php
├── components/{sunflower,quiz-card}.blade.php
├── quizzes/{index,create,edit,show}.blade.php
├── attempts/{guest-form,show,result}.blade.php
└── leaderboard/index.blade.php
```

See `ARCHITECTURE.md` for full design decisions.
