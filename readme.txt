=== Hype.dev WordPress Post Replicator ===
Contributors: vitche
Tags: post replication, REST API, content management, AI integration, WordPress plugin
Requires at least: 5.0
Tested up to: 6.3
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily replicate posts or drafts from other WordPress blogs or specialized data sources, including AI content providers, using the Posts REST API.

== Description ==

The **Hype.dev WordPress Post Replicator** plugin is a powerful tool designed for content creators, developers, and marketers seeking to aggregate and manage content from multiple sources. This versatile plugin utilizes the REST API to import and replicate posts and drafts from any source that provides a Posts REST API, including other WordPress blogs and AI-driven content providers.

Whether you're managing multiple blogs or sourcing content from AI to enhance your digital marketing strategy, the Hype.dev WordPress Post Replicator plugin simplifies content replication and management processes, eliminating the need for complex coding and ensuring secure, customizable operation.

== Installation ==

1. Upload the `wordpress-plugin-post-replicator` directory to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the installation script provided in the project:
   ```shell
   mkdir -p "wp-content/plugins/wordpress-plugin-post-replicator" && curl -o "wp-content/plugins/wordpress-plugin-post-replicator/index.php" "https://raw.githubusercontent.com/vitche/wordpress-plugin-post-replicator/refs/heads/main/index.php" && echo "Downloaded index.php to wp-content/plugins/wordpress-plugin-post-replicator"
   ```
   Alternatively, run the [install.sh](./install.sh) script in the root of your WordPress project.

== Frequently Asked Questions ==

= How does the Hype.dev WordPress Post Replicator plugin work? =

The plugin uses the REST API to fetch and replicate posts and drafts from any supported source, ensuring seamless integration with existing content management workflows.

= Can I use this plugin with AI content providers? =

Yes, you can use this plugin with AI content providers as long as they offer a Posts REST API.

= Is coding necessary to use this plugin? =

No, the Hype.dev WordPress Post Replicator plugin is designed to be user-friendly and requires no coding experience for basic operations.

== Screenshots ==

1. Screenshot of the plugin interface or a relevant feature.
2. Image showing integration with different REST API sources.

== Changelog ==

= 1.0.0 =
* Initial release of Hype.dev WordPress Post Replicator.
* Added basic post replication functionality from WordPress blogs and AI sources via REST API.

== Upgrade Notice ==

= 1.0.0 =
Initial release - enjoy seamless content replication across multiple platforms.

== Arbitrary section ==

For more information and support, contact [developers@vitche.com](mailto:developers@vitche.com) or visit our GitHub repository: https://github.com/vitche/wordpress-plugin-post-replicator.

== Acknowledgements ==

This plugin is maintained by the Vitche Research Team. We thank our contributors and users for their support and feedback.
