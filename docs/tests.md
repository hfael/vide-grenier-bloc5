# Plan de tests - Vide Grenier En Ligne

## Objectif

Ce document couvre les tests unitaires et les tests d'integration attendus pour la recette.
Les tests sont volontairement simples afin d'etre executables pendant la soutenance.

## Commandes

Demarrer l'environnement de developpement:

```bash
docker compose up -d --build
```

Lancer les tests unitaires:

```bash
docker compose exec app composer test:unit
```

Lancer les tests d'integration HTTP:

```bash
docker compose exec app composer test:integration
```

Tester la recette:

```bash
docker compose -f docker-compose.recette.yml up -d --build
docker compose -f docker-compose.recette.yml exec app_recette sh -lc "APP_BASE_URL=http://localhost composer test:integration"
```

Tester la production:

```bash
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec app_prod sh -lc "APP_BASE_URL=http://localhost composer test:integration"
```

## Tests unitaires automatises

| Test | Donnee | Resultat attendu |
| --- | --- | --- |
| Hash deterministe | `secret` + `salt` | Le hash a toujours la meme valeur et contient 64 caracteres. |
| Generation de salt | longueur `32` | Le salt genere contient 32 caracteres. |
| Validation upload image | `annonce.JPG`, 120 Ko | Le fichier est accepte. |
| Extension interdite | `script.php` | Une exception est levee. |
| Fichier trop lourd | `photo.png`, 5 Mo | Une exception est levee. |
| Routage statique | route `login` | Le routeur resout le controleur `User` et l'action `login`. |
| Capture de parametre | URL `product/42` | Le parametre `id` capture vaut `42`. |
| URL non routee | URL `product/abc` | Aucune route ne correspond (match faux). |

## Tests d'integration automatises

| Test | Jeu de donnees | Resultat attendu |
| --- | --- | --- |
| API produits | base importee depuis `sql/import.sql` | `/api/products?sort=date` retourne HTTP 200 et un tableau JSON d'annonces. |
| Fiche produit | annonce `id=1` | `/product/1` affiche un formulaire de contact et ne contient plus de lien `mailto:`. |
| Contact vendeur | POST sur `/product/1` avec nom, email, message | La fiche reste disponible en HTTP 200 et affiche le message de confirmation. |

## Tests manuels de non-regression

1. Creer un compte depuis `/register`.
2. Verifier la redirection automatique vers `/account`.
3. Se deconnecter puis se reconnecter avec "Se souvenir de moi".
4. Deposer une annonce avec image.
5. Deposer une annonce sans image.
6. Ouvrir la fiche produit et envoyer un message via le formulaire de contact.
7. Verifier que le tri "Recent" de l'accueil fonctionne.
