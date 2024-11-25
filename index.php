<?php
/*
Plugin Name: Hype.dev WordPress Content Replicator
Plugin URI: https://github.com/vitche/wordpress-plugin-post-replicator
Description: Seamlessly import and synchronize drafts and posts into your WordPress site from external REST APIs. Enhance your content strategy with automated content replication and keep your website up-to-date effortlessly.
Version: 1.3.2
Author: Vitche Research Team
Author URI: https://hype.dev
Text Domain: hype-replicator
Domain Path: /languages
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.2
Requires at least: 5.0
Tested up to: 6.3
Stable tag: trunk
Tags: content importer, REST API integration, content replication, automated publishing, WordPress automation, external API, content synchronization
*/


// Add a custom interval of every minute
add_filter('cron_schedules', 'add_custom_cron_intervals');

function add_custom_cron_intervals($schedules) {
    $schedules['every_minute'] = array(
        'interval' => 60, // 60 seconds = 1 minute
        'display'  => __('Every Minute', 'hype-replicator')
    );
    return $schedules;
}

// Schedule the event on plugin activation
register_activation_hook(__FILE__, 'schedule_post_import');
function schedule_post_import() {
    if (!wp_next_scheduled('fetch_external_posts_hook')) {
        wp_schedule_event(time(), 'every_minute', 'fetch_external_posts_hook');
    }
}

// Clear the scheduled event on plugin deactivation
register_deactivation_hook(__FILE__, 'clear_post_import_schedule');
function clear_post_import_schedule() {
    $timestamp = wp_next_scheduled('fetch_external_posts_hook');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'fetch_external_posts_hook');
    }
}

// Hook your function to the scheduled event
add_action('fetch_external_posts_hook', 'fetch_external_posts');

// Execute this extion from an external activation source
add_action('init', function() {
    if (isset($_GET['fetch_external_posts_hook']) && $_GET['fetch_external_posts_hook'] === 'run') {
        do_action('fetch_external_posts_hook');
        exit('Hook executed');
    }
});

