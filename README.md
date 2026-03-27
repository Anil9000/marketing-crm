# Marketing CRM

A full-stack marketing automation and CRM platform built with Laravel 11, providing campaign management, contact segmentation, email analytics, A/B testing, and lead capture — all wrapped in a dark-themed Bootstrap 5 dashboard.

---

## Tech Stack

| Layer      | Technology                                              |
|------------|---------------------------------------------------------|
| Backend    | PHP 8.2+, Laravel 11, JWT Auth (tymon/jwt-auth)        |
| Frontend   | Blade, Bootstrap 5.3, Alpine.js, Chart.js 4            |
| Database   | SQLite 3 (default) — MySQL 8.0+ also supported          |
| Auth       | JWT (API) + Laravel session (web)                       |
| Storage    | Local disk (configurable for Azure Blob / S3)           |
| Queue      | Laravel Queue (database driver, configurable)           |
| Build      | npm + Vite (optional — CDN assets used by default)      |

---

## Features

- **Campaign Management** — Create, schedule, and send email, SMS, push notification, and social media campaigns
- **Contact Management** — Full contact CRUD with custom fields, status tracking, and CSV import/export
- **Audience Segmentation** — Dynamic segments with rule-based filter builder (field / operator / value)
- **Email Templates** — Reusable HTML templates with Handlebars-style variable substitution
- **A/B Testing** — Split-test subject lines and content; track winners by opens and clicks
- **Analytics Dashboard** — Visualise campaign performance, open rates, click rates, and conversions with Chart.js
- **Lead Forms** — Embeddable lead capture forms with custom field definitions; public submission endpoint
- **Email Tracking** — Open pixel tracking and click-through redirect tracking with token-based attribution
- **Role-based Access** — Three roles: `admin`, `marketing_manager`, `viewer`
- **Google OAuth** — Sign in with Google (configurable)
- **RESTful API** — Full JSON API with JWT auth, API Resources, and versioned routes (`/api/v1/`)
- **Dark Theme UI** — Professional dark sidebar layout with CSS variables for easy theming

---

## Prerequisites

- PHP 8.2 or higher with the `pdo_sqlite` extension (`php --version`)
- Composer 2.x (`composer --version`)
- Git
- Node.js 18+ and npm *(only required if modifying frontend assets — CDN assets used by default)*
- MySQL 8.0+ *(optional — SQLite is the default and requires no extra setup)*

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-org/marketing-crm.git
cd marketing-crm
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Copy the environment file

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Generate the JWT secret

```bash
php artisan jwt:secret
```

### 6. Configure the database

**Option A — SQLite (default, zero config):**

The `.env.example` already has SQLite configured. The database file is created automatically when you run migrations — no extra setup needed.

```env
DB_CONNECTION=sqlite
# DB_DATABASE defaults to database/database.sqlite
```

**Option B — MySQL:**

Edit `.env` and set your credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketing_crm
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then create the database:

