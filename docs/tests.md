# Plan de tests – Vide Grenier En Ligne

## 1. Tests d’authentification

### Inscription utilisateur
- Accès à la page /register
- Saisie des champs requis
- Validation du formulaire
- Vérification de la création du compte
- Vérification de la connexion automatique après inscription

### Connexion utilisateur
- Accès à la page /login
- Saisie email / mot de passe valides
- Vérification redirection vers /account

### Refus connexion invalide
- Saisie mauvais mot de passe
- Vérification absence de redirection compte

### Fonction "Se souvenir de moi"
- Connexion avec case cochée
- Vérification persistance session navigateur

## 2. Tests annonces

### Création annonce avec image
- Connexion utilisateur
- Création annonce avec image
- Vérification affichage image produit

### Création annonce sans image
- Connexion utilisateur
- Création annonce sans image
- Vérification absence d’erreur

## 3. Tests sécurité

### Protection espace compte
- Accès /account sans session
- Vérification redirection /login

### Protection dépôt annonce
- Accès /product sans session
- Vérification redirection /login

## 4. Tests affichage produit

### Affichage fiche produit
- Vérification :
  - image
  - description
  - nombre de vues

### Contact vendeur
- Vérification affichage email vendeur intégré à la fiche