# Server Setup

## GitHub Actions – Automatisk deploy

### Förutsättningar på servern (engångssteg)

Generera en SSH-nyckel på VPS:en som GitHub Actions kan använda vid deploy:

```bash
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
cat ~/.ssh/github_deploy  # kopiera denna — det är den privata nyckeln för VPS_SSH_KEY
```

Tillåt `administrator` att ladda om nginx och php-fpm utan lösenord:

```bash
sudo visudo
# Lägg till dessa rader:
administrator ALL=(ALL) NOPASSWD: /usr/sbin/service nginx reload
administrator ALL=(ALL) NOPASSWD: /usr/sbin/service php8.4-fpm reload
administrator ALL=(ALL) NOPASSWD: /bin/systemctl reload nginx
administrator ALL=(ALL) NOPASSWD: /bin/systemctl reload php8.4-fpm
```

---

### GitHub Secrets att lägga till

Gå till: `Settings → Secrets and variables → Actions → New repository secret`

| Secret | Värde |
|--------|-------|
| `VPS_HOST` | Serverns IP-adress eller domännamn |
| `VPS_USER` | `administrator` |
| `VPS_SSH_KEY` | Privata nyckeln från `~/.ssh/github_deploy` |
| `VPS_PORT` | `22` |
| `VPS_DEPLOY_PATH` | `/var/www/zync-erp` |

---

### Hur det fungerar

- Varje `git push` till `main` triggar workflow
- GitHub Actions SSH:ar in på servern
- Kör `git pull`, `composer install`, och laddar om PHP-FPM + Nginx
- Inget manuellt arbete krävs
