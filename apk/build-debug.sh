#!/usr/bin/env bash
set -euo pipefail
npm install --no-audit --no-fund
npm run apk:prepare-web
if [ ! -d android ]; then
  npx cap add android
fi
npx cap sync android
cd android
./gradlew assembleDebug
