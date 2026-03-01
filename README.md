# Zync-ERP

Probably the best ERP in the world?

---

## ZYNC ERP Application

The application lives under the `/zync-erp/` directory. The repository root remains documentation.

### Prerequisites

- PHP 8.3 or 8.4
- [Composer](https://getcomposer.org/)
- Apache with `mod_rewrite` enabled (or any compatible web server)
- MariaDB (for future database features)

### Local Setup

```bash
# 1. Navigate into the application directory
cd zync-erp

# 2. Install Composer dependencies
composer install

# 3. Copy the example environment file and configure it
cp .env.example .env
# Edit .env with your local settings (APP_DEBUG=true for development)

# 4. Point your web server document root to zync-erp/public/
```

### Environment Variables (`.env`)

| Variable         | Default            | Description                             |
|------------------|--------------------|-----------------------------------------|
| `APP_NAME`       | `ZYNC ERP`         | Application display name                |
| `APP_ENV`        | `production`       | Environment (`production`/`local`)      |
| `APP_DEBUG`      | `false`            | Enable debug output                     |
| `APP_URL`        | `https://...`      | Public URL of the application           |
| `DB_HOST`        | `localhost`        | Database host                           |
| `DB_PORT`        | `3306`             | Database port                           |
| `DB_NAME`        | —                  | Database name                           |
| `DB_USER`        | —                  | Database username                       |
| `DB_PASS`        | —                  | Database password                       |
| `ADMIN_EMAIL`    | —                  | E-mail for the seeded admin user        |
| `ADMIN_PASSWORD` | —                  | Plain-text password for the admin seed  |

### Database Setup

After configuring the `DB_*` and admin variables in `.env`, run the following from the `zync-erp/` directory:

```bash
# Run pending migrations (creates the migrations and users tables)
php bin/migrate.php

# Seed the initial admin user (reads ADMIN_EMAIL / ADMIN_PASSWORD from .env)
php bin/seed.php
```

Both commands are idempotent and safe to run multiple times.

### Apache Configuration

Set the `DocumentRoot` to the `zync-erp/public/` directory and ensure `mod_rewrite` is enabled. The included `public/.htaccess` routes all requests through `public/index.php`.

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/zync-erp/public

    <Directory /var/www/html/zync-erp/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Directory Structure

```
zync-erp/
├── app/
│   ├── Controllers/        # Application controllers
│   ├── Core/               # Framework core (App, Router, Request, Response, …)
│   └── Models/             # Data models (future)
├── config/
│   ├── app.php             # Application configuration
│   └── database.php        # Database configuration
├── database/
│   └── migrations/         # Database migrations (future)
├── lang/
│   └── en/                 # English language files
├── modules/                # ERP modules (future)
├── public/
│   ├── .htaccess           # Apache URL rewrite rules
│   └── index.php           # Application entry point
├── storage/
│   ├── cache/              # Cache files (git-ignored)
│   ├── logs/               # Application logs (git-ignored)
│   └── sessions/           # Session files (git-ignored)
├── views/
│   ├── layouts/
│   │   └── main.php        # Main HTML layout (Tailwind + Alpine)
│   └── home.php            # Home page view
├── .env.example            # Example environment file
├── .gitignore
└── composer.json
```

