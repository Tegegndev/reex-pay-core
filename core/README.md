# Production deployment

For the production cPanel deployment, use `https://reexpaylimited.com` as the canonical HTTPS domain. Upload the project with the `core` directory as the application root, copy `.env.example` to `.env`, and set the production database values. Do not include live API secrets in the archive; configure them in the admin payment-gateway screen after deployment.

If using phpMyAdmin, import `DB/digikash.sql` first, then import `DB/marzpay_uganda_only_setup.sql`. The second file disables other currencies and gateways and creates the MarzPay UGX records for the SQL-dump schema. It leaves MarzPay disabled until credentials are entered in the admin panel.

Run these commands from the application root after uploading:

```sh
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Because the default queue driver is `database`, add a cPanel cron job that runs every minute:

```sh
php /home/CPANEL_USER/reexpaylimited.com/core/artisan queue:work --stop-when-empty --tries=3
```

Replace `CPANEL_USER` with the cPanel account username. This processes queued payment notifications and other background jobs.

The Uganda-only deployment migration keeps UGX as the only active currency and wallet currency, disables all other gateways and payment methods, and enables MarzPay deposit and withdrawal methods only after valid MarzPay credentials and a webhook signing secret are present. Register `https://reexpaylimited.com/ipn/marzpay` in the MarzPay dashboard; it must be reachable over HTTPS.
[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F6273b8ef-f6ff-4bcb-a701-c740923a3c1f%3Fdate%3D1%26label%3D1%26commit%3D1&style=for-the-badge)](https://forge.laravel.com/servers/869708/sites/2569642)
