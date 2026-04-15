# Environnement de developpement

## Objectif

Le moyen le plus simple pour voir le projet dans un navigateur est d'utiliser Docker Compose.
Le front statique est deja compile dans `public/style/main.css`, donc Node.js n'est pas necessaire pour un premier lancement.

## Prerequis

- Docker Desktop ou Docker Engine avec Compose
- ports libres:
  - `8080` pour l'application
  - `3307` pour MySQL

## Lancement

Depuis la racine du projet:

```bash
docker compose up --build
```

L'application sera disponible sur:

- `http://localhost:8080`

La base sera accessible sur:

- host: `localhost`
- port: `3307`
- base: `videgrenierenligne`
- utilisateur: `videgrenier`
- mot de passe: `videgrenier`

## Ce que fait le setup

- lance Apache avec `public/` comme racine web
- active `mod_rewrite` pour reutiliser `public/.htaccess`
- installe automatiquement les dependances Composer manquantes au demarrage
- lance MySQL et importe `sql/import.sql` au premier demarrage

## Specificites du projet

- Le premier demarrage peut etre long car `sql/import.sql` contient un gros referentiel `villes_france`.
- L'import SQL n'est joue qu'au premier initialisation du volume MySQL.
- Si vous voulez reinitialiser completement la base, utilisez:

```bash
docker compose down -v
docker compose up --build
```

- Les images uploadees sont ecrites dans `public/storage/`.
- Pour le moment, l'application depend encore de variables de configuration en dur si aucune variable d'environnement n'est fournie.

## Pages a verifier dans le navigateur

- `/` accueil
- `/login`
- `/register`
- `/product/1` exemple de fiche annonce
- `/api/products`
- `/api/cities?query=Par`

## Limites actuelles connues

- Le flux d'inscription ne connecte pas automatiquement l'utilisateur.
- Le tri "Recent" du front envoie `sort=date`, alors que le modele attend actuellement `data`.
- Les fonctions de modification et suppression ne sont pas implementees dans le code actuel.
