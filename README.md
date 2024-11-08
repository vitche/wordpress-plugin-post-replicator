# `Hype.dev` WordPress Post Replicator Plug-in
**`Hype.dev` WordPress POST Replicator** is a versatile plugin that imports and replicates posts or drafts using REST API from various sources. Ideal for content creators, developers, and marketers, it aggregates content from multiple WordPress blogs and AI-driven providers. Easy to use without coding, it ensures secure, customizable content management.

<img src="replicator.png" alt="Hype.dev WordPress POST Replicator" />

## Features

- Replicate posts or drafts from WordPress blogs or specialized AI content providers using the Posts REST API.
- No coding necessary for basic operations.
- Secure and customizable content management.

## Installation
You can:
1. Upload the `wordpress-plugin-post-replicator` directory to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

Or you can:
1. Use the following installation script in your WordPress project root to simplify installation:
2. Activate the plugin through the 'Plugins' screen in WordPress.

Or, even run the following in your `WordPress` project root folder:
```shell
mkdir -p "wp-content/plugins/wordpress-plugin-post-replicator" && curl -o "wp-content/plugins/wordpress-plugin-post-replicator/index.php" "https://raw.githubusercontent.com/vitche/wordpress-plugin-post-replicator/refs/heads/main/index.php" && echo "Downloaded index.php to wp-content/plugins/wordpress-plugin-post-replicator"
```

Alternatively, you can run the [install.sh](./install.sh) script provided in the project.

## Frequently Asked Questions

### How does the Hype.dev WordPress Post Replicator plugin work?

The plugin uses the REST API to fetch and replicate posts and drafts from any supported source, ensuring seamless integration with existing content management workflows.

### Can I use this plugin with AI content providers?

Yes, the plugin supports any AI content provider that offers a Posts REST API.

### Is coding necessary to use this plugin?

No, the Hype.dev WordPress Post Replicator plugin is designed to be user-friendly and requires no coding experience for basic operations.

## Support and Contact

For more information and support, contact [developers@vitche.com](mailto:developers@vitche.com) or visit our [GitHub repository](https://github.com/vitche/wordpress-plugin-post-replicator).

## Acknowledgements

This plugin is maintained by the Vitche Research Team. We thank our contributors and users for their support and feedback.

## Changelog

**1.0.0**
- Initial release of Hype.dev WordPress Post Replicator.
- Added basic post replication functionality from WordPress blogs and AI sources via REST API.
