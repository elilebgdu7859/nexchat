# NexChat

NexChat est une base d'application de messagerie inspirée de WhatsApp, pensée pour le web mobile/PWA plutôt qu'un APK compilé. Elle inclut des comptes, des conversations stockées dans MySQL (`nexchat_index`), une interface responsive, un backend PHP et une intégration Groq pour l'assistant NexIA.

## Structure

- `public/` : application web/PWA à déposer sur le site.
- `php/api/` : endpoints PHP JSON pour auth, messages, IA et PeerJS.
- `php/config/config.php` : configuration par variables d'environnement.
- `database/schema.sql` : schéma MySQL de la base `nexchat_index`.
- `docs/deployment.md` : notes de déploiement Alwaysdata.

## Variables d'environnement

```bash
NEXCHAT_DB_HOST=localhost
NEXCHAT_DB_NAME=nexchat_index
NEXCHAT_DB_USER=...
NEXCHAT_DB_PASS=...
NEXCHAT_JWT_SECRET=une-longue-valeur-secrete
GROQ_API_KEY=gsk_...
GROQ_MODEL=llama-3.1-70b-versatile
```

## Démarrage local rapide

1. Importer `database/schema.sql` dans MySQL.
2. Configurer les variables d'environnement ou modifier temporairement `php/config/config.php`.
3. Lancer un serveur PHP depuis la racine :

```bash
php -S localhost:8080
```

4. Ouvrir `http://localhost:8080/public/`.

## Idées NexIA à poursuivre

- Résumés automatiques de discussions longues.
- Réponses suggérées selon le ton du contact.
- Traduction instantanée.
- Détection de messages urgents.
- Mémoire personnelle contrôlée par l'utilisateur dans `ai_memories`.
