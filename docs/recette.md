# Procédure de recette et mise en production – Vide Grenier En Ligne

## 1. Environnement de développement

### Lancement

```bash
docker compose up --build
```

### Accès

* Application : http://localhost:8080
* Base de données : port 3307

## 2. Environnement de recette

### Lancement

```bash
docker compose -f docker-compose.recette.yml up --build
```

### Accès

* Application : http://localhost:8081
* Base de données : port 3308

### Vérifications

* Création compte
* Connexion
* Dépôt annonce avec image
* Dépôt annonce sans image
* Consultation fiche produit

## 3. Environnement de production

### Lancement

```bash
docker compose -f docker-compose.prod.yml up --build
```

### Accès

* Application : http://localhost:8082
* Base de données : port 3309

## 4. Déploiement GitFlow

### Cycle de branches

* `dev` : développement
* `recette` : validation intermédiaire
* `main` : production

### Processus

* Développement sur `dev`
* Merge request vers `recette`
* Validation en recette
* Merge request vers `main`

## 5. Correctifs apportés

### Authentification

* Correction de la redirection après échec de connexion
* Connexion automatique après inscription
* Protection de la page compte
* Ajout d’une persistance de session

### Gestion des annonces

* Correction de l’upload optionnel d’image
* Protection de l’accès au dépôt d’annonce

### Fiche produit

* Suppression du lien `mailto:` au profit d’un contact affiché directement sur la fiche
