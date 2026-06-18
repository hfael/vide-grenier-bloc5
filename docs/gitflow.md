# Suivi GitFlow et issues

## Branches

| Branche | Usage |
| --- | --- |
| `dev` | Corrections, tests et documentation en cours. |
| `recette` | Validation fonctionnelle avant production. |
| `main` | Version de production. |

## Flux applique

1. Les bugs client sont recenses dans GitHub Issues.
2. Les corrections sont commitees sur `dev`.
3. Une pull request `dev -> recette` sert a valider la version.
4. Une pull request `recette -> main` sert a publier la version de production.
5. Les issues sont fermees quand les corrections sont disponibles dans `main`.

## Issues creees

- #1 Creation d'annonce sans image.
- #2 Connexion automatique apres inscription.
- #3 Option `Se souvenir de moi`.
- #4 Formulaire de contact produit.
- #5 Tests, recette et production Docker.