```sql
CREATE DATABASE marketing_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Run migrations and seed the database

```bash
php artisan migrate --seed
```

This will create all tables and seed demo data including:
- 5 user accounts (admin + team members)
- 500+ contacts with realistic profiles
- Dynamic and static audience segments
- 10 campaigns with full analytics stats
- 5 pre-built email templates
- A/B test records

**Default login credentials:**

| Role              | Email                        | Password   |
|-------------------|------------------------------|------------|
| Admin             | `admin@marketingcrm.com`     | `password` |
| Marketing Manager | `sarah@marketingcrm.com`     | `password` |
| Viewer            | `mike@marketingcrm.com`      | `password` |

### 8. Ensure cache directory exists (SQLite / file cache)

```bash
mkdir -p storage/framework/cache/data
chmod -R 775 storage/framework/cache
```

### 9. Start the development server

```bash
php artisan serve
```

```bash
php artisan serve
```

The application is now available at [http://localhost:8000](http://localhost:8000).

---

## Environment Variables

| Variable                  | Description                                                    | Example                         |
|---------------------------|----------------------------------------------------------------|---------------------------------|
| `APP_NAME`                | Application name shown in UI                                   | `Marketing CRM`                 |
| `APP_ENV`                 | Environment: `local`, `production`                             | `local`                         |
| `APP_KEY`                 | Laravel encryption key (generated by `key:generate`)           | `base64:...`                    |
| `APP_URL`                 | Full application URL                                           | `http://localhost:8000`         |
| `DB_CONNECTION`           | Database driver                                                | `sqlite`                        |
| `DB_HOST`                 | Database host                                                  | `127.0.0.1`                     |
| `DB_PORT`                 | Database port                                                  | `3306`                          |
| `DB_DATABASE`             | Database name                                                  | `marketing_crm`                 |
| `DB_USERNAME`             | Database user                                                  | `root`                          |
| `DB_PASSWORD`             | Database password                                              |                                 |
| `JWT_SECRET`              | JWT signing secret (generated by `jwt:secret`)                 | `...`                           |
| `JWT_TTL`                 | JWT token time-to-live in minutes                              | `60`                            |
| `MAIL_MAILER`             | Mail driver: `smtp`, `ses`, `log`, `array`                     | `smtp`                          |
| `MAIL_HOST`               | SMTP host                                                      | `smtp.mailtrap.io`              |
| `MAIL_PORT`               | SMTP port                                                      | `587`                           |
| `MAIL_USERNAME`           | SMTP username                                                  |                                 |
| `MAIL_PASSWORD`           | SMTP password                                                  |                                 |
| `MAIL_FROM_ADDRESS`       | Default from address                                           | `hello@marketingcrm.com`        |
| `GOOGLE_CLIENT_ID`        | Google OAuth client ID (optional)                              |                                 |
| `GOOGLE_CLIENT_SECRET`    | Google OAuth client secret (optional)                          |                                 |
| `GOOGLE_REDIRECT_URI`     | Google OAuth redirect URI                                      | `http://localhost:8000/auth/google/callback` |
| `QUEUE_CONNECTION`        | Queue driver: `sync`, `database`, `redis`                      | `database`                      |
| `CACHE_STORE`             | Cache driver: `file`, `redis`, `database`                      | `file`                          |

---

## API Documentation

The API is versioned under `/api/v1/`. All protected routes require a `Bearer` JWT token in the `Authorization` header.

A complete Postman collection is provided in `postman_collection.json`. Import it into Postman and set the `base_url` and `token` collection variables.

### Authentication

| Method | Endpoint                  | Description            |
|--------|---------------------------|------------------------|
| POST   | `/api/v1/auth/register`   | Register a new user    |
| POST   | `/api/v1/auth/login`      | Login and get JWT      |
| POST   | `/api/v1/auth/refresh`    | Refresh JWT token      |
| POST   | `/api/v1/auth/logout`     | Invalidate JWT token   |
| GET    | `/api/v1/auth/me`         | Get authenticated user |

### Resources

| Resource          | Base Path                   | Actions                              |
|-------------------|-----------------------------|--------------------------------------|
| Campaigns         | `/api/v1/campaigns`         | index, store, show, update, destroy  |
| Campaign Stats    | `/api/v1/campaigns/{id}/stats` | show                              |
| Contacts          | `/api/v1/contacts`          | index, store, show, update, destroy  |
| Contact Import    | `/api/v1/contacts/import`   | store (multipart CSV)                |
| Segments          | `/api/v1/segments`          | index, store, show, update, destroy  |
| Segment Contacts  | `/api/v1/segments/{id}/contacts` | index                           |
| Segment Export    | `/api/v1/segments/{id}/export`   | get (CSV download)              |
| Email Templates   | `/api/v1/email-templates`   | index, store, show, update, destroy  |
| Analytics         | `/api/v1/analytics/overview` | show                                |
| Lead Forms        | `/api/v1/lead-forms`        | index, store, show, update, destroy  |
| Lead Submit       | `/api/v1/forms/{slug}/submit` | store (public, no auth)            |

### Tracking

| Method | Endpoint                         | Description                          |
|--------|----------------------------------|--------------------------------------|
| GET    | `/track/open/{token}`            | Track email open (returns 1x1 pixel) |
| GET    | `/track/click/{token}`           | Track click + redirect               |

---

## Database Schema

