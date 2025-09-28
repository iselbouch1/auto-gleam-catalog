# 🚀 Auto Gleam - Déploiement Production

## Architecture Production

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Load Balancer  │────│  Nginx Reverse  │────│   Application   │
│    (Cloudflare)  │    │     Proxy       │    │    Servers      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ├─ Frontend (Static)
                                ├─ Backend API (PHP-FPM)
                                ├─ Admin Filament
                                ├─ Storage Assets
                                └─ WebSocket (Reverb)
```

## 1. Build des Images Docker

### Configuration
```bash
# Créer .env.prod avec vos vraies variables
cp .env.prod.example .env.prod
```

### Build Multi-Architecture
```bash
# Init buildx (une seule fois)
docker buildx create --name multi-arch --use

# Build et push toutes les images
docker buildx bake --push -f docker-compose.prod.yml
```

### Images générées
- `registry.com/auto-gleam/frontend:latest`
- `registry.com/auto-gleam/backend:latest`  
- `registry.com/auto-gleam/nginx:latest`

## 2. Secrets & Variables d'Environnement

### Variables Backend (.env.prod)
```env
# Application
APP_NAME="Auto Gleam"
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_32_CHARS
APP_DEBUG=false
APP_URL=https://auto-gleam.com

# Database (géré)
DB_CONNECTION=mysql
DB_HOST=db.auto-gleam.com
DB_PORT=3306
DB_DATABASE=auto_gleam_prod
DB_USERNAME=auto_gleam
DB_PASSWORD=SUPER_SECURE_PASSWORD

# Cache & Queue
REDIS_HOST=redis.auto-gleam.com
REDIS_PASSWORD=REDIS_SECURE_PASSWORD
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Storage (S3/R2)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=VOTRE_ACCESS_KEY
AWS_SECRET_ACCESS_KEY=VOTRE_SECRET_KEY
AWS_DEFAULT_REGION=auto
AWS_BUCKET=auto-gleam-storage
AWS_URL=https://storage.auto-gleam.com
AWS_ENDPOINT=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com

# Reverb WebSocket
REVERB_APP_ID=prod-auto-gleam
REVERB_APP_KEY=SECURE_RANDOM_KEY
REVERB_APP_SECRET=SECURE_RANDOM_SECRET
REVERB_HOST=ws.auto-gleam.com
REVERB_PORT=443
REVERB_SCHEME=https

# Mail (service géré)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=VOTRE_USERNAME
MAIL_PASSWORD=VOTRE_PASSWORD
MAIL_ENCRYPTION=tls
```

### Variables Frontend
```env
VITE_API_BASE_URL=https://api.auto-gleam.com/api/v1
VITE_USE_MOCK=false
VITE_PUSHER_APP_KEY=SECURE_RANDOM_KEY
VITE_PUSHER_HOST=ws.auto-gleam.com
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
```

## 3. Configuration Nginx Production

### Reverse Proxy Principal (/etc/nginx/sites-available/auto-gleam)
```nginx
upstream backend_api {
    server backend:9000;
}

upstream frontend_static {
    server frontend:80;
}

upstream reverb_ws {
    server backend:8080;
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name auto-gleam.com *.auto-gleam.com;
    return 301 https://$server_name$request_uri;
}

