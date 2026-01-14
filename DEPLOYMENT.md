# 🚀 Deployment Guide - Ngajar.id ke Production

Panduan deploy aplikasi Laravel + Filament + Supabase ke production server.

---

## 🎯 Opsi Deployment

### 1. **Laravel Forge** (Recommended - Easiest)

✅ Auto-setup Laravel environment
✅ One-click deployment
✅ SSL certificate auto-renewal
✅ Server monitoring
💰 $12/month + server cost

### 2. **Vercel** (Serverless - Gratis)

✅ Free untuk hobby projects
✅ Auto SSL
✅ Global CDN
⚠️ Need Vercel Postgres atau tetap pakai Supabase

### 3. **VPS Manual** (DigitalOcean, AWS, etc)

✅ Full control
✅ Bisa custom semua
⚠️ Setup manual lebih kompleks
💰 $5-20/month

### 4. **Shared Hosting** (cPanel)

⚠️ Perlu support PHP 8.2+, Composer, Node.js
⚠️ Limited resources
💰 $3-10/month

---

## 🔥 Deployment ke Laravel Forge (Recommended)

### Prerequisites

- Server VPS (DigitalOcean, AWS, Linode, dll)
- Domain name (opsional tapi recommended)
- Laravel Forge account ($12/month)

### Step 1: Setup Forge Account

