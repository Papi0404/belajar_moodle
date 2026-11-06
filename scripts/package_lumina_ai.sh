#!/usr/bin/env bash
# ==============================================================================
# Lumina AI Chatbot Local Plugin Packager
# Generates a standard Moodle-compliant zip archive ready for plugin installation.
# Component: local_lumina_ai
# Target root in archive: lumina_ai/
# ==============================================================================

set -e

python3 -c "
import os, zipfile

src_dir = '/var/www/html/moodle/public/local/lumina_ai'
target_zips = [
    '/var/www/html/moodle/plugins_backup/local_lumina_ai.zip',
    '/var/www/html/moodle/local_lumina_ai.zip'
]

for target_zip in target_zips:
    os.makedirs(os.path.dirname(target_zip), exist_ok=True)
    with zipfile.ZipFile(target_zip, 'w', zipfile.ZIP_DEFLATED) as zf:
        for root, dirs, files in os.walk(src_dir):
            for file in files:
                if file.startswith('.') or file.endswith('.zip'):
                    continue
                full_path = os.path.join(root, file)
                rel_path = os.path.relpath(full_path, src_dir)
                archive_name = os.path.join('lumina_ai', rel_path)
                zf.write(full_path, archive_name)

print('==> Lumina AI Chatbot plugin packaged successfully!')
for target_zip in target_zips:
    size = os.path.getsize(target_zip) / 1024
    print(f'    - {target_zip} ({size:.1f} KB)')
"

