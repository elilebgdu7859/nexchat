# Télécharger l'APK depuis GitHub Actions

Quand cette branche est poussée sur GitHub, le workflow **Build NexChat Android APK** se lance automatiquement grâce à `.github/workflows/android-apk.yml`.

## Où cliquer sur GitHub

1. Ouvre le dépôt GitHub `elilebgdu7859/nexchat`.
2. Clique sur l'onglet **Actions** en haut du dépôt, pas seulement sur la page des commits.
3. Clique sur **Build NexChat Android APK**.
4. Ouvre le dernier run vert.
5. Descends dans **Artifacts**.
6. Télécharge **NexChat-debug.apk**.
7. Sur Android, autorise l'installation depuis le navigateur/fichier si demandé, puis installe l'APK.

## Lancer manuellement

Dans **Actions → Build NexChat Android APK**, clique **Run workflow**. Choisis la branche qui contient ce fichier, puis valide. Le workflow peut aussi se lancer à chaque `push` et sur chaque pull request.

## Important

Si tu ne vois pas l'onglet Actions ou le bouton **Run workflow**, il faut d'abord pousser/merger cette branche sur GitHub avec le dossier `.github/workflows/`. GitHub n'affiche pas un workflow qui n'existe pas encore sur la branche distante.
