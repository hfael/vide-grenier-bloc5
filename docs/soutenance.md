# Procédure de soutenance — Bloc 5 (15 minutes)

Soutenance individuelle. Chaque point non démontré = **0 pts**.  
Tout doit être lancé **avant** d'entrer en salle.

---

## Checklist avant d'entrer en salle

- [ ] Tous les conteneurs démarrés (script `demo_start.ps1`)
- [ ] Navigateur : 3 onglets ouverts → http://localhost:8080 | http://localhost:8081 | http://localhost:8082
- [ ] GitHub ouvert sur l'onglet **Issues** (liste des issues fermées)
- [ ] GitHub ouvert sur l'onglet **Pull requests** (PRs dev→recette et recette→main visibles)
- [ ] GitHub ouvert sur l'onglet **Code** (graphe des branches visible)
- [ ] Terminal ouvert dans le répertoire du projet
- [ ] PDFs ouverts : `docs/pdf/mise_en_production.pdf` et `docs/pdf/guide_utilisateur.pdf`

---

## Scénario minute par minute

### ⏱ 0:00 – 1:00 | Présentation du projet (1 min)

**Ce que tu dis :**
> "Je vais vous présenter le projet Vide Grenier En Ligne. J'ai repris une application existante avec des bugs, que j'ai corrigée, testée et déployée en suivant un workflow GitFlow complet, avec trois environnements Docker : développement, recette et production."

**Montrer :** aperçu rapide du site sur `http://localhost:8080`

---

### ⏱ 1:00 – 2:30 | Repository Git & GitFlow (1 min 30)

**Aller sur GitHub → onglet réseau des branches (Insights > Network) ou Code > branches**

**Montrer et dire :**
- Les 3 branches : `dev`, `recette`, `main`
- "Le repo est vivant : chaque fix a son propre commit, pas de commit unique avec tout le code"
- Montrer 2-3 commits parlants : `Fix auth et session cookie`, `Fix client bugs and product contact flow`

**Aller sur Pull Requests (onglet ouvert)**

- Montrer la PR `dev → recette` et la PR `recette → main`
- "J'ai utilisé les merge requests pour pousser le code d'un environnement à l'autre, conformément au GitFlow"

---

### ⏱ 2:30 – 3:30 | Système de gestion des issues (1 min)

**Aller sur GitHub → Issues (onglet ouvert, filtrer "Closed")**

**Montrer et dire :**
- Les 4 issues créées correspondant aux 4 bugs du client
- "Chaque bug a été tracé en issue, avec le temps passé noté dans les commentaires"
- Montrer une issue fermée liée à son commit de fix

---

### ⏱ 3:30 – 5:30 | Environnement de développement Docker (2 min)

**Dans le terminal :**
```
docker ps
```

**Montrer et dire :**
- Les conteneurs `vide-grenier-app` (port 8080) et `vide-grenier-db` (port 3307) sont UP
- "L'environnement de dev est basé sur Docker : un conteneur PHP/Nginx et un conteneur MySQL"
- "Le code source est monté en volume pour permettre le développement live"

**Montrer `docker-compose.yml` rapidement dans l'éditeur**

---

### ⏱ 5:30 – 8:00 | Démonstration des 4 bugs corrigés (2 min 30)

> Avoir un compte test prêt (`test@test.fr / Test1234!`) et rester sur `http://localhost:8080`.

---

**Bug 1 — Photo non requise provoquait une erreur**

Ce que tu dis :
> "Dans le code original, `Product.php` appelait systématiquement `Upload::uploadFile()` même si aucune photo n'était envoyée, ce qui provoquait une exception PHP. J'ai ajouté une méthode `hasUploadedPicture()` qui vérifie d'abord si un fichier est présent avant de tenter l'upload."

Démo : Aller sur `/add`, remplir titre + description, **ne pas mettre de photo**, soumettre → l'annonce est créée sans erreur.

---

**Bug 2 — Utilisateur non connecté après inscription**

Ce que tu dis :
> "Dans `registerAction()`, il y avait un `TODO` commenté : l'inscription créait le compte mais ne connectait pas l'utilisateur. J'ai ajouté l'appel à `$this->login($data)` juste après l'inscription réussie, suivi d'une redirection vers `/account`."

Démo : Aller sur `/register`, créer un nouveau compte → arrivée directe sur la page `/account` connecté.

---

**Bug 3 — "Se souvenir de moi" ne fonctionnait pas**

Ce que tu dis :
> "Double problème : dans le HTML, la checkbox avait `name='#'` — un nom invalide qui ne transmettait jamais la valeur. Et dans `login()`, il y avait un `TODO` non implémenté. J'ai corrigé le `name` en `remember_me` et implémenté `rememberCurrentSession()` qui prolonge le cookie de session à 30 jours."

