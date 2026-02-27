# ZYNC ERP – Serveruppsättning

Det här dokumentet beskriver hur servern är uppsatt och hur man reproducerar installationen från grunden.

---

## Servermiljö

| Komponent  | Version                  |
|------------|--------------------------|
| OS         | Ubuntu 24.04.4 LTS       |
| PHP        | 8.4.18-FPM               |
| MariaDB    | 10.11.14                 |
| Nginx      | (paketversion för Ubuntu 24.04) |
| Git        | 2.43.0                   |

---

## Installerade paket

```bash
sudo apt update && sudo apt install -y nginx mariadb-server
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
sudo apt install -y php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath php8.4-intl php8.4-gd php8.4-tokenizer
sudo mkdir -p /var/www/zync-erp
sudo chown -R administrator:www-data /var/www/zync-erp
sudo chmod -R 755 /var/www/zync-erp
sudo systemctl enable nginx && sudo systemctl start nginx
```

---

## Nginx-konfiguration

Konfigurationsfilen finns i repot under `zync-erp/nginx/zync-erp.conf`. Installera den så här:

```bash
sudo cp /var/www/zync-erp/nginx/zync-erp.conf /etc/nginx/sites-available/zync-erp
sudo ln -s /etc/nginx/sites-available/zync-erp /etc/nginx/sites-enabled/zync-erp
sudo nginx -t
sudo systemctl reload nginx
```

---

## Deploy (manuellt)

```bash
cd /var/www/zync-erp
git clone https://github.com/zebastianahlin-art/Zync-ERP.git .
cd zync-erp
composer install --no-dev --optimize-autoloader
cp .env.example .env
# Redigera .env med rätt DB-uppgifter
nano .env
```

---

## Framtida deploy (GitHub Actions)

Automatiserad deploy via GitHub Actions + SSH kommer att sättas upp som ett nästa steg.

---

## Tjänster som ska köra

- `nginx.service`
- `php8.4-fpm.service`
- `mariadb.service`

Kontrollera med:

```bash
sudo systemctl list-units --type=service --state=running | grep -E "nginx|php|mariadb"
```