1. Daftar di [Laravel Forge](https://forge.laravel.com)
2. Connect dengan Source Control (GitHub/GitLab)
3. Connect dengan VPS provider (DigitalOcean, AWS, dll)

### Step 2: Create Server

1. Forge Dashboard → **Servers** → **Create Server**
2. Pilih:
   - **Provider:** DigitalOcean / AWS / Custom VPS
   - **Server Size:** Minimal $6/month (1GB RAM)
   - **Region:** Singapore (dekat Indonesia)
   - **PHP Version:** 8.2
   - **Database:** PostgreSQL (jika ingin backup lokal, tapi tidak perlu karena pakai Supabase)
3. Klik **Create Server**
4. Wait 5-10 minutes

### Step 3: Deploy Site

1. **Create Site:**

   - Server → **New Site**
   - **Root Domain:** ngajar.id atau subdomain
   - **Web Directory:** `/public` (default Laravel)
   - Klik **Add Site**

2. **Connect Repository:**

   - Site → **Git Repository**
   - Provider: GitHub
   - Repository: `username/ngajar-id`
   - Branch: `main` atau `production`
   - Klik **Install Repository**

3. **Setup Environment:**

   - Site → **Environment**
   - Edit `.env`:

   ```env
   APP_NAME="Ngajar.id"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://ngajar.id

   DB_CONNECTION=pgsql
   DB_HOST=db.pnnjmyeerflqwjnwcurf.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=your_supabase_password

   SUPABASE_URL=https://pnnjmyeerflqwjnwcurf.supabase.co
   SUPABASE_KEY=your_anon_key
   SUPABASE_BUCKET=ngajar-files

   SESSION_DRIVER=database
   CACHE_DRIVER=database
   QUEUE_CONNECTION=database

   MAIL_MAILER=smtp
   # Setup mail config untuk production
   ```

   - Klik **Save**

4. **Deploy Script:**

   - Site → **Deployment**
   - Edit deploy script:

   ```bash
   cd /home/forge/ngajar.id
   git pull origin $FORGE_SITE_BRANCH

   $FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

   ( flock -w 10 9 || exit 1
       echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

   if [ -f artisan ]; then
       $FORGE_PHP artisan migrate --force
       $FORGE_PHP artisan config:cache
       $FORGE_PHP artisan route:cache
       $FORGE_PHP artisan view:cache
       $FORGE_PHP artisan optimize
   fi

   npm ci
   npm run build
   ```

   - Klik **Deploy Now**

5. **Setup SSL:**

   - Site → **SSL**
   - Pilih **LetsEncrypt**
   - Klik **Obtain Certificate**
   - Wait 2 minutes
   - Enable **Force HTTPS**

6. **Setup Scheduler (untuk queue, jika pakai):**
   - Server → **Scheduler**
   - Add: `* * * * * php /home/forge/ngajar.id/artisan schedule:run`

### Step 4: Post-Deployment

```bash
# SSH ke server
ssh forge@your-server-ip

# Masuk ke directory
cd /home/forge/ngajar.id

# Create admin user
php artisan make:filament-user

# Test
php artisan about
```

Access di: https://ngajar.id/admin

---

## 🌐 Deployment ke Vercel (Serverless)

**Note:** Vercel lebih cocok untuk Next.js. Untuk Laravel, gunakan Forge atau VPS.

Alternatif: Deploy Laravel API di VPS, frontend terpisah di Vercel (jika ada).

---

## 🖥️ Deployment ke VPS Manual (Ubuntu)

### Prerequisites

- Ubuntu 22.04 VPS
- Root access
- Domain (opsional)

### Step 1: Setup Server

```bash
# SSH ke VPS
ssh root@your-server-ip

# Update system
apt update && apt upgrade -y

# Install PHP 8.2
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2 php8.2-{cli,fpm,mysql,pgsql,mbstring,xml,curl,zip,gd,bcmath} -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install nodejs -y

# Install Nginx
apt install nginx -y

# Install PostgreSQL client (untuk testing, opsional)
apt install postgresql-client -y
```

### Step 2: Setup Project

```bash
# Create directory
mkdir -p /var/www/ngajar.id
cd /var/www/ngajar.id

# Clone repository (atau upload manual)
git clone https://github.com/username/ngajar-id.git .

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup permissions
chown -R www-data:www-data /var/www/ngajar.id
chmod -R 755 /var/www/ngajar.id
chmod -R 775 /var/www/ngajar.id/storage
chmod -R 775 /var/www/ngajar.id/bootstrap/cache

# Copy .env
cp .env.example .env
php artisan key:generate

# Edit .env
nano .env
# (Paste Supabase credentials)

# Run migrations
php artisan migrate --force
```

### Step 3: Setup Nginx

```bash
nano /etc/nginx/sites-available/ngajar.id
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name ngajar.id www.ngajar.id;
    root /var/www/ngajar.id/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
ln -s /etc/nginx/sites-available/ngajar.id /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### Step 4: Setup SSL (Certbot)

```bash
# Install Certbot
apt install certbot python3-certbot-nginx -y

# Get certificate
certbot --nginx -d ngajar.id -d www.ngajar.id

# Auto-renewal (crontab)
crontab -e
# Add: 0 0 * * * certbot renew --quiet
```

### Step 5: Optimization

```bash
# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Setup supervisor untuk queue (jika pakai)
apt install supervisor -y
nano /etc/supervisor/conf.d/ngajar-worker.conf
```

```ini
[program:ngajar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ngajar.id/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ngajar.id/storage/logs/worker.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start ngajar-worker:*
```

---

## 📋 Pre-Deployment Checklist

### Code

- [ ] `APP_ENV=production` di `.env`
- [ ] `APP_DEBUG=false` di `.env`
- [ ] Route cache cleared
- [ ] Config cache cleared
- [ ] Remove unused packages
- [ ] Optimize Composer autoloader

### Security

- [ ] HTTPS enabled (SSL certificate)
- [ ] `.env` tidak di-commit ke Git
- [ ] Strong `APP_KEY` generated
- [ ] Database password secure
- [ ] File permissions correct (775 storage, 755 others)
- [ ] CORS configured (jika ada API)
- [ ] CSRF protection enabled

### Performance

- [ ] Config cached
- [ ] Routes cached
- [ ] Views cached
- [ ] Optimize command run
- [ ] Assets minified (`npm run build`)
- [ ] Images compressed
- [ ] CDN setup (opsional)

### Database

- [ ] Migrations tested
- [ ] Seeders ready (jika perlu)
- [ ] Backup strategy in place
- [ ] Connection pooling configured

### Monitoring

- [ ] Error logging setup
- [ ] Server monitoring (Forge/New Relic/Sentry)
- [ ] Uptime monitoring
- [ ] Log rotation configured

---

## 🔄 Update/Redeploy Process

### Via Forge (Auto)

1. Push to Git repository
2. Forge auto-deploy (jika auto-deploy enabled)
3. ATAU klik **Deploy Now** di Forge dashboard

### Via Manual VPS

```bash
ssh user@server-ip
cd /var/www/ngajar.id

# Pull changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Clear & cache
php artisan down
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan up

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```

---

## 📊 Monitoring & Maintenance

### Daily Checks

- [ ] Server uptime
- [ ] Error logs: `storage/logs/laravel.log`
- [ ] Database size (Supabase dashboard)
- [ ] Storage usage

### Weekly Checks

- [ ] Backup database (Supabase auto-backup)
- [ ] Review user activities
- [ ] Check for Laravel updates
- [ ] Security patches

### Monthly Checks

- [ ] SSL certificate renewal (auto via Certbot)
- [ ] Server resources usage
- [ ] Clean old logs
- [ ] Review performance metrics

---

## 🆘 Common Production Issues

### 500 Error

```bash
# Check logs
tail -f /var/www/ngajar.id/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Permission Issues

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed

1. Check `.env` credentials
2. Whitelist server IP di Supabase (jika ada firewall)
3. Test connection: `psql -h db.xxx.supabase.co -U postgres -d postgres`

---

## 💰 Cost Estimate (Monthly)

| Service                | Cost           |
| ---------------------- | -------------- |
| Supabase (Free Tier)   | $0             |
| VPS DigitalOcean (1GB) | $6             |
| Laravel Forge          | $12            |
| Domain (.id)           | ~$1            |
| **Total**              | **~$19/month** |

**Alternatif Murah:**

- VPS Manual (tanpa Forge): **$6/month**
- Shared Hosting (jika support Laravel): **$3-5/month**

---

## ✅ Post-Deployment

1. ✅ Test all features
2. ✅ Create admin user: `php artisan make:filament-user`
3. ✅ Login ke `/admin`
4. ✅ Upload test file (cek Supabase storage)
5. ✅ Monitor for 24 hours
6. ✅ Setup monitoring (Sentry, New Relic, dll)
7. ✅ Setup backups

---

**Deployment Date:** TBD
**Server:** TBD
**Status:** 📝 Guide Ready

Good luck with deployment! 🚀
