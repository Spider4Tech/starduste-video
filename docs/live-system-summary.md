# Résumé des modifications pour les lives

## Objectif

Mise en place d'un système de live fonctionnel avec :

- une clé de live par utilisateur ;
- un serveur RTMP pour recevoir le flux depuis OBS ou un encodeur ;
- une conversion RTMP vers HLS ;
- une page dédiée de visionnage live ;
- un affichage des lives actifs sur la page d'accueil ;
- une activation/désactivation automatique des lives selon l'état réel du flux.

## Infrastructure RTMP/HLS

### Nouveau service Docker `rtmp`

Ajout d'un service Docker basé sur l'image :

```yaml
tiangolo/nginx-rtmp
```

Ports exposés :

```text
1935 -> RTMP
8081 -> HLS HTTP
```

Utilisation :

```text
RTMP ingest : rtmp://localhost:1935/live
HLS output  : http://localhost:8081/hls/<clé>.m3u8
```

Fichier ajouté :

```text
docker/rtmp/nginx.conf
```

Ce fichier configure :

- le serveur RTMP ;
- l'application RTMP `live` ;
- la génération HLS ;
- les fragments HLS de 2 secondes ;
- le nettoyage automatique des fichiers HLS ;
- les callbacks Symfony `on_publish` et `on_publish_done`.

## Variables d'environnement

Ajout dans `.env` et dans le service PHP Docker :

```env
LIVE_HLS_PUBLIC_BASE_URL=http://localhost:8081/hls
LIVE_RTMP_PUBLIC_URL=rtmp://localhost:1935/live
```

Ces variables permettent à Symfony de générer automatiquement :

```text
http://localhost:8081/hls/<clé>.m3u8
```

à partir de la clé de live.

## Base de données

### Utilisateur

Ajout d'une colonne sur `Utilisateurs` :

```sql
live_stream_key VARCHAR(64) UNIQUE DEFAULT NULL
```

Cette colonne stocke la clé privée utilisée par OBS ou un encodeur.

Exemple :

```text
sd_live_07a3881429c5985985290fc6cb4496f722c64ac95da3b070
```

### Nouvelle table `live_stream`

Ajout d'une entité dédiée `LiveStream` avec une table :

```sql
live_stream
```

Champs principaux :

```text
id
streamer_id
slug
title
category
thumbnail_path
playback_url
live
viewers
started_at
created_at
updated_at
```

Le champ `live` indique si le flux RTMP est réellement actif.

Migration ajoutée :

```text
migrations/Version20260601010000.php
```

## Backend Symfony

### Nouvelle entité

Fichier ajouté :

```text
src/Entity/LiveStream.php
```

Elle représente un live avec :

- son créateur ;
- son slug public ;
- son titre ;
- sa catégorie ;
- son URL HLS ;
- son état actif/inactif ;
- son nombre de spectateurs ;
- ses dates de création, mise à jour et démarrage.

### Nouveau repository

Fichier ajouté :

```text
src/Repository/LiveStreamRepository.php
```

Méthode ajoutée :

```php
findLiveStreams(int $limit = 12)
```

Elle retourne uniquement les lives actifs :

```php
live = true
```

triés par nombre de spectateurs puis date de démarrage.

## Contrôleur Live

Fichier ajouté/modifié :

```text
src/Controller/LiveController.php
```

### Routes ajoutées

```text
GET      /lives
GET      /live/{slug}
POST     /live/key/generate
POST     /live/start
POST     /live/stop
GET|POST /live/rtmp/publish
GET|POST /live/rtmp/publish-done
```

### `/live/key/generate`

Génère ou régénère une clé live pour l'utilisateur connecté.

Format :

```text
sd_live_<48 caractères hexadécimaux>
```

### `/live/start`

Prépare un live :

- crée une entrée `LiveStream` si nécessaire ;
- définit le titre ;
- définit la catégorie ;
- définit la miniature optionnelle ;
- génère automatiquement l'URL HLS si aucune URL manuelle n'est donnée.

Important : cette route prépare le live, mais ne le marque pas forcément comme réellement actif. Le live devient actif quand le serveur RTMP reçoit un flux valide.

### `/live/stop`

Arrête manuellement le live :

```php
live = false
viewers = 0
```

### `/live/{slug}`

Affiche la page dédiée du live.

La page montre :

- le player live ;
- le statut live ou hors ligne ;
- les informations du streamer ;
- la catégorie ;
- le nombre de spectateurs ;
- un chat visuel côté front ;
- les informations de configuration OBS si le propriétaire regarde sa propre page.

### `/live/rtmp/publish`

Callback appelé automatiquement par Nginx RTMP quand un flux démarre.

Il reçoit la clé via le paramètre `name`.

Comportement :

- si la clé est absente : `403` ;
- si la clé est invalide : `403` ;
- si la clé est valide :
  - retrouve l'utilisateur ;
  - crée un live si nécessaire ;
  - active le live ;
  - définit l'URL HLS ;
  - renseigne `started_at`.

### `/live/rtmp/publish-done`

Callback appelé automatiquement quand le flux RTMP s'arrête.

Comportement :

- retrouve l'utilisateur par clé ;
- passe le live en hors ligne ;
- remet les spectateurs à `0`.

## Page d'accueil

Fichier modifié :

```text
templates/acceuil.html.twig
```

L'encart "En direct" utilise maintenant de vrais lives actifs.

Avant, l'encart était prévu mais non branché.

Maintenant :

- il récupère les lives depuis `LiveStreamRepository` ;
- chaque live pointe vers `/live/{slug}` ;
- affiche :
  - miniature ;
  - badge LIVE ;
  - nombre de spectateurs ;
  - avatar du streamer ;
  - titre ;
  - pseudo ;
  - catégorie.

