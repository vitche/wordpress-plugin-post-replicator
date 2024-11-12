<?php
/*
Plugin Name: Hype.dev WordPress Replicator
Description: A plugin to import drafts and posts into WordPress through an external REST API.
Version: 1.1
Author: Vitche Research Team Developer
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

    // Extract and process tags
    $tag_ids = array();
    if (isset($post_data['tags']) && !empty($post_data['tags'])) {
        $tags = $post_data['tags']; // Assuming this is an array of tag names or IDs

        // If tags are provided as IDs, you might need to get their names
        // If tags are provided as names, proceed to process them
        if (!is_array($tags)) {
            // If tags are provided as a comma-separated string, convert to array
            $tags = explode(',', $tags);
        }

        // Ensure tags are sanitized
        $tags = array_map('sanitize_text_field', $tags);

        foreach ($tags as $tag_name) {
            $tag_name = trim($tag_name);
            if (!empty($tag_name)) {
                $tag = get_term_by('name', $tag_name, 'post_tag');
                if (!$tag) {
                    // Tag doesn't exist, create it
                    $tag = wp_insert_term($tag_name, 'post_tag');
                    if (!is_wp_error($tag)) {
                        $tag_ids[] = $tag['term_id'];
                    } else {
                        error_log('Failed to insert tag: ' . $tag_name . ' - ' . $tag->get_error_message());
                    }
                } else {
                    $tag_ids[] = $tag->term_id;
                }
            }
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
        'post_category' => array(1),
        'tax_input'     => array(
            'post_tag' => $tag_ids,
        ),
    );

    // Avoid duplicate posts
    $existing_post = get_page_by_path($slug, OBJECT, 'post');
    if (!$existing_post) {

        $post_id = wp_insert_post($new_post);

        if (!is_wp_error($post_id)) {
            // Attach media if available
            if (!empty($post_data['image_url'])) {
                attach_media_to_post($post_data['image_url'], $post_id);
            }
            // Log success
            error_log('Post inserted successfully with ID: ' . $post_id);
        } else {
            error_log('Failed to insert post: ' . $post_id->get_error_message());
        }
    } else {
        error_log('Post already exists: ' . $new_post['post_title']);
    }
}

// Register the plugin settings
add_action('admin_init', 'hype_register_settings');

function hype_register_settings() {
    register_setting('hype_settings_group', 'hype_rest_api_endpoint', 'esc_url_raw');
    register_setting('hype_settings_group', 'hype_post_status', 'sanitize_text_field');
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
