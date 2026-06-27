# Préparer l'APK NexChat

Le site reste à la racine du `www/` Alwaysdata (`index.html`, `assets/`, `manifest.webmanifest`). Pour Android, le projet est prêt pour Capacitor.

## Commandes

```bash
npm install
npm run apk:init
npm run apk:sync
npm run apk:open
```

Dans Android Studio, générer ensuite l'APK ou l'AAB.

## API utilisée par l'APK

Le JavaScript utilise automatiquement `https://nexchat.alwaysdata.net/php/api/` quand l'application tourne hors du site web classique. Pour forcer une autre API pendant les tests :

```js
localStorage.nexchat_api_base = 'https://ton-domaine/php/api/';
```

La clé Groq reste toujours côté serveur dans `GROQ_API_KEY`.
