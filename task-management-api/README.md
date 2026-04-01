# Task Management API

A Task Management RESTful API built with Laravel and MySQL.

## Features
- **Create tasks** with validations (unique title per date, valid priorities, future/today due date).
- **List tasks** sorted by priority (high → low) and then due date. Optional filtering by status.
- **Update task status** strictly progressing from `pending` → `in_progress` → `done`.
- **Delete tasks** (only when status is `done`).
- **Daily Task Report (Bonus)**: View summaries grouped by priority and status for a specific date.

---

## 🚀 Setup Instructions (Local)

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL Database

### Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd task-management-api
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   Copy the `env` file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup:**
   Create a local MySQL database named `task_management_api` (or configure a different name). In your `.env` file, ensure your MySQL credentials are correct:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management_api
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Run Migrations & Seeders:**
   Prepare the database schema and populate it with initial specific entries.
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   The API should now be accessible at `http://localhost:8000`.

---

## 🌐 Deployment Instructions (Render.com)

This project is configured for automated deployment on **Render.com** using Docker and the included `render.yaml` blueprint.

### 1. Push to GitHub/GitLab
Ensure all your changes (including `Dockerfile`, `render.yaml`, and `nginx/default.conf`) are committed and pushed to your remote repository.

### 2. Deploy using Blueprint
1.  Log in to your [Render Dashboard](https://dashboard.render.com).
2.  Click **New +** and select **Blueprint**.
3.  Connect your GitHub/GitLab repository.
4.  Render will automatically detect the `render.yaml` file and propose creating:
    -   A **PostgreSQL** database (`task-database`).
    -   A **Web Service** (`task-management-api`).
5.  Click **Apply**.

### 3. Final Configuration
Once the services are being created:
1.  Go to the **Web Service** settings.
2.  In the **Environment** tab, ensure you have an `APP_KEY`. You can generate one locally using `php artisan key:generate --show` and paste it into the `APP_KEY` variable on Render.
3.  Render will automatically run migrations and optimize the application using the `scripts/render-deploy.sh` script defined in the Dockerfile.

---

---

## 🛠️ Example API Requests

All routes are prefixed with `/api`.

### 1. Create a Task
**POST `/api/tasks`**
```json
{
    "title": "Complete Laravel Assignment",
    "due_date": "2026-04-01",
    "priority": "high"
}
```

### 2. List Tasks
**GET `/api/tasks`**
Optionally filter by status: `GET /api/tasks?status=pending`
```json
// Example Response
{
  "total": 1,
  "tasks": [
    {
      "id": 1,
      "title": "Complete Laravel Assignment",
      "due_date": "2026-04-01T00:00:00.000000Z",
      "priority": "high",
      "status": "pending",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

### 3. Update Task Status
**PATCH `/api/tasks/{id}/status`**
Note: State can only progress (`pending` -> `in_progress` -> `done`).
```json
{
    "status": "in_progress"
}
```

### 4. Delete a Task
**DELETE `/api/tasks/{id}`**
Only applicable to tasks marked as `done`.
```json
// Returns 200 OK
{
    "message": "Task deleted successfully."
}
```

### 5. Daily Task Report (Bonus)
**GET `/api/tasks/report?date=2026-04-01`**
```json
{
  "date": "2026-04-01",
  "summary": {
    "high": {
      "pending": 1,
      "in_progress": 0,
      "done": 0
    },
    "medium": {
      "pending": 0,
      "in_progress": 0,
      "done": 0
    },
    "low": {
      "pending": 0,
      "in_progress": 0,
      "done": 0
    }
  }
}
```
