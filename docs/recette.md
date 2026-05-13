# Procedure de recette - Vide Grenier En Ligne

## Objectif

Valider que la version corrigee repond aux demandes client avant la mise en production.

## Branches GitFlow

| Branche | Role |
| --- | --- |
| `dev` | Developpement et corrections. |
| `recette` | Validation fonctionnelle. |
| `main` | Production. |

Processus attendu:

1. Developper sur `dev`.
2. Merger `dev` vers `recette`.
3. Valider les tests et la demonstration.
4. Merger `recette` vers `main`.

## Lancer la recette

```bash
docker compose -f docker-compose.recette.yml up -d --build
```

Acces:

- Application: `http://localhost:8081`
- Base MySQL exposee: `localhost:3308`
- Base: `videgrenierenligne_recette`
- Utilisateur: `videgrenier`
- Mot de passe: `videgrenier`

## Scenario de recette

1. Ouvrir `http://localhost:8081`.
2. Verifier que les annonces s'affichent.
3. Utiliser le filtre "Popularite".
4. Utiliser le filtre "Recent".
5. Creer un compte depuis `/register`.
6. Confirmer que l'utilisateur arrive sur `/account` apres inscription.
7. Se deconnecter.
8. Se reconnecter avec la case "Se souvenir de moi".
9. Deposer une annonce avec image.
10. Deposer une annonce sans image.
11. Ouvrir la fiche d'une annonce sans image et verifier l'image par defaut.
12. Envoyer un message depuis le formulaire de contact d'une fiche produit.

## Commandes de validation

```bash
docker compose -f docker-compose.recette.yml exec app_recette composer test:unit
docker compose -f docker-compose.recette.yml exec app_recette sh -lc "APP_BASE_URL=http://localhost composer test:integration"
```

## Critere d'acceptation

La recette est acceptee si:

- les 4 bugs client sont corriges;
- les tests unitaires passent;
- les tests d'integration passent;
- l'application fonctionne dans le navigateur sur le port `8081`;
- aucune regression bloquante n'est constatee dans le scenario ci-dessus.
