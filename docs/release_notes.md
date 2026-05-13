# Release notes - v1.1.0

Date: 2026-05-13

## Contexte

Cette version corrige les retours client du site Vide Grenier En Ligne et ajoute les livrables de deploiement demandes pour le Bloc 5.

Issues suivies:

- #1 Bug: creation d'annonce sans image
- #2 Bug: connexion automatique apres inscription
- #3 Bug: option se souvenir de moi
- #4 Evolution: formulaire de contact produit
- #5 Livrables: tests, recette et production Docker

## Corrections

- Une annonce peut etre creee sans image.
- Une image par defaut est affichee quand l'annonce n'a pas de photo.
- L'utilisateur est connecte automatiquement apres inscription.
- La connexion ne redirige plus vers le compte si les identifiants sont invalides.
- La case `Se souvenir de moi` prolonge le cookie de session.
- La fiche produit contient un formulaire de contact integre.
- Le tri `Recent` de l'accueil fonctionne avec `sort=date`.

## Tests

- Ajout d'un runner PHP minimal.
- Ajout de tests unitaires sur le hash et la validation d'upload.
- Ajout de tests d'integration HTTP sur l'API produits et la fiche produit.

## Deploiement

- Ajout d'un environnement de recette sur `8081` / MySQL `3308`.
- Ajout d'un environnement de production sur `8082` / MySQL `3309`.
- L'image Apache/PHP embarque le code applicatif.
- Les donnees MySQL, les logs et les images uploadees sont persistants via volumes Docker.

## Impact utilisateur

- Le parcours d'inscription est plus fluide.
- Le depot d'annonce est possible meme sans photo.
- Le contact vendeur se fait depuis le site.

## Verification avant livraison

```bash
docker compose up -d --build
docker compose exec app composer test:unit
docker compose exec app composer test:integration
docker compose -f docker-compose.recette.yml config --quiet
docker compose -f docker-compose.prod.yml config --quiet
```
