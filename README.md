# Infra Monitoring

A Laravel application for **provincial infrastructure monitoring**—tracking projects from procurement through obligation, implementation, and payment. Built for transparency across the project lifecycle (Provincial Budget Office workflow).

- **Public site:** marketing landing page at `/`
- **Admin panel:** Filament at `/admin` (login required)

## Features

### Project lifecycle

Each **project** ties together related records across the full process:

| Stage | Resource |
|-------|----------|
| Project master | Projects |
| Pre-procurement | Pre-Procurement |
| Purchase request | Purchase Requests |
| Technical working group | TWG |
| Procurement / PMO control | Procurement Control, PR Control |
| Award & contract | Procurement |
| Obligation | Obligation Requests |
| Implementation progress | Implementations |
| Disbursement | Payments |

### Admin capabilities

- **Dashboards** — stats and chart views
- **Project monitoring** — consolidated view of appropriation, allotment, contract, payments, and implementation per project
- **Activity logs** — global log plus per-project timeline with filters (date range, record type, action, user)
- **PDF export** — project reports via DomPDF
- **Roles & permissions** — [Filament Shield](https://github.com/bezansalleh/filament-shield) (`super_admin` and role-based access)
- **Offices & users** — organizational structure and account management

### Landing page

Public homepage with an Airbnb-inspired design system (see `DESIGN.md`), dark mode toggle, and sections for features, team, and contact.

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.2+, Laravel 12 |
| Admin UI | Filament 3, Livewire 3 |
| Frontend | Vite 7, Tailwind CSS 4 |
| Auth & RBAC | Filament Shield |
| Auditing | Spatie Activity Log, noxoua/filament-activity-log |
| PDF | barryvdh/laravel-dompdf |
| Tests | Pest 3, PHPUnit 11 |
| Code style | Laravel Pint |

## Requirements

- PHP 8.2 or higher (8.3 recommended)
- Composer 2
- Node.js 18+ and npm
- MySQL 8+ (default) or another database supported by Laravel

## Installation

### 1. Clone and install dependencies

```bash
git clone <repository-url> hotkopiv1
cd hotkopiv1

composer install
npm install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for your database and `APP_URL`:

```env
APP_NAME="Infra Monitoring"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotkopiv1
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database

```bash
php artisan migrate
php artisan db:seed
```

The default seeder creates an admin user:

| Field | Value |
|-------|-------|
| Email | `admin@example.com` |
| Password | `password` |

Change these credentials after first login.

### 4. Filament Shield (roles)

Generate permissions and assign a super admin role as needed:

```bash
php artisan shield:generate --all
php artisan shield:super-admin
```

Follow the prompts to attach the super admin role to your user.

### 5. Frontend assets

```bash
npm run build
```

For local development with hot reload:

```bash
npm run dev
```

### 6. Run the application

**Option A — all services (recommended for dev):**

```bash
composer run dev
```

Starts the HTTP server, queue worker, log tail (Pail), and Vite.

**Option B — minimal:**

```bash
php artisan serve
```

Visit:

- Landing: `http://localhost:8000`
- Admin: `http://localhost:8000/admin`

## Development

### Tests

```bash
php artisan test

# Single file
php artisan test tests/Feature/ExampleTest.php

# Filter by name
php artisan test --filter=testName
```

### Code formatting

```bash
vendor/bin/pint
vendor/bin/pint --dirty
```

### Laravel Boost (Cursor MCP)

This project includes [Laravel Boost](https://github.com/laravel/boost) for AI-assisted development. MCP config lives in `.cursor/mcp.json`:

```json
{
  "mcpServers": {
    "laravel-boost": {
      "type": "stdio",
      "command": "php",
      "args": ["${workspaceFolder}/artisan", "boost:mcp"],
      "envFile": ".env"
    }
  }
}
```

Enable **laravel-boost** in Cursor → Settings → MCP after install.

## Project structure (high level)

```
app/
├── Filament/
│   ├── Resources/     # CRUD resources (Project, Payment, etc.)
│   ├── Pages/         # Dashboards, activity log
│   ├── Loggers/       # Activity log field definitions
│   └── Widgets/
├── Http/Controllers/
├── Models/
└── Providers/Filament/AdminPanelProvider.php

resources/
├── views/
│   ├── partials/      # Landing page sections
│   └── filament/      # Custom Filament / project pages
└── css/app.css        # Tailwind + design tokens

routes/web.php         # Landing + PDF routes
```

## Key routes

| Route | Description |
|-------|-------------|
| `GET /` | Public landing page |
| `GET /admin` | Filament admin (auth) |
| `GET /admin/projects/{record}/monitoring` | Project monitoring board |
| `GET /admin/projects/{record}/activities` | Per-project activity log |
| `GET /admin/projects/{project}/pdf` | Project PDF (auth) |

## Design

UI tokens and layout guidance for the landing page are documented in [`DESIGN.md`](DESIGN.md) (Rausch primary `#ff385c`, Inter typography, light/dark canvas).

Admin styling follows Filament defaults with a pink primary palette.

## License

This application is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