function fetch_external_posts() {

    // Retrieve the REST API endpoint from settings
    $api_endpoint = get_option('hype_rest_api_endpoint');

    if (empty($api_endpoint)) {
        error_log('Hype.dev Replicator: REST API endpoint is not set.');
        return;
    }

    $response = wp_remote_get($api_endpoint);

    if (is_wp_error($response)) {
        error_log('Failed to fetch posts: ' . $response->get_error_message());
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $posts = json_decode($body, true);

    if (empty($posts)) {
        error_log('No posts found in the API response.');
        return;
    }

    foreach ($posts as $post_data) {
        insert_post_into_wordpress($post_data);
    }
}

function insert_post_into_wordpress($post_data) {

    // Log the post data for debugging
    error_log('Post Data: ' . print_r($post_data, true));

    // Extract and sanitize the title
    $title = '';
    if (isset($post_data['title'])) {
        if (is_array($post_data['title'])) {
            $title = isset($post_data['title']['rendered']) ? $post_data['title']['rendered'] : '';
        } else {
            $title = $post_data['title'];
        }
    }

    // Log the title for debugging
    error_log('Post Title: ' . $title);

    // Extract and sanitize the slug
    $slug = '';
    if (isset($post_data['slug'])) {
        $slug = sanitize_title($post_data['slug']);
    }

    // Extract and sanitize the content
    $content = '';
    if (isset($post_data['content'])) {
        if (is_array($post_data['content'])) {
            $content = isset($post_data['content']['rendered']) ? $post_data['content']['rendered'] : '';
        } else {
            $content = $post_data['content'];
        }
    }

    // Log the content for debugging
    error_log('Post Content: ' . substr($content, 0, 100) . '...'); // Log first 100 characters

    // Get the post status from settings
    $post_status = get_option('hype_post_status', 'draft'); // Default to 'draft' if not set

    // Get the category slug from settings
    $category_slug = get_option('hype_post_category', '');

    // Get the category ID from the slug
    $category_id = 1; // Default to 'Uncategorized' if not found
    if (!empty($category_slug)) {
        $category = get_category_by_slug($category_slug);
        if ($category) {
            $category_id = $category->term_id;
        }
    }

    // Prepare post data
    $new_post = array(
        'post_name'     => $slug,
        'post_title'    => sanitize_text_field($title),
        'post_content'  => wp_kses_post($content),
        'post_status'   => $post_status,
        // The `get_current_user_id()` may return `0` in the CRON. So, using the default author.
        'post_author'   => 1,
        'post_category' => array($category_id)
    );

    // Avoid duplicate posts
    $existing_post = get_page_by_path($slug, OBJECT, 'post');
    if (!$existing_post) {

        $post_id = wp_insert_post($new_post);

        if (!is_wp_error($post_id)) {

            // Handle tags
            if (isset($post_data['tags']) && !empty($post_data['tags'])) {

                $tags = is_array($post_data['tags']) ? $post_data['tags'] : explode(',', $post_data['tags']);
                $tags = array_map('trim', array_map('sanitize_text_field', $tags));

                // Create and assign tags
                $tag_ids = array();
                foreach ($tags as $tag_name) {
                    if (!empty($tag_name)) {
                        $tag = get_term_by('name', $tag_name, 'post_tag');
                        if (!$tag) {
                            $new_tag = wp_insert_term($tag_name, 'post_tag');
                            if (!is_wp_error($new_tag)) {
                                $tag_ids[] = (int)$new_tag['term_id'];
                            }
                        } else {
                            $tag_ids[] = (int)$tag->term_id;
                        }
                    }
                }

                if (!empty($tag_ids)) {
                    wp_set_post_tags($post_id, $tag_ids, false);
                }
            }

            // Handle featured image
            if (!empty($post_data['image_url'])) {
                attach_media_to_post($post_data['image_url'], $post_id);
            }
        }
        return $post_id;
    }

    return false;
}

// Register the plugin settings
add_action('admin_init', 'hype_register_settings');

function hype_register_settings() {
    register_setting('hype_settings_group', 'hype_rest_api_endpoint', 'esc_url_raw');
    register_setting('hype_settings_group', 'hype_post_status', 'sanitize_text_field');
    register_setting('hype_settings_group', 'hype_post_category', 'sanitize_text_field'); // New setting for category slug
}

// Add a settings page under the Settings menu
add_action('admin_menu', 'hype_add_settings_page');

function hype_add_settings_page() {
    add_options_page(
        __('Hype.dev Replicator Settings', 'hype-replicator'),
        __('Hype Replicator', 'hype-replicator'),
        'manage_options',
        'hype-replicator-settings',
        'hype_render_settings_page'
    );
}

function hype_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Hype.dev Replicator Settings', 'hype-replicator'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('hype_settings_group');
            do_settings_sections('hype_replicator');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="hype_rest_api_endpoint"><?php esc_html_e('REST API Endpoint URL', 'hype-replicator'); ?></label></th>
                    <td>
                        <input type="url" id="hype_rest_api_endpoint" name="hype_rest_api_endpoint" value="<?php echo esc_attr(get_option('hype_rest_api_endpoint')); ?>" class="regular-text ltr" required />
                        <p class="description"><?php esc_html_e('Enter the full URL of the REST API endpoint. For example:', 'hype-replicator'); ?> <code>https://example.com/wp-json/wp/v2/posts</code></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Publish Posts Immediately', 'hype-replicator'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php esc_html_e('Publish Posts Immediately', 'hype-replicator'); ?></span></legend>
                            <label for="hype_post_status">
                                <input name="hype_post_status" type="checkbox" id="hype_post_status" value="publish" <?php checked(get_option('hype_post_status'), 'publish'); ?> />
                                <?php esc_html_e('Yes, publish posts immediately (otherwise they will be saved as drafts).', 'hype-replicator'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="hype_post_category"><?php esc_html_e('Default Post Category Slug', 'hype-replicator'); ?></label></th>
                    <td>
                        <input type="text" id="hype_post_category" name="hype_post_category" value="<?php echo esc_attr(get_option('hype_post_category')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter the slug of the category to assign to imported posts. For example:', 'hype-replicator'); ?> <code>news</code></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add settings link on plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'hype_add_settings_link');

function hype_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=hype-replicator-settings">' . __('Settings', 'hype-replicator') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
