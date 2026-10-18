# PortfolioGen

PortfolioGen lets students and professionals build, preview, publish, and manage responsive portfolios.

## Features

- Six-step portfolio editor with project descriptions, technology stacks, GitHub/demo links, and screenshots.
- Three templates: Simple, Modern, and Creative.
- Live draft preview, browser-side writing suggestions, and a completeness/link quality checker.
- Publishable `/p/{slug}` pages, public portfolio listings, and downloadable QR codes.
- Profile photo and project screenshot preview, replacement, and removal. JPG, PNG, and WebP uploads are limited to 2 MB each.
- S3-compatible image storage for AWS S3, Cloudflare R2, or Supabase Storage.

The writing helper is deterministic and runs in the browser. It does not call an AI provider or send portfolio text to a third party.

## Local development

Requirements: PHP 8.3+, Composer, Node.js 22+, and the PDO SQLite extension.

```powershell
composer setup
npm run dev
```

`composer setup` creates `.env` from `.env.example`, creates the local SQLite file, generates an application key, runs migrations, and builds the front-end dependencies. Keep `.env` local; it is ignored by Git.

## Neon PostgreSQL

For a hosted database, set these values in the untracked `.env` file or deployment environment:

```dotenv
DB_CONNECTION=pgsql
DB_URL=postgresql://USER:PASSWORD@HOST/DATABASE?sslmode=require
DB_SSLMODE=require
```

Use the Neon pooled connection string for the application and never put credentials in source files. The app's migrations create the portfolio, user, session, cache, and queue tables.

## Cloud image storage

Configure an S3-compatible bucket and make it publicly readable (or provide its public custom-domain URL). Set:

```dotenv
FILESYSTEM_DISK=s3
PORTFOLIO_STORAGE_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_ENDPOINT=
AWS_URL=
AWS_USE_PATH_STYLE_ENDPOINT=true
```

- **AWS S3:** use the bucket region and leave `AWS_ENDPOINT` empty. `AWS_URL` can be omitted for standard S3 URLs.
- **Cloudflare R2:** set `AWS_ENDPOINT` to the account's R2 S3 endpoint, `AWS_DEFAULT_REGION=auto`, and `AWS_URL` to the bucket's public domain.
- **Supabase Storage:** use the bucket's S3 endpoint and S3 access keys, and set `AWS_URL` to the bucket's public object URL.

For local development, `PORTFOLIO_STORAGE_DISK=public` uses Laravel's public local disk.

## Deploy to Render

The repository includes a Docker-based [Render Blueprint](./render.yaml). Connect the repository to Render and set the secret environment variables in the Render dashboard:

- `APP_KEY`: generate locally with `php artisan key:generate --show`.
- `APP_URL`: the deployed HTTPS URL.
- `DB_URL`: Neon PostgreSQL connection string with SSL enabled.
- S3 credentials and endpoint/public URL for the chosen image provider.

The container builds Composer and Vite assets, runs database migrations at startup, serves Laravel's `public` directory on Render's assigned port, and uses cloud object storage for uploaded images. Render's local filesystem is ephemeral, so production images must use the configured S3-compatible bucket.

Run tests locally with:

```powershell
php artisan test
```
