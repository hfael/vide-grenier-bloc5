Write-Host "`n=== DEMARRAGE ENVIRONNEMENT DEMO ===" -ForegroundColor Cyan

# Les 3 environnements doivent coexister. Chacun est isole dans son PROPRE
# projet Docker (-p) pour avoir son propre reseau. Sans cela, les 3 fichiers
# compose partagent le projet par defaut (nom du dossier) et le reseau casse.

Write-Host "`n=== NETTOYAGE DES ANCIENS CONTENEURS ===" -ForegroundColor Cyan
docker rm -f vide-grenier-app vide-grenier-db vide-grenier-phpmyadmin vide-grenier-app-recette vide-grenier-db-recette vide-grenier-app-prod vide-grenier-db-prod 2>$null
docker network prune -f | Out-Null

Write-Host "`n[1/3] Environnement DEV (port 8080)..." -ForegroundColor Yellow
docker compose -p vg_dev -f docker-compose.yml up -d --build

Write-Host "`n[2/3] Environnement RECETTE (port 8081)..." -ForegroundColor Yellow
docker compose -p vg_recette -f docker-compose.recette.yml up -d --build

Write-Host "`n[3/3] Environnement PRODUCTION (port 8082)..." -ForegroundColor Yellow
docker compose -p vg_prod -f docker-compose.prod.yml up -d --build

Write-Host "`n=== ATTENTE DES CONTENEURS (30s) ===" -ForegroundColor Cyan
Start-Sleep -Seconds 30

Write-Host "`n=== ETAT DES CONTENEURS ===" -ForegroundColor Green
docker ps --format "table {{.Names}}`t{{.Status}}`t{{.Ports}}"

Write-Host "`n=== ACCES ===" -ForegroundColor Green
Write-Host "DEV      -> http://localhost:8080" -ForegroundColor White
Write-Host "RECETTE  -> http://localhost:8081" -ForegroundColor White
Write-Host "PROD     -> http://localhost:8082" -ForegroundColor White

Write-Host "`nReady pour soutenance !" -ForegroundColor Green
