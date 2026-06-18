# Mise en production - Vide Grenier En Ligne

## Sommaire

- 1. Architecture cible
- 2. Prerequis
- 3. Chronologie de mise en production
- 4. Verification post-deploiement
- 5. Retour arriere
- 6. Exploitation

## 1. Architecture cible

```text
Navigateur
  -> http://localhost:8082
  -> Container app_prod, Apache + PHP 8.1
  -> public/index.php
  -> MVC PHP / Twig
  -> Container db_prod, MySQL 8.0
  -> Volume db_data_prod
```

Services de production:

| Service | Role | Port |
| --- | --- | --- |
| `app_prod` | Serveur web Apache/PHP avec le code embarque dans l'image | `8082:80` |
| `db_prod` | Base MySQL persistante | `3309:3306` |
| `prod_storage` | Images deposees par les utilisateurs | volume Docker |
| `prod_logs` | Journaux applicatifs | volume Docker |

## 2. Prerequis

- Docker Desktop ou Docker Engine.
- Port `8082` libre.
- Port `3309` libre.
- Branche `main` a jour.

## 3. Chronologie de mise en production

1. Recuperer le depot:

```bash
git clone https://github.com/hfael/vide-grenier-bloc5.git
cd vide-grenier-bloc5
```

2. Se placer sur la branche production:

```bash
git checkout main
git pull origin main
```

3. Construire et lancer l'environnement de production:

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

4. Controler l'etat des conteneurs:

```bash
docker compose -f docker-compose.prod.yml ps
```

5. Lancer les tests de fumee:

```bash
docker compose -f docker-compose.prod.yml exec app_prod sh -lc "APP_BASE_URL=http://localhost composer test:integration"
```

6. Ouvrir l'application:

```text
http://localhost:8082
```

## 4. Verification post-deploiement

- La page d'accueil affiche les annonces.
- `/api/products?sort=date` retourne du JSON.
- `/product/1` affiche un formulaire de contact.
- `/register` permet de creer un compte.
- `/product` permet de deposer une annonce une fois connecte.
- Une annonce sans image s'affiche avec l'image par defaut.

## 5. Retour arriere

En cas d'anomalie bloquante:

```bash
docker compose -f docker-compose.prod.yml down
git checkout <ancien_commit_valide>
docker compose -f docker-compose.prod.yml up -d --build
```

Les donnees MySQL restent conservees dans `db_data_prod`.

## 6. Exploitation

Voir les logs:

```bash
docker compose -f docker-compose.prod.yml logs -f app_prod
docker compose -f docker-compose.prod.yml logs -f db_prod
```

Arreter la production:

```bash
docker compose -f docker-compose.prod.yml down
```

Reinitialiser completement la production, donnees comprises:

```bash
docker compose -f docker-compose.prod.yml down -v
```
