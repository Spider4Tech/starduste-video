# starduste-video

starduste-video est une application web de partage de vidéos (longues et shorts) construite avec Symfony 8 et PostgreSQL, avec traitement des médias via FFmpeg.[cite:18][cite:24]  
Elle permet l’upload de vidéos, la gestion de miniatures, la lecture avec page dédiée et un système de commentaires par utilisateur.[cite:24][cite:25][cite:27][cite:28]

---

## Fonctionnalités principales

- Upload de vidéos via un formulaire dédié, avec détection automatique de la durée et du format vidéo grâce à FFprobe/FFmpeg.[cite:24]  
- Gestion différenciée des contenus « shorts » (vertical) et vidéos classiques (horizontal) avec arborescence de stockage séparée.[cite:24][cite:28]  
- Génération ou upload de miniatures personnalisées, avec miniature de secours par défaut si aucune image n’est fournie.[cite:24]  
- Page de lecture `/watch/{uuid}` avec affichage de la vidéo, des commentaires, des likes/dislikes de commentaires et du profil de l’auteur.[cite:25][cite:27][cite:28]  
- Lecture de shorts via la route `/short/{uuid}` avec filtre sur le flag `isShort`.[cite:25][cite:28]  
- Système d’authentification (login/logout) basé sur le composant Security de Symfony.[cite:18][cite:26]  
- Modèle de données structuré autour des entités `Video`, `Utilisateurs` et `Comments` (likes/dislikes, statut, vues, catégories, etc.).[cite:27][cite:28]  
- Docker Compose pour la base PostgreSQL et un serveur Mailpit pour les emails en environnement de développement.[cite:19][cite:20]  

---

## Stack technique

- **Langage** : PHP ≥ 8.4[cite:18]  
- **Framework backend** : Symfony 8 (framework-bundle, security, form, twig, validator, http-client, serializer, notifier, asset, etc.).[cite:18]  
- **Base de données** : PostgreSQL 16 (via `doctrine/orm` et `doctrine/doctrine-bundle`).[cite:18][cite:19]  
- **ORM** : Doctrine ORM 3.x, migrations via `doctrine/doctrine-migrations-bundle`.[cite:18][cite:16]  
- **Médias** : `php-ffmpeg/php-ffmpeg` et `FFProbe` pour l’analyse des vidéos (dimensions, durée).[cite:18][cite:24]  
- **Tests** : PHPUnit 13, configuration via `phpunit.dist.xml`.[cite:18][cite:21]  
- **Documentation API** : `nelmio/api-doc-bundle` (prévu pour documenter les endpoints API).[cite:18]  
- **Logs & outils** : `symfony/monolog-bundle`, Web Profiler, Maker Bundle, etc.[cite:18][cite:21]  

---

## Structure du projet

Structure simplifiée des dossiers importants à la racine :[cite:16]

- `src/Controller` : contrôleurs HTTP (API upload, lecture vidéo, authentification, pages par défaut…).[cite:22][cite:23][cite:24][cite:25][cite:26]  
- `src/Entity` : entités Doctrine (`Video`, `Utilisateurs`, `Comments`).[cite:27][cite:28]  
- `src/Form` : formulaires Symfony (ex. `VideoUploadType`, `CommentType`).[cite:22][cite:24][cite:25]  
- `src/Repository` : repositories Doctrine pour les entités.[cite:22][cite:27]  
- `templates/` : vues Twig (lecture vidéo, login, upload…).[cite:16][cite:24][cite:25][cite:26]  
- `public/` : point d’entrée HTTP et fichiers statiques, y compris les uploads de vidéos et miniatures.[cite:16][cite:24]  
- `migrations/` : migrations Doctrine pour la base de données.[cite:16]  
- `config/` : configuration Symfony (services, routes, doctrine, sécurité…).[cite:16]  
- `compose.yaml` / `compose.override.yaml` : configuration Docker (PostgreSQL + Mailpit pour le mail).[cite:19][cite:20]  
- `phpunit.dist.xml` : configuration des tests automatisés.[cite:21]  

---

## Modèle de données

### Entité `Video`

L’entité `Video` représente une vidéo publiée sur la plateforme :[cite:27][cite:28]

