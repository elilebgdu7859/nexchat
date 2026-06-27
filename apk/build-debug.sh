#!/usr/bin/env bash
set -euo pipefail
npm install
npm run apk:init || true
npm run apk:sync
cd android
./gradlew assembleDebug