See `database/schema.sql` for the complete DDL with comments and ER diagram notes.

### Tables

| Table               | Description                                                   |
|---------------------|---------------------------------------------------------------|
| `users`             | Application users with role-based access                      |
| `segments`          | Audience segments with dynamic JSON filter rules              |
| `campaigns`         | Marketing campaigns (email, SMS, push, social)                |
| `campaign_stats`    | Aggregated stats per campaign (opens, clicks, conversions)    |
| `contacts`          | CRM contacts with custom fields and activity tracking         |
| `segment_contacts`  | Pivot table linking contacts to segments                      |
| `email_templates`   | Reusable HTML email templates                                 |
| `email_events`      | Individual email event log (sent, open, click, bounce)        |
| `lead_forms`        | Lead capture form definitions with field schemas              |
| `lead_submissions`  | Individual form submissions                                   |
| `ab_tests`          | A/B test variants and result tracking per campaign            |
| `sessions`          | Laravel session storage                                       |
| `password_reset_tokens` | Password reset token store                               |

### Key Relationships

```
users ──1:N──> campaigns
users ──1:N──> segments
users ──1:N──> contacts
users ──1:N──> email_templates
users ──1:N──> lead_forms
campaigns ──1:1──> campaign_stats
campaigns ──1:N──> email_events
campaigns ──1:N──> ab_tests
campaigns ──N:1──> segments (optional)
segments ──M:N──> contacts  (via segment_contacts)
lead_forms ──1:N──> lead_submissions
```

---

## Project Structure

```
marketing-crm/
├── app/
│   ├── Enums/                  # PHP 8.1 backed enums (CampaignStatus, ContactStatus, etc.)
│   ├── Http/
│   │   ├── Controllers/        # Thin controllers — delegate to Services
│   │   ├── Middleware/         # RoleMiddleware, JWT middleware
│   │   └── Requests/           # Form Request classes for validation
│   ├── Models/                 # Eloquent models with relationships
│   ├── Providers/
│   │   └── AppServiceProvider.php  # Repository bindings
│   ├── Repositories/
│   │   ├── Interfaces/         # Repository contracts
│   │   ├── CampaignRepository.php
│   │   └── ContactRepository.php
│   └── Services/               # Business logic layer
│       ├── CampaignService.php
│       ├── ContactService.php
│       └── ...
├── bootstrap/
│   └── app.php                 # Laravel 11 application bootstrap
├── database/
│   ├── migrations/             # Ordered schema migrations
│   ├── seeders/                # Demo data seeders
│   ├── factories/              # Model factories
│   └── schema.sql              # Full SQL DDL for reference
├── public/
│   ├── css/app.css             # Custom dark theme CSS
│   ├── js/app.js               # Charts, AJAX helpers, toast notifications
│   └── index.php               # Application entry point
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php   # Dark sidebar layout
│       │   └── auth.blade.php  # Centred auth layout
│       ├── auth/               # Login, register
│       ├── campaigns/          # Campaign CRUD + show + calendar
│       ├── contacts/           # Contact CRUD + import
│       ├── segments/           # Segment CRUD + show
│       ├── email-templates/    # Template CRUD
│       ├── analytics/          # Analytics dashboard
│       ├── lead-forms/         # Lead form CRUD
│       ├── dashboard/          # Main dashboard
│       └── components/         # Reusable Blade components
├── routes/
│   ├── web.php                 # Web (session) routes
│   ├── api.php                 # JWT API routes (v1)
│   └── console.php             # Artisan commands
├── postman_collection.json     # Full Postman API collection
├── .env.example                # Environment variable template
└── README.md
```

---

## Useful Artisan Commands

```bash
# Optimise for production
php artisan optimize
php artisan view:cache
php artisan config:cache
php artisan route:cache

# Clear all caches during development
php artisan optimize:clear

# Re-seed without dropping tables
php artisan db:seed

# Fresh migration + seed
php artisan migrate:fresh --seed

# Run queue worker
php artisan queue:work --queue=default,emails

# Run scheduled tasks (add this to crontab in production)
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit your changes following PSR-12 standards
4. Push to the branch and open a pull request

---

## License

MIT License. See [LICENSE](LICENSE) for details.
