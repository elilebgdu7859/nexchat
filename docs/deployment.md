# Déploiement NexChat sur Alwaysdata

1. Créer la base MySQL `nexchat_index` et importer `database/schema.sql`.
2. Déployer `public/` comme racine web ou sous-dossier public.
3. Déployer `php/` à côté de `public/` pour conserver les endpoints accessibles via `../php/api/`.
4. Définir les variables d'environnement PHP : `NEXCHAT_DB_HOST`, `NEXCHAT_DB_NAME`, `NEXCHAT_DB_USER`, `NEXCHAT_DB_PASS`, `NEXCHAT_JWT_SECRET`, `GROQ_API_KEY`.
5. Configurer un serveur PeerJS sécurisé sur `nexchat.alwaysdata.net` avec le chemin `/peerjs`, ou adapter `public/assets/app.js`.
6. Forcer HTTPS: les appels audio via `getUserMedia` et PeerJS doivent être servis en contexte sécurisé.

## Sécurité minimale incluse

- Mots de passe hashés avec `password_hash`.
- Jetons signés HMAC côté PHP.
- Requêtes PDO préparées.
- Clé Groq uniquement côté serveur, jamais dans le JavaScript public.

Avant production, remplacez le jeton maison par une vraie librairie JWT auditée et limitez précisément les origines CORS.
