# Wooden Dad Design ERP Deployment Checklist

## Production `.env` Example

```dotenv
APP_NAME="Wooden Dad Design"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://woodendaddesign.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wooden_dad_sales_engine
DB_USERNAME=wooden_dad_user
DB_PASSWORD=change_this_password

LOG_CHANNEL=stack
QUEUE_CONNECTION=database

LINE_CHANNEL_ACCESS_TOKEN=
LINE_USER_ID=
LINE_GROUP_ID=
```

## Deploy Commands

```bash
cd /var/www/wooden-dad-sales-engine

git pull

composer install --no-dev --optimize-autoloader

npm install
npm run build

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Permissions

```bash
chmod -R ug+rwx storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Storage Link

```bash
php artisan storage:link
```

If the symbolic link already exists, confirm it points to `storage/app/public`.

## Queue / Supervisor Note

If queues are enabled for notifications or webhook processing:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Use Supervisor or systemd to keep the queue worker running in production.

## Final Checks

```bash
php artisan about
php artisan route:list
```

Open these URLs after deploy:

- `https://woodendaddesign.com/`
- `https://woodendaddesign.com/lead`
- `https://woodendaddesign.com/admin/dashboard`
- `https://woodendaddesign.com/admin/settings/line`
- `https://woodendaddesign.com/admin/settings/facebook`
- `https://woodendaddesign.com/webhooks/facebook`

## Important

- Do not upload or commit local `.env`.
- Set `APP_URL=https://woodendaddesign.com` before testing LINE or Facebook webhooks.
- Tokens must only be stored in Admin Settings or `.env`; never expose them on public pages.
