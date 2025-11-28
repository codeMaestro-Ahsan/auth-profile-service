# 🚀 Deployment Guide - Auth Profile Service

## Pre-Deployment Checklist

### ✅ Code Verification
- [x] All PHP files have valid syntax
- [x] All routes properly named and configured
- [x] All controllers implemented with methods
- [x] All views created and styled
- [x] Database migrations ready
- [x] Models configured with relationships

### ✅ Configuration
- [x] Mail service configured (Mailtrap)
- [x] Database connection settings ready
- [x] Session configuration set
- [x] CSRF protection enabled
- [x] Authorization policies in place

### ✅ Security
- [x] Password hashing enabled (bcrypt)
- [x] Email verification with signed URLs
- [x] Password reset token validation
- [x] CSRF tokens on all forms
- [x] SQL injection prevention (Eloquent)
- [x] Session security configured

---

## Step-by-Step Deployment

### Phase 1: Local Setup (Development)

```bash
# 1. Clone repository
git clone <your-repo-url>
cd auth-profile-service

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
# Edit .env with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auth_profile_service
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Configure Mail (Mailtrap)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Auth Profile Service"

# 7. Start development server
php artisan serve
# Opens at http://localhost:8000
```

### Phase 2: Testing (Before Production)

```bash
# Run automated tests
php artisan test

# Verify all routes
php artisan route:list

# Check configuration
php artisan config:list | grep -E "(APP_|DB_|MAIL_)"

# Verify syntax
php -l app/Http/Controllers/Auth/*.php
php -l app/Http/Controllers/Web/*.php
php -l routes/*.php
```

### Phase 3: Manual Testing

Follow the complete testing checklist in `TESTING_CHECKLIST.md`:

1. **Test Registration Flow**
   - Register new user
   - Verify email received
   - Click verification link
   - Verify success message and modal

2. **Test Login Flow**
   - Login with verified account
   - Dashboard displays correctly
   - User info shows in navbar

3. **Test Password Reset**
   - Forgot password from login
   - Check email for reset link
   - Reset password
   - Login with new password

4. **Test Profile Management**
   - Edit profile
   - Upload avatar
   - View other users

5. **Test Form Validation**
   - Submit empty forms
   - Check error messages
   - Verify validation rules

### Phase 4: Staging Deployment

```bash
# On staging server
cd /var/www/auth-profile-service

# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev
npm install --production

# 3. Build assets
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Test endpoints
curl http://staging.example.com/
curl -X POST http://staging.example.com/api/register -H "Content-Type: application/json"
```

### Phase 5: Production Deployment

```bash
# On production server
cd /var/www/auth-profile-service

# 1. Backup database
mysqldump -u root auth_profile_service > backup_$(date +%Y%m%d).sql

# 2. Pull latest code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Build frontend
npm install --production
npm run build

# 5. Run migrations (if needed)
php artisan migrate --force

# 6. Clear and cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data .

# 8. Restart PHP-FPM
sudo systemctl restart php-fpm

# 9. Restart web server
sudo systemctl restart nginx  # or apache2
```

---

## Environment Configuration

### Development (.env)
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
MAIL_FROM_ADDRESS=dev@example.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auth_profile_dev
DB_USERNAME=root
DB_PASSWORD=

# Mail (Mailtrap)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

### Staging (.env.staging)
```env
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.example.com
MAIL_FROM_ADDRESS=noreply@example.com

# Database
DB_CONNECTION=mysql
DB_HOST=staging-db.example.com
DB_PORT=3306
DB_DATABASE=auth_profile_staging
DB_USERNAME=staging_user
DB_PASSWORD=staging_password

# Mail (Real SMTP provider)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
```

### Production (.env.production)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://auth-profile.example.com
MAIL_FROM_ADDRESS=noreply@auth-profile.example.com

# Database
DB_CONNECTION=mysql
DB_HOST=prod-db.example.com
DB_PORT=3306
DB_DATABASE=auth_profile_prod
DB_USERNAME=prod_user
DB_PASSWORD=very_strong_password

# Mail (Real SMTP provider)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587

# SSL
FORCE_SCHEME=https
SESSION_SECURE_COOKIES=true
```

---

## Web Server Configuration

### Nginx Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name auth-profile.example.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    root /var/www/auth-profile-service/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Redirect HTTP to HTTPS
    if ($server_port !~ 443){
        rewrite ^/(.*)$ https://$server_name/$1 permanent;
    }
}
```

### Apache Configuration (.htaccess)
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteRule ^(.+)/$ /$1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>
```

---

## Database Optimization

### Before Production
```bash
# Analyze tables
php artisan tinker
>>> DB::statement('ANALYZE TABLE users');
>>> DB::statement('ANALYZE TABLE profiles');
>>> DB::statement('ANALYZE TABLE personal_access_tokens');

# Create indexes
php artisan tinker
>>> DB::statement('CREATE INDEX idx_email ON users(email)');
>>> DB::statement('CREATE INDEX idx_verified ON users(email_verified_at)');
>>> DB::statement('CREATE INDEX idx_user_id ON profiles(user_id)');
```

### Backup Strategy
```bash
# Daily backup script (cron)
#!/bin/bash
BACKUP_DIR="/backups/auth-profile-service"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u root --password=YOUR_PASSWORD auth_profile_service > $BACKUP_DIR/db_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