# Main Application
server {
    listen 443 ssl http2;
    server_name auto-gleam.com;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/auto-gleam.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/auto-gleam.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Frontend
    location / {
        proxy_pass http://frontend_static;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # API Backend
    location /api/ {
        proxy_pass http://backend_api;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # Admin
    location /admin {
        proxy_pass http://backend_api;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

# WebSocket Subdomain
server {
    listen 443 ssl http2;
    server_name ws.auto-gleam.com;
    
    ssl_certificate /etc/letsencrypt/live/auto-gleam.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/auto-gleam.com/privkey.pem;
    
    location / {
        proxy_pass http://reverb_ws;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 4. Stratégie Cache & CDN

### Cache Backend (Redis)
- Sessions utilisateur: 2h
- Queries API: 1h avec tags
- Rate limiting: 60 req/min par IP

### Cache Nginx
```nginx
# Cache assets statiques
location ~* \.(jpg|jpeg|png|gif|ico|svg|woff2|css|js)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    add_header Vary Accept-Encoding;
}

# Cache API responses
location /api/ {
    proxy_cache api_cache;
    proxy_cache_valid 200 1h;
    proxy_cache_key $scheme$proxy_host$request_uri;
    add_header X-Cache-Status $upstream_cache_status;
}
```

### CDN (Cloudflare)
```yaml
Cache Rules:
  - Static Assets: Cache Everything, Edge TTL 1 month
  - API Responses: Bypass Cache (géré par Redis)
  - Admin: Bypass Cache
  
Page Rules:
  - /storage/*: Cache Level: Cache Everything, TTL: 1 month
  - /api/*: Cache Level: Bypass, Security: High
```

## 5. Monitoring & Alertes

### Health Checks
```bash
# Endpoint health check
curl -f https://auto-gleam.com/api/v1/health || exit 1

# Database connectivity
curl -f https://auto-gleam.com/api/v1/health/db || exit 1

# Redis connectivity  
curl -f https://auto-gleam.com/api/v1/health/cache || exit 1

# WebSocket connectivity
wscat -c wss://ws.auto-gleam.com/health
```

### Logs Centralisés
```yaml
# docker-compose.prod.yml
logging:
  driver: json-file
  options:
    max-size: "10m"
    max-file: "3"
    labels: "service"
```

## 6. GitHub Actions CI/CD

### .github/workflows/deploy.yml
```yaml
name: Deploy Production

on:
  push:
    branches: [main]
    tags: ['v*']

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Build & Push Images
        run: |
          echo "${{ secrets.REGISTRY_PASSWORD }}" | docker login -u "${{ secrets.REGISTRY_USER }}" --password-stdin
          docker buildx bake --push
      
      - name: Deploy to Production
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.PROD_HOST }}
          username: ${{ secrets.PROD_USER }}
          key: ${{ secrets.PROD_SSH_KEY }}
          script: |
            cd /opt/auto-gleam
            docker compose -f docker-compose.prod.yml pull
            docker compose -f docker-compose.prod.yml up -d
            docker system prune -f
```

## 7. Backup & Disaster Recovery

### Base de Données
```bash
# Backup quotidien
0 2 * * * /opt/scripts/mysql-backup.sh

# Script backup
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME | gzip > /backups/auto_gleam_$DATE.sql.gz
aws s3 cp /backups/auto_gleam_$DATE.sql.gz s3://auto-gleam-backups/
```

### Storage Assets
```bash
# Sync vers backup bucket
aws s3 sync s3://auto-gleam-storage s3://auto-gleam-backups/storage --delete
```

## 8. Checklist Déploiement

### Pré-déploiement
- [ ] Secrets configurés (.env.prod)
- [ ] DNS configurés (A records, CNAME)
- [ ] SSL certificates (Let's Encrypt)
- [ ] Base de données créée
- [ ] Redis instance configurée
- [ ] S3/R2 bucket configuré
- [ ] SMTP service configuré

### Tests Post-Déploiement
- [ ] Site accessible (https://auto-gleam.com)
- [ ] Admin accessible (https://auto-gleam.com/admin)
- [ ] API répond (https://auto-gleam.com/api/v1/products)
- [ ] WebSocket connecté (wss://ws.auto-gleam.com)
- [ ] Images s'affichent
- [ ] Temps réel fonctionne (modifier produit dans admin)
- [ ] SSL Grade A+ (ssllabs.com)

### Surveillance
- [ ] Uptim monitoring configuré
- [ ] Error tracking (Sentry)
- [ ] Performance monitoring (New Relic)
- [ ] Log aggregation (ELK/Grafana)