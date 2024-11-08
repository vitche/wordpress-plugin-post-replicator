#!/bin/bash
mkdir -p "wp-content/plugins/wordpress-plugin-post-replicator" && curl -o "wp-content/plugins/wordpress-plugin-post-replicator/index.php" "https://raw.githubusercontent.com/vitche/wordpress-plugin-post-replicator/refs/heads/main/index.php" && echo "Downloaded index.php to wp-content/plugins/wordpress-plugin-post-replicator"