# Upload to cloud storage (optional)
# aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://your-bucket/backups/
```

---

## Monitoring & Logging

### Application Logging
```env
LOG_CHANNEL=stack
LOG_LEVEL=notice

# In config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'daily'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'days' => 14,
    ],
]
```

### Error Monitoring (Sentry)
```env
# In .env
SENTRY_DSN=https://your-sentry-dsn@sentry.io/project-id

# In config/app.php
// Enable in providers
'providers' => [
    Sentry\Laravel\ServiceProvider::class,
]
```

### Performance Monitoring
```bash
# Enable query logging
DB::enableQueryLog();

# View queries in tinker
php artisan tinker
>>> DB::getQueryLog()
```

---

## Security Hardening

### SSL/TLS
```bash
# Generate Let's Encrypt certificate
sudo certbot certonly --webroot -w /var/www/auth-profile-service/public -d auth-profile.example.com

# Auto-renewal
sudo certbot renew --quiet --no-eff-email
```

### Firewall Rules
```bash
# Allow SSH (admin only)
ufw allow from 203.0.113.0/24 to any port 22/tcp

# Allow HTTP/HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Deny all other
ufw default deny incoming
```

### Rate Limiting
```env
# In .env
RATE_LIMIT=60,1 # 60 requests per minute
```

### File Permissions
```bash
# Production permissions
chmod 755 bootstrap cache storage
chmod 644 .htaccess
chmod 600 .env
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

---

## Performance Optimization

### Caching
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache for 1 week
php artisan cache:clear && php artisan config:cache
```

### Database Query Optimization
```bash
# Add indexes
php artisan migrate

# Monitor slow queries
mysql -u root -p -e "SET GLOBAL slow_query_log = 'ON';"
```

### Asset Minification
```bash
# In vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: 'terser',
        sourcemap: false,
    }
})
```

---

## Post-Deployment Verification

### Health Checks
```bash
# Check application
curl -I https://auth-profile.example.com/
# Should return 200

# Check API
curl -X GET https://auth-profile.example.com/api/users
# Should return JSON response

# Check email
curl -X POST https://auth-profile.example.com/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"Pass123!"}'
# Should send verification email
```

### Database Verification
```bash
mysql -u prod_user -p auth_profile_prod << EOF
SELECT COUNT(*) as user_count FROM users;
SELECT COUNT(*) as profile_count FROM profiles;
SHOW TABLE STATUS;
EOF
```

### Log Monitoring
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Check for errors
grep -i "error\|exception\|fatal" storage/logs/laravel.log | tail -20
```

---

## Rollback Plan

If something goes wrong after deployment:

```bash
# 1. Restore previous code
git revert HEAD

# 2. Restore database
mysql -u root auth_profile_service < backup_YYYYMMDD.sql

# 3. Clear cache
php artisan cache:clear

# 4. Restart services
sudo systemctl restart php-fpm nginx

# 5. Verify
curl -I https://auth-profile.example.com/
```

---

## Maintenance Mode

### During Updates
```bash
# Enable maintenance mode
php artisan down --message="Updating..." --retry=60

# Run updates
php artisan migrate
php artisan cache:clear

# Disable maintenance mode
php artisan up
```

### Scheduled Tasks
```bash
# In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:work --max-jobs=1000')->daily();
    $schedule->command('expired-tokens:cleanup')->daily();
    $schedule->command('backup:run')->daily();
}

# Run scheduler
php artisan schedule:work
```

---

## Support & Troubleshooting

### Common Issues

**Issue: 500 Error after deployment**
```bash
# Check logs
tail -100 storage/logs/laravel.log

# Verify permissions
ls -la storage/ bootstrap/cache/

# Clear cache
php artisan cache:clear
php artisan config:clear
```

**Issue: Email not sending**
```bash
# Verify MAIL credentials in .env
# Test SMTP connection
telnet smtp.mailgun.org 587

# Check mail logs
grep -i "mail\|error" storage/logs/laravel.log
```

**Issue: Database connection failed**
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo()

# Check credentials in .env
# Verify database exists
mysql -u root -e "SHOW DATABASES LIKE 'auth_profile%';"
```

---

## Checklist for Go-Live

- [ ] All tests passing
- [ ] Database migrated
- [ ] SSL certificate installed
- [ ] Email service configured
- [ ] Backups enabled
- [ ] Monitoring set up
- [ ] Firewall configured
- [ ] Cache cleared
- [ ] Logs configured
- [ ] Health checks passing
- [ ] Performance acceptable
- [ ] Security headers set
- [ ] Rate limiting enabled
- [ ] Documentation updated

---

## Contact & Support

For issues or questions:
1. Check `TESTING_CHECKLIST.md`
2. Review `COMPLETE_WEB_AUTH_GUIDE.txt`
3. Check application logs: `storage/logs/laravel.log`
4. Review error messages in browser console
5. Check Mailtrap for email delivery issues

---

**Deployment Status: ✅ Ready**

Your authentication system is production-ready and can be deployed following this guide.
