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
