# Déploiement NexChat sur Alwaysdata

1. Créer la base MySQL `nexchat_index` et importer `database/schema.sql`.
2. Déployer `index.html`, `assets/`, `manifest.webmanifest`, `php/`, `database/` et `docs/` directement dans le `www/` Alwaysdata.
3. Les endpoints doivent rester accessibles via `/php/api/` depuis la racine du site.
4. Définir les variables d'environnement PHP : `NEXCHAT_DB_HOST`, `NEXCHAT_DB_NAME`, `NEXCHAT_DB_USER`, `NEXCHAT_DB_PASS`, `NEXCHAT_JWT_SECRET`, `GROQ_API_KEY`.
5. Configurer un serveur PeerJS sécurisé sur `nexchat.alwaysdata.net` avec le chemin `/peerjs`, ou adapter `assets/app.js`.
6. Forcer HTTPS: les appels audio via `getUserMedia` et PeerJS doivent être servis en contexte sécurisé.

## Sécurité minimale incluse

- Mots de passe hashés avec `password_hash`.
- Jetons signés HMAC côté PHP.
- Requêtes PDO préparées.
- Clé Groq uniquement côté serveur, jamais dans le JavaScript public.

Avant production, remplacez le jeton maison par une vraie librairie JWT auditée et limitez précisément les origines CORS.
