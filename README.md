# Zync-ERP

Probably the best ERP in the world?

---

## ZYNC ERP Application

The application lives under the `/zync-erp/` directory. The repository root remains documentation.

### Prerequisites

- PHP 8.3 or 8.4
- [Composer](https://getcomposer.org/)
- Nginx with PHP 8.4-FPM
- MariaDB

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

| Variable      | Default            | Description                        |
|---------------|--------------------|------------------------------------|
| `APP_NAME`    | `ZYNC ERP`         | Application display name           |
| `APP_ENV`     | `production`       | Environment (`production`/`local`) |
| `APP_DEBUG`   | `false`            | Enable debug output                |
| `APP_URL`     | `https://...`      | Public URL of the application      |
| `DB_HOST`     | `localhost`        | Database host                      |
| `DB_PORT`     | `3306`             | Database port                      |
| `DB_NAME`     | —                  | Database name                      |
| `DB_USER`     | —                  | Database username                  |
| `DB_PASS`     | —                  | Database password                  |

### Deployment

The server runs **Ubuntu 24.04 LTS** with **Nginx + PHP 8.4-FPM + MariaDB 10.11**.

See [`zync-erp/docs/SERVER_SETUP.md`](zync-erp/docs/SERVER_SETUP.md) for full server setup instructions (in Swedish), including Nginx configuration, package installation, and deploy steps.

The Nginx server block config is at [`zync-erp/nginx/zync-erp.conf`](zync-erp/nginx/zync-erp.conf).

### Nginx Configuration

Set the Nginx `root` to `zync-erp/public/`. The included config at `nginx/zync-erp.conf` routes all requests through `public/index.php` via `try_files`.

```nginx
sudo cp zync-erp/nginx/zync-erp.conf /etc/nginx/sites-available/zync-erp
sudo ln -s /etc/nginx/sites-available/zync-erp /etc/nginx/sites-enabled/zync-erp
sudo nginx -t && sudo systemctl reload nginx
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
├── docs/
│   └── SERVER_SETUP.md     # Server setup instructions (Swedish)
├── nginx/
│   └── zync-erp.conf       # Nginx server block config
├── public/
│   ├── .htaccess           # Apache rewrite rules (unused on this server — Nginx)
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

