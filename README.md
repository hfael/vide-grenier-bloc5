# Vide Grenier En Ligne

Application PHP MVC de depot et consultation d'annonces gratuites.

## Demarrage rapide

```bash
docker compose up -d --build
```

Acces:

- Application: `http://localhost:8080`
- MySQL: `localhost:3307`
- Base: `videgrenierenligne`
- Utilisateur: `videgrenier`
- Mot de passe: `videgrenier`

## Environnements

| Environnement | Commande | Application | MySQL |
| --- | --- | --- | --- |
| Developpement | `docker compose up -d --build` | `http://localhost:8080` | `3307` |
| Recette | `docker compose -f docker-compose.recette.yml up -d --build` | `http://localhost:8081` | `3308` |
| Production | `docker compose -f docker-compose.prod.yml up -d --build` | `http://localhost:8082` | `3309` |

Les trois environnements peuvent tourner en meme temps.

## Tests

```bash
docker compose exec app composer test:unit
docker compose exec app composer test:integration
```

Voir [docs/tests.md](docs/tests.md).

## Documentation

- [Architecture](docs/architecture.md)
- [Developpement](docs/dev_setup.md)
- [Recette](docs/recette.md)
- [Mise en production](docs/mise_en_production.md)
- [Guide utilisateur](docs/guide_utilisateur.md)
- [Release notes](docs/release_notes.md)

Des versions PDF sont disponibles dans `docs/pdf/`.

## GitFlow

- `dev`: corrections et evolutions.
- `recette`: validation avant production.
- `main`: version de production.

La reprise a ete tracee avec des issues GitHub pour les bugs client et les livrables de deploiement.

## Points corriges dans la version 1.1.0

- Creation d'annonce possible sans image.
- Connexion automatique apres inscription.
- Option `Se souvenir de moi` fonctionnelle.
- Formulaire de contact integre sur la fiche produit.
- Tests unitaires et tests d'integration.
- Environnements Docker developpement, recette et production.