- `id` (int, PK)  
- `uuid` (string 32, unique) : identifiant public utilisé dans les URLs `/watch/{uuid}` et `/short/{uuid}`.[cite:25][cite:28]  
- `isShort` (bool) : indique s’il s’agit d’un short (vidéo verticale) ou non.[cite:28]  
- `title` (string) : titre de la vidéo.[cite:28]  
- `description` (string, 1024, nullable) : description détaillée.[cite:28]  
- `categorie` (string, nullable) : catégorie de classement.[cite:28]  
- `status` (bool) : statut (visible / privé / brouillon selon usage applicatif).[cite:28]  
- `video_url` (string, nullable) : chemin relatif du fichier vidéo dans `public/uploads/...`.[cite:24][cite:28]  
- `thumbnail` (string, nullable) : chemin relatif de la miniature.[cite:24][cite:28]  
- `video_duration` (int) : durée de la vidéo en secondes (+ méthode `getdurationformatted()` pour rendre  `HH:MM:SS` ou `MM:SS`).[cite:28]  
- `upload_date` (DateTime, nullable) : date de mise en ligne.[cite:24][cite:28]  
- `views` (int) : compteur de vues.[cite:28]  
- `like_vid` / `dislike_vid` (int) : compteurs de likes / dislikes vidéo.[cite:28]  
- Relation `ManyToOne` vers `Utilisateurs` (`uploader_id`) pour l’auteur de la vidéo.[cite:27][cite:28]  
- Relation `OneToMany` vers `Comments` pour les commentaires associés.[cite:27][cite:28]  

### Entités `Utilisateurs` et `Comments`

- `Utilisateurs` : représente un compte utilisateur, incluant notamment pseudo, mot de passe, photo de profil (ex. `pfppath`), rôles et relations avec les vidéos/commentaires.[cite:27][cite:25]  
- `Comments` : représente un commentaire avec texte, date, likes/dislikes, auteur (`CommentUploader`) et lien vers la vidéo (`ComVideo`).[cite:25][cite:27]  

---

## Fonctionnement des uploads vidéo

L’upload des vidéos est géré par `UploadsController` :[cite:23][cite:24]

- **Route API** : `POST /api/VideoUpload`  
- **Formulaire** : `VideoUploadType` avec au minimum les champs :
  - `videoFile` (fichier vidéo)
  - `thumbnailFile` (fichier image optionnel)
  - `title`, `description`, `status`, `categorie`  
- **Traitement** :
  - Inspecte la vidéo avec `FFProbe` pour récupérer largeur, hauteur et durée.[cite:24]  
  - Génère un `uuid` aléatoire (16 octets -> 32 caractères hex).[cite:24][cite:28]  
  - Si la vidéo est verticale (`height > width`) :
    - Stockage dans `public/uploads/shorts/{uuid}/` avec nom de fichier `{uuid}.{ext}`.[cite:24]  
    - Flag `isShort = true` sur l’entité `Video`.[cite:24][cite:28]  
  - Sinon (vidéo classique) :
    - Stockage dans `public/uploads/videos/{uuid}/`.[cite:24]  
    - Flag `isShort = false`.[cite:24][cite:28]  
  - Si aucune miniature n’est fournie, utilisation de `uploads/fallbacksElement/FallbackThumbnail.webp` comme miniature par défaut.[cite:24]  
  - Création de l’entité `Video`, initialisation des compteurs (vues, likes, dislikes) et association à l’utilisateur connecté.[cite:24][cite:28]  

En cas de succès, le contrôleur renvoie une réponse JSON (ex. `"upload short reussi"` ou `"upload Video reussi"`). En cas d’erreur de validation, il renvoie la liste des messages d’erreur avec un statut HTTP 400.[cite:24]  

---

## Routes principales

### Lecture de vidéos

Géré par `WatchController` :[cite:23][cite:25]

- `GET /watch/{uuid}` (`app_watch`)  
  - Charge la `Video` par `uuid`.  
  - Charge les `Comments` associés à la vidéo.  
  - Prépare un `CommentType` pour ajouter un commentaire (action `comment_add`).  
  - Rend le template `watch/watch.html.twig` avec :
    - `video`
    - `Commentaires` (tableau structuré)
    - `CommentForm` (vue Twig du formulaire)[cite:25]  

- `GET /short/{uuid}` (`app_short`)  
  - Charge la vidéo via `VideoRepository` avec `uuid` et `isShort = true`.  
  - Rend le template `watch/short.html.twig` avec la variable `short`.[cite:25]  

### Uploads

Toujours dans `UploadsController` :[cite:23][cite:24]

- `POST /api/VideoUpload` : endpoint d’upload vidéo (cf. section Upload).  
- `GET /shortupload` (`short_uploads_form`) : rend la vue `short_upload.html.twig` (formulaire d’upload de short).[cite:24]  
- `GET /createvideo` (`video_create_form`) : rend `uploads/createvideo.html.twig` avec `uploadForm`.[cite:24]  

### Authentification

Géré par `AuthentificationController` :[cite:23][cite:26]

- `GET /login` (`app_login`)  
  - Affiche le formulaire de login (template `/authentification/login.html.twig`).  
  - Expose `last_username` et `error` pour afficher les erreurs de connexion.[cite:26]  

- `GET /logout` (`app_logout`)  
  - Route interceptée par le firewall Symfony pour effectuer la déconnexion.[cite:26]  

### Autres contrôleurs