Démo : Se connecter en cochant "Se souvenir de moi" → fermer le navigateur → rouvrir `localhost:8080` → toujours connecté. (Ou montrer le cookie dans les DevTools F12 → Application → Cookies : expiration dans 30 jours.)

---

**Bug 4 — Formulaire de contact ouvrait la boite mail**

Ce que tu dis :
> "La vue `Product/Show.html` avait un simple lien `<a href='mailto:...'>`  qui ouvrait le client mail local. J'ai remplacé ça par un vrai formulaire HTML avec les champs nom, email, message, et le traitement correspondant dans `showAction()` via la méthode `isValidContactRequest()`."

Démo : Aller sur n'importe quelle annonce → faire défiler vers le bas → le formulaire de contact est visible, pas de boite mail qui s'ouvre.

---

---

### ⏱ 8:00 – 9:00 | Tests unitaires (1 min)

**Dans le terminal :**
```
docker exec vide-grenier-app php tests/unit.php
```

**Montrer et dire :**
- Les tests passent au vert
- "J'ai créé des tests unitaires pour les classes `Hash` et `Upload` — hachage de mot de passe et validation des fichiers uploadés"

---

### ⏱ 9:00 – 10:30 | Environnement de recette (1 min 30)

**Basculer sur `http://localhost:8081`**

**Dans le terminal :**
```
docker ps --filter "name=recette"
```

**Montrer et dire :**
- "L'environnement de recette tourne en parallèle sur le port 8081"
- "L'image Docker de recette embarque le code de la branche `recette`, pas un volume"
- Montrer `docker-compose.recette.yml` — pas de montage `./:/var/www/html`

---

### ⏱ 10:30 – 12:30 | Environnement de production (2 min)

**Basculer sur `http://localhost:8082`**

**Dans le terminal :**
```
docker ps --filter "name=prod"
```

**Montrer et dire :**
- 2 conteneurs : `vide-grenier-app-prod` (port 8082) et `vide-grenier-db-prod` (port 3309)
- "La base de données est persistante : volume `db_data_prod`"
- "Le code de la branche `main` est **intégré dans l'image** Docker — pas de volume de code"
- Montrer `docker-compose.prod.yml` — volumes uniquement pour storage, logs, vendor

**Pour prouver la persistance DB :**
- Ajouter une annonce sur le port 8082
- `docker restart vide-grenier-app-prod`
- Revenir sur le site → l'annonce est toujours là

---

### ⏱ 12:30 – 13:30 | Documents d'exploitation (1 min)

**Ouvrir `docs/pdf/mise_en_production.pdf`**

- "Ce document permet à un collègue de remonter tout le système : commandes, captures d'écran, configuration Docker"

**Ouvrir `docs/pdf/guide_utilisateur.pdf`**

- "Ce document est destiné à l'utilisateur final : comment s'inscrire, déposer une annonce, contacter un vendeur"

---

### ⏱ 13:30 – 14:30 | Coexistence des environnements (1 min)

**Dans le terminal :**
```
docker ps
```

**Montrer et dire :**
- Tous les 6 conteneurs sont UP en même temps : app-dev, db-dev, app-recette, db-recette, app-prod, db-prod
- "Les trois environnements coexistent sur la même machine, sur des ports distincts : 8080 / 8081 / 8082"

**Montrer les 3 onglets navigateur côte à côte**

---

### ⏱ 14:30 – 15:00 | Conclusion (30 sec)

> "Pour résumer : j'ai mis en place un workflow GitFlow complet avec gestion des issues, corrigé les 4 bugs du client, créé des tests unitaires, et déployé l'application sur trois environnements Docker indépendants. Toute la démarche est documentée et les deux documents d'exploitation sont disponibles en PDF."

---

## Points risqués à anticiper

| Risque | Mitigation |
|--------|-----------|
| Conteneur qui met du temps à démarrer | Lancer `demo_start.ps1` **10 min avant** la soutenance |
| Jury demande l'accès GitHub | Vérifier que le repo est partagé avec le jury en amont |
| Oubli du mot de passe du compte test | Mettre `test@test.fr / Test1234!` dans un post-it |
| `docker ps` montre un conteneur Exited | `docker compose up -d` sur le compose concerné |

---

## Ordre de priorité si tu manques de temps

Si tu es à 13 minutes et il reste des points, saute dans cet ordre :
1. ~~Conclusion~~ → passe directement
2. ~~Détail des commits~~ → montre juste la liste
3. ~~Restart pour persistance~~ → montre juste le volume dans le yml

**Ne saute jamais :** tests unitaires, 4 bugs, docker prod 2 containers, les 2 PDFs — ce sont probablement les points les plus lourds.
