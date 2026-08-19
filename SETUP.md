# EyeCare Studio — Fresh Windows 11 Setup Guide

Complete steps to run this Laravel project on a brand-new Windows 11 PC.

## 1. Install Required Tools

| Tool | Purpose | Download |
|---|---|---|
| **Laragon** (Full) | Bundles PHP 8.2, MySQL, Node.js | https://laragon.org/download.html |
| **Git** | Clone the repository | https://git-scm.com/downloads |

> After installing Laragon:
> - Open Laragon → Menu → **PHP → Version** → select **8.2.x** (make it active)
> - Menu → **Node.js → Version** → enable/select Node.js LTS

## 2. Clone the Repository

Open a terminal (Git Bash / PowerShell) and run:

```bash
cd C:\laragon\www
git clone https://github.com/pratik2883/eyecare.git
cd eyecare
```

## 3. Install Dependencies

```bash
composer install
npm install
```

## 4. Create the `.env` File

```bash
copy .env.example .env
php artisan key:generate
```

Then edit `.env` and set the database section:

```
APP_NAME="GEM OPTICIANS"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=optical_db
DB_USERNAME=root
DB_PASSWORD=
```

Create the database:
- Laragon → Menu → **Database → Open** (opens phpMyAdmin)
- Create a new database named `optical_db`

## 5. Load the Database (choose one)

### Option A — Full data (807 products, real store data)

```bash
mysql -u root optical_db < main_database/main_db_upload.sql
```

### Option B — Fresh install (migrations + sample seeders)

```bash
php artisan migrate
php artisan db:seed
```

## 6. Link Storage (required for uploaded images)

```bash
php artisan storage:link
php artisan migrate
```

> `storage:link` creates the public symlink so `/storage/banners`, `/storage/products`, and `/storage/menu` images are served correctly.

## 7. Run the Application

```bash
php artisan serve
```

| URL | Description |
|---|---|
| http://127.0.0.1:8000 | Storefront |
| http://127.0.0.1:8000/admin/login | Admin panel (credentials in `credential.md`) |

### Dev mode with hot reload (Vite + queue + logs)

```bash
composer run dev
```

## Troubleshooting

| Issue | Fix |
|---|---|
| `composer: not recognized` | Install Composer, or use Laragon terminal / restart PC |
| `php not found` | Make sure PHP 8.2 is active in Laragon |
| Port 8000 in use | `php artisan serve --port=8080` |
| Images `/storage/...` return 404 | Re-run `php artisan storage:link` |
| DB connection refused | Start MySQL in Laragon (Menu → Services) |
| Queue jobs not running | Run `php artisan queue:work` |

## Important Notes

- **Never commit `.env`** — it contains secrets. The repo only tracks `.env.example`.
- Uploaded images live in `storage/app/public/` (gitignored), so a fresh clone starts with empty upload folders.
- Admin login credentials are stored in `credential.md` at the project root.