Le lien "Tous les lives" pointe maintenant vers :

```text
/lives
```

## Page liste des lives

Fichier ajouté :

```text
templates/live/index.html.twig
```

Route :

```text
/lives
```

Cette page affiche tous les lives actifs sous forme de grille.

Chaque carte contient :

- miniature ou fallback visuel ;
- badge LIVE ;
- nombre de spectateurs ;
- avatar du streamer ;
- titre ;
- pseudo ;
- catégorie.

Si aucun live n'est actif, un état vide est affiché.

## Page dédiée live

Fichier ajouté :

```text
templates/live/watch.html.twig
```

Route :

```text
/live/{slug}
```

Design inspiré des plateformes type Twitch / YouTube Live :

- player principal large ;
- colonne de chat à droite ;
- barre supérieure ;
- infos du streamer sous le player ;
- boutons d'action ;
- état "flux en attente" si le live est préparé mais pas encore reçu ;
- état "live terminé" si le flux est arrêté.

Le player lit automatiquement l'URL HLS :

```text
http://localhost:8081/hls/<clé>.m3u8
```

quand le live est actif.

## Onglet Live dans le profil

Fichier modifié :

```text
templates/partials/_profile_modal.html.twig
```

Ajout d'un nouvel onglet :

```text
Live
```

L'onglet permet de :

- créer une clé live ;
- régénérer la clé live ;
- afficher/masquer la clé ;
- copier la clé ;
- copier l'URL serveur RTMP ;
- préparer un live ;
- ouvrir la page live ;
- arrêter le live s'il est actif.

Informations affichées pour OBS :

```text
Serveur : rtmp://localhost:1935/live
Clé     : clé live utilisateur
```

## Contrôleur d'accueil

Fichier modifié :

```text
src/Controller/DefaultController.php
```

Ajouts :

- injection de `LiveStreamRepository` ;
- récupération des lives actifs ;
- récupération du live courant de l'utilisateur connecté ;
- passage de l'URL RTMP à la vue.

Données envoyées au template :

```php
'lives'
'currentUserLive'
'liveRtmpUrl'
```

## Configuration RTMP/HLS

Fichier ajouté :

```text
docker/rtmp/nginx.conf
```

Configuration importante :

```nginx
application live {
    live on;
    record off;

    hls on;
    hls_path /tmp/hls;
    hls_fragment 2s;
    hls_playlist_length 10s;
    hls_cleanup on;
    wait_key on;

    notify_method get;
    on_publish http://nginx/live/rtmp/publish;
    on_publish_done http://nginx/live/rtmp/publish-done;
}
```

Cela signifie :

- OBS publie en RTMP ;
- Nginx RTMP génère les fichiers HLS ;
- Symfony valide la clé au démarrage ;
- Symfony désactive le live à l'arrêt.

## Commandes et utilisation

### Démarrer le serveur RTMP/HLS

```bash
docker compose up -d rtmp
```

### Configuration OBS

Dans OBS :

```text
Service : Personnalisé
Serveur : rtmp://localhost:1935/live
Clé     : disponible dans Profil > Live
```

### URL de lecture HLS générée

```text
http://localhost:8081/hls/<clé>.m3u8
```

Exemple :

```text
http://localhost:8081/hls/sd_live_xxxxx.m3u8
```

## Tests effectués

### Docker

Vérification des conteneurs :

```bash
docker compose ps
```

Résultat : services PHP, Nginx, MariaDB et RTMP actifs.

### Santé du serveur HLS

Test :

```bash
curl -I http://localhost:8081/health
```

Résultat :

```text
HTTP/1.1 200 OK
```

### Validation clé invalide

Test d'un callback avec une fausse clé :

```text
/live/rtmp/publish?name=invalid-test-key
```

Résultat :

```text
403 Forbidden
invalid stream key
```

### Test RTMP réel

Flux test envoyé avec `ffmpeg` vers :

```text
rtmp://localhost:1935/live/<clé>
```

Résultat :

- le flux RTMP est accepté ;
- Symfony active le live ;
- Nginx génère le manifeste HLS ;
- le `.m3u8` est accessible en `200` pendant le flux ;
- Symfony désactive le live après arrêt.

### Test HLS

Pendant le flux :

```bash
curl http://localhost:8081/hls/<clé>.m3u8
```

Résultat : manifeste HLS valide avec segments `.ts`.

### Pages HTTP

Vérifiées en `200` :

```text
/
/lives
/live/{slug}
```

## Comportement final

Le fonctionnement complet est maintenant :

1. L'utilisateur ouvre son profil.
2. Il va dans l'onglet `Live`.
3. Il génère une clé live.
4. Il configure OBS avec :
   - serveur : `rtmp://localhost:1935/live`
   - clé : sa clé Starduste
5. Il prépare le live dans Starduste.
6. Il démarre le stream dans OBS.
7. Nginx RTMP appelle Symfony pour valider la clé.
8. Si la clé est valide :
   - le live devient actif ;
   - il apparaît sur l'accueil ;
   - il apparaît sur `/lives` ;
   - la page `/live/{slug}` lit le HLS.
9. Quand OBS s'arrête :
   - Nginx appelle Symfony ;
   - le live passe hors ligne ;
   - il disparaît de l'accueil.

## Limites actuelles

Le système est fonctionnel localement, mais certains points restent simples :

- le chat live est seulement front-end visuel, pas encore persistant ni temps réel ;
- le compteur de spectateurs est incrémenté à l'ouverture de la page mais pas décrémenté proprement ;
- pas encore de modération live ;
- pas encore de stockage VOD après live ;
- pas encore de transcodage multi-qualité ;
- pas encore de miniatures automatiques extraites du flux ;
- HLS est nettoyé automatiquement après arrêt, donc le manifeste disparaît quand le live est fini.