- `DefaultController`, `ApiAuthController`, `TestController`, `VideoTestController` fournissent des routes additionnelles (tests, API d’auth, pages par défaut) et peuvent servir de base pour étendre l’application (non détaillés ici pour conserver un README concis).[cite:23]  

---

## Prérequis

Pour lancer le projet en local sans Docker complet (application + BDD), il faut :[cite:18][cite:19][cite:24]

- PHP ≥ 8.4  
- Composer  
- Une base de données PostgreSQL (16 recommandé)  
- FFmpeg/FFprobe installés et accessibles dans le PATH  
- Symfony CLI (facultatif mais recommandé)  
- Node.js et npm/yarn si vous gérez des assets front supplémentaires (via Asset Mapper/Stimulus).  

Pour les services annexes via Docker :[cite:19][cite:20]

- Docker et Docker Compose  

---

## Installation (sans Docker complet)

1. **Cloner le dépôt et se placer sur la branche `dev`**  

   ```bash
   git clone https://github.com/Spider4Tech/starduste-video.git
   cd starduste-video
   git checkout dev
   ```

2. **Installer les dépendances PHP**  

   ```bash
   composer install
   ```

3. **Configurer l’environnement**  

   - Dupliquer le fichier `.env` en `.env.local` (ou créer `.env.local`), puis ajuster au minimum :
     - `DATABASE_URL` (PostgreSQL)
     - `MAILER_DSN`
     - éventuelles variables liées à FFmpeg (si nécessaire)  
   - Ne pas committer `.env.local` dans le dépôt.  

4. **Créer la base et appliquer les migrations**  

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Lancer le serveur de développement Symfony**  

   ```bash
   symfony server:start
   # ou
   php -S 127.0.0.1:8000 -t public
   ```

6. **Accéder à l’application**  

   - Frontend : [http://127.0.0.1:8000](http://127.0.0.1:8000)  
   - Page de login : `/login`  
   - Upload vidéo : `/createvideo` ou `/shortupload` (selon vos templates)  

---

## Utilisation avec Docker (BDD + mail)

Le projet fournit une configuration Docker Compose pour la base PostgreSQL et Mailpit :[cite:19][cite:20]

1. **Démarrer les services nécessaires**

   ```bash
   docker compose up -d database mailer
   ```

2. **Services exposés**

   - **PostgreSQL** (`database`) :
     - Image : `postgres:16-alpine`.[cite:19]  
     - Port : `5432` (exposé localement).[cite:20]  
     - Variables : `POSTGRES_DB`, `POSTGRES_USER`, `POSTGRES_PASSWORD` (à configurer dans `.env`).[cite:19]  

   - **Mailpit** (`mailer`) :
     - Port SMTP : `1025`.[cite:20]  
     - Interface web : [http://127.0.0.1:8025](http://127.0.0.1:8025).[cite:20]  
     - Utilisé pour capturer les emails en développement (tests d’envoi sans livraison réelle).  

3. **Configuration Symfony**

   - Configurez `DATABASE_URL` pour utiliser l’hôte du service Docker (`database`) et le port 5432.  
   - Configurez `MAILER_DSN` pour pointer vers Mailpit (`smtp://localhost:1025` par exemple).  

L’application Symfony elle-même reste lancée via PHP/Symfony CLI sur la machine hôte.

---

## Lancer les tests

Les tests sont configurés via `phpunit.dist.xml` pour exécuter la suite `tests/` en environnement `test` :[cite:21]

```bash
php bin/phpunit
```

La configuration active, entre autres, l’affichage des erreurs, des warnings et des dépréciations, et inclut le dossier `src/` pour analyser la couverture du code.[cite:21]  

---

## API d’upload (intégration côté client)

Pour intégrer l’upload vidéo depuis un client (SPA, mobile, etc.), la route principale est :[cite:24]

- `POST /api/VideoUpload`  

Exemple d’appel (pseudo-code HTTP multipart) :

```http
POST /api/VideoUpload HTTP/1.1
Host: localhost:8000
Content-Type: multipart/form-data; boundary=----boundary

------boundary
Content-Disposition: form-data; name="videoFile"; filename="video.mp4"
Content-Type: video/mp4

...binaire...
------boundary
Content-Disposition: form-data; name="thumbnailFile"; filename="thumb.jpg"
Content-Type: image/jpeg

...binaire...
------boundary
Content-Disposition: form-data; name="title"

Mon super titre
------boundary
Content-Disposition: form-data; name="description"

Description de la vidéo
------boundary
Content-Disposition: form-data; name="status"

1
------boundary
Content-Disposition: form-data; name="categorie"

Musique
------boundary--
```

La réponse sera un JSON indiquant le succès ou les erreurs de validation (messages lisibles côté client).[cite:24]  

---

## Licence

Le projet est actuellement déclaré comme **propriétaire** dans `composer.json`. Toute réutilisation doit donc être validée par l’auteur du dépôt.[cite:18]
