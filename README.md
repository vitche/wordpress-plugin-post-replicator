# `Hype.dev` WordPress Post Replicator Plug-in
**`Hype.dev` WordPress POST Replicator** is a versatile plugin that imports and replicates posts or drafts using REST API from various sources. Ideal for content creators, developers, and marketers, it aggregates content from multiple WordPress blogs and AI-driven providers. Easy to use without coding, it ensures secure, customizable content management.

![Hype.dev WordPress POST Replicator](replicator.png)

## Features

- **Seamless Content Replication**: Import posts or drafts from WordPress blogs or specialized AI content providers using the REST API.
- **No Coding Required**: User-friendly interface allows you to set up and manage content replication without any programming knowledge.
- **Secure and Customizable**: Manage content securely with customizable settings to fit your content strategy.

## Installation

### Download the Plugin

[![Download Plugin](https://img.shields.io/badge/Download%20Plugin-ZIP-blue?style=for-the-badge&logo=github)](https://github.com/vitche/wordpress-plugin-post-replicator/archive/refs/heads/main.zip)

Click the **Download Plugin** button to download the latest version of the plugin as a ZIP file.

### Install via WordPress Admin Dashboard

1. **Navigate to Plugins**: In your WordPress admin dashboard, go to **Plugins > Add New**.
2. **Upload Plugin**: Click on the **Upload Plugin** button at the top of the page.
3. **Choose File**: Select the ZIP file you downloaded (`wordpress-plugin-post-replicator-main.zip`).
4. **Install Now**: Click **Install Now** to upload and install the plugin.
5. **Activate Plugin**: Once installed, click **Activate Plugin** to enable it on your site.

### Install via FTP or File Manager

1. **Download and Extract**: Download the plugin ZIP file and extract it. You will get a folder named `wordpress-plugin-post-replicator-main`.
2. **Rename Folder (Optional)**: Rename the folder to `wordpress-plugin-post-replicator` for consistency.
3. **Upload to Server**: Upload the `wordpress-plugin-post-replicator` folder to your WordPress installation's `/wp-content/plugins/` directory.
4. **Activate Plugin**: In your WordPress admin dashboard, navigate to **Plugins**, find **Hype.dev WordPress Post Replicator**, and click **Activate**.

### Install via Command Line

Alternatively, you can install the plugin using the command line in your WordPress project root folder:

```shell
mkdir -p "wp-content/plugins/wordpress-plugin-post-replicator" && \
curl -L -o "wp-content/plugins/wordpress-plugin-post-replicator/index.php" \
"https://raw.githubusercontent.com/vitche/wordpress-plugin-post-replicator/main/index.php" && \
echo "Downloaded index.php to wp-content/plugins/wordpress-plugin-post-replicator"
```

**Note**: This command creates the plugin directory and downloads the `index.php` file directly from the GitHub repository.

### Install via Provided Script

You can also run the [install.sh](./install.sh) script provided in the project:

```shell
sh install.sh
```

This script automates the installation process for you.

## Frequently Asked Questions

### How does the Hype.dev WordPress Post Replicator plugin work?

The plugin uses the REST API to fetch and replicate posts and drafts from supported sources, ensuring seamless integration with your existing content management workflows.

### Can I use this plugin with AI content providers?

Yes, the plugin supports any AI content provider that offers a Posts REST API.

### Is coding necessary to use this plugin?

No, the Hype.dev WordPress Post Replicator plugin is designed to be user-friendly and requires no coding experience for basic operations.

## Support and Contact

- **Email**: For more information and support, contact us at [developers@vitche.com](mailto:developers@vitche.com).
- **GitHub**: Visit our [GitHub repository](https://github.com/vitche/wordpress-plugin-post-replicator) for source code, issues, and contributions.
- **Website**: Learn more about our projects at [hype.dev](https://hype.dev).

## Acknowledgements

This plugin is maintained by the Vitche Research Team. We thank our contributors and users for their support and feedback.

## Changelog

### **1.2.0**

- Added ability to import tags from the REST API.
- Introduced settings to specify the default category by slug.
- Enhanced plugin header for better SEO and user clarity.

### **1.0.0**

- Initial release of Hype.dev WordPress Post Replicator.
- Basic post replication functionality from WordPress blogs and AI sources via REST API.

---

Feel free to contribute to the project by submitting issues or pull requests. Your feedback helps us improve and add new features.

---
