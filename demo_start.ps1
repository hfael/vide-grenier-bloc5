Write-Host "`n=== DEMARRAGE ENVIRONNEMENT DEMO ===" -ForegroundColor Cyan

Write-Host "`n[1/3] Environnement DEV (port 8080)..." -ForegroundColor Yellow
docker compose -f docker-compose.yml up -d --build

Write-Host "`n[2/3] Environnement RECETTE (port 8081)..." -ForegroundColor Yellow
docker compose -f docker-compose.recette.yml up -d --build

Write-Host "`n[3/3] Environnement PRODUCTION (port 8082)..." -ForegroundColor Yellow
docker compose -f docker-compose.prod.yml up -d --build

Write-Host "`n=== ATTENTE DES CONTENEURS (30s) ===" -ForegroundColor Cyan
Start-Sleep -Seconds 30

Write-Host "`n=== ETAT DES CONTENEURS ===" -ForegroundColor Green
docker ps --format "table {{.Names}}`t{{.Status}}`t{{.Ports}}"

Write-Host "`n=== ACCES ===" -ForegroundColor Green
Write-Host "DEV      -> http://localhost:8080" -ForegroundColor White
Write-Host "RECETTE  -> http://localhost:8081" -ForegroundColor White
Write-Host "PROD     -> http://localhost:8082" -ForegroundColor White

Write-Host "`nReady pour soutenance !" -ForegroundColor Green
