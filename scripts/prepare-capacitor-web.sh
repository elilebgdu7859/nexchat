#!/usr/bin/env bash
set -euo pipefail
rm -rf mobile-web
mkdir -p mobile-web/assets
cp index.html manifest.webmanifest mobile-web/
cp assets/app.js assets/styles.css mobile-web/assets/
