#!/bin/bash
# Authorized Pentest Deployment
mkdir -p data
chmod 755 data
chmod 644 *.html *.php
chmod +x deploy.sh
echo "🚀 Pentest phishing kit deployed!"
echo "Admin: save_data.php (pass: pentest_admin_2026_hackerai)"
echo "Data stored in: data/"
echo "Test: curl -X POST save_data.php"