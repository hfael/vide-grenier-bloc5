# Architecture du projet

## Architecture applicative

Vide Grenier en Ligne est une application web monolithique en PHP organisee autour d'un MVC simple.
Le point d'entree unique est [`public/index.php`](../public/index.php), expose par re-ecriture d'URL via [`public/.htaccess`](../public/.htaccess) ou [`nginx-configuration.txt`](../nginx-configuration.txt).

Flux technique principal:

```text
Navigateur
  -> public/index.php
  -> Core\Router
  -> App\Controllers\*
  -> App\Models\*
  -> MySQL
  -> rendu Twig ou reponse JSON
```

Les couches sont les suivantes:

- `Core/`
  - infrastructure minimale du framework maison: routeur, controleur de base, acces PDO, rendu Twig, gestion des erreurs.
- `App/Controllers/`
  - orchestration des cas d'usage.
  - `Home` gere l'accueil.
  - `User` gere inscription, connexion, compte et deconnexion.
  - `Product` gere le depot et la consultation d'une annonce.
  - `Api` expose les reponses JSON utilisees par le front.
- `App/Models/`
  - acces aux tables MySQL via PDO.
  - `Articles` gere la lecture, l'incrementation des vues, la creation d'annonce et l'association d'image.
  - `User` gere la creation de compte et la recherche d'un utilisateur pour la connexion.
  - `Cities` gere l'autocompletion des villes.
- `App/Views/`
  - templates Twig rendus cote serveur.
  - `base.html` definit le layout commun et injecte l'utilisateur connecte sur toutes les pages via `Core\View::setDefaultVariables()`.
- `App/Utility/`
  - services transverses.
  - `Hash` genere le hash SHA-256 et le salt des mots de passe.
  - `Upload` deplace les fichiers dans `public/storage/`.
- `public/`
  - racine web, assets statiques, CSS compile, JavaScript, images et stockage des images d'annonces.

Le routage est declare dans [`public/index.php`](../public/index.php):

- `/` -> `Home::indexAction`
- `/login`, `/register`, `/logout`, `/account`
- `/product` et `/product/{id}`
- `/{controller}/{action}` pour les endpoints generiques comme `/api/products` et `/api/cities`

Les routes privees s'appuient sur un simple flag `private` dans le routeur. L'acces est autorise si `$_SESSION['user']['id']` est present.

Le rendu est majoritairement serveur, avec deux enrichissements AJAX:

- la page d'accueil charge les annonces via `/api/products`
- le formulaire de depot interroge `/api/cities` pour l'autocompletion

### Donnees principales

La base importee depuis [`sql/import.sql`](../sql/import.sql) repose principalement sur:

- `users`
  - comptes utilisateurs, email, mot de passe hash, salt, role admin.
- `articles`
  - annonces publiees, auteur, description, date, compteur de vues, image.
  - relation `articles.user_id -> users.id`.
- `villes_france`
  - referentiel de villes utilise par la recherche.

## Flux utilisateurs

### 1. Consulter l'accueil

1. L'utilisateur ouvre `/`.
2. `Home::indexAction` rend `App/Views/Home/index.html`.
3. Le JavaScript de la page appelle `/api/products?sort=...`.
4. `Api::ProductsAction` recupere les articles via `Articles::getAll()`.
5. La liste JSON est injectee dans le DOM par jQuery.

### 2. Creer un compte

1. L'utilisateur ouvre `/register`.
2. Le formulaire poste `username`, `email`, `password` et `password-check`.
3. `User::registerAction` verifie la confirmation puis appelle `User::register()`.
4. Le mot de passe est sale et hashe via `App\Utility\Hash`.
5. `App\Models\User::createUser()` insere l'utilisateur en base.
6. L'utilisateur est connecte automatiquement puis redirige vers `/account`.

### 3. Se connecter

1. L'utilisateur ouvre `/login`.
2. Le formulaire poste `email` et `password`.
3. `User::loginAction` appelle `User::login()`.
4. `App\Models\User::getByLogin()` charge l'utilisateur.
5. Le hash saisi est compare au hash stocke.
6. Si la verification reussit, `$_SESSION['user']` est alimente puis l'utilisateur est redirige vers `/account`.

### 4. Consulter son compte

1. L'utilisateur connecte ouvre `/account`.
2. Le routeur bloque l'acces si aucune session n'est presente.
3. `User::accountAction` charge les annonces de l'utilisateur via `Articles::getByUser()`.
4. La vue `User/account.html` affiche la liste des annonces deposees.

### 5. Deposer une annonce

1. L'utilisateur connecte ouvre `/product`.
2. `Product::indexAction` affiche le formulaire d'ajout.
3. Le champ ville appelle `/api/cities?query=...` pour l'autocompletion.
4. Lors de la soumission:
   - l'annonce est inseree via `Articles::save()`
   - l'image est enregistree dans `public/storage/` via `Upload::uploadFile()`
   - le nom du fichier est rattache a l'article via `Articles::attachPicture()`
5. L'utilisateur est redirige vers `/product/{id}`.

### 6. Consulter une annonce

1. L'utilisateur ouvre `/product/{id}`.
2. `Product::showAction` incremente le compteur avec `Articles::addOneView()`.
3. L'article complet est charge avec son proprietaire via `Articles::getOne()`.
4. Des suggestions sont chargees via `Articles::getSuggest()`.
5. La page affiche l'image, la description, le compteur de vues et un formulaire de contact integre.

### 7. Se deconnecter

1. L'utilisateur ouvre `/logout`.
2. `User::logoutAction` vide `$_SESSION`, supprime le cookie de session puis redirige vers `/`.

## Structure du projet

```text
App/
  Config.php
  Controllers/
  Models/
  Utility/
  Views/
Core/
  Controller.php
  Error.php
  Model.php
  Router.php
  View.php
public/
  index.php
  .htaccess
  bootstrap/
  css/
  fonts/
  images/
  js/
  storage/
  style/
sql/
  import.sql
style/
  *.scss
vendor/
docs/
  architecture.md
```

Roles des repertoires:

- `style/` contient les sources SCSS.
- `public/style/` contient le CSS compile expose au navigateur.
- `public/storage/` contient les images televersees.
- `vendor/` contient les dependances Composer.
- `logs/` est present pour accueillir les journaux mais n'est pas encore exploite dans le code.

## Dependances principales

### Back-end

- PHP 7.x
- Composer avec autoload PSR-4
- `twig/twig` `~3.0` pour le rendu HTML
- extensions PHP `ext-pdo` et `ext-json`
- MySQL ou MariaDB pour les tables `users`, `articles` et `villes_france`

### Front-end

- Bootstrap 3 embarque dans `public/bootstrap/`
- jQuery embarque dans `public/js/jquery.min.js`
- plugin `bootstrap-autocomplete` charge depuis jsDelivr
- Font Awesome et police `circle-video`
- Google Fonts (`Hind`, `Hind Guntur`)

### Tooling

- Node.js / npm
- `node-sass` `^4.14.1`
- script `npm run watch` pour compiler `style/*.scss` vers `public/style/`

## Points d'attention pour la maintenance

- La gestion de session est volontairement simple et ne repose pas sur un middleware dedie.
- L'upload d'image est ecrit directement dans `public/storage/`, ce qui simplifie l'affichage mais melange stockage applicatif et exposition web.
- Le formulaire de contact affiche une confirmation cote application; l'envoi email reel pourra etre branche ensuite sur un SMTP.
