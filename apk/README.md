# Branche APK NexChat

Cette branche contient la préparation Android séparée du site Alwaysdata.

## Génération du projet Android

```bash
npm install
npm run apk:init
npm run apk:sync
npm run apk:open
```

Android Studio permettra ensuite de générer un APK de test ou un AAB de production.

## Connexion à l'API NexChat

L'APK utilise l'API distante `https://nexchat.alwaysdata.net/php/api/` quand l'application ne tourne pas directement depuis le domaine web. La clé Groq reste côté serveur dans `.env` / variables Alwaysdata et n'est jamais embarquée dans l'APK.

## À faire avant publication

- Ajouter des icônes Android via Android Studio.
- Changer `NEXCHAT_JWT_SECRET` en production.
- Vérifier le serveur PeerJS HTTPS sur `nexchat.alwaysdata.net/peerjs`.
- Générer une clé de signature Android privée hors du dépôt.


## GitHub Actions

Le workflow `.github/workflows/android-apk.yml` construit automatiquement un APK debug quand la branche `nexchat-apk` est poussée ou lancée manuellement. L'artefact s'appelle `nexchat-debug-apk`.

Le build prépare d'abord `mobile-web/` avec seulement les fichiers web nécessaires, puis génère le projet Android Capacitor et exécute `./gradlew assembleDebug`.


## Java 21

Le build APK utilise maintenant Java 21, car Capacitor Android compile avec `source release: 21`.
