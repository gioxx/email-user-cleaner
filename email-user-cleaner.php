<?php
/*
Plugin Name:       E-mail User Cleaner
Plugin URI:        https://go.gioxx.org/emailusercleaner
Description:       Delete users corresponding to the specified email addresses.
Version:           1.5
Author:            Gioxx
Author URI:        https://gioxx.org
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

// WordPress admin menu item
add_action('admin_menu', 'euc_admin_menu');

function euc_admin_menu() {
    add_menu_page(
        'E-mail User Cleaner',
        'E-mail User Cleaner',
        'manage_options',
        'email-user-cleaner',
        'euc_admin_page',
        'dashicons-superhero'
    );
}

// Enqueue styles
function euc_enqueue_styles() {
    wp_enqueue_style(
        'euc-styles',
        plugins_url('email-user-cleaner.css', __FILE__),
        array(),
        '1.5'
    );
}
add_action('admin_enqueue_scripts', 'euc_enqueue_styles');

// Show the plugin page
function euc_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'email-user-cleaner'), 403);
    }
    
    $plugin_data = get_plugin_data(__FILE__);
    $plugin_name = esc_html($plugin_data['Name']);
    $plugin_version = esc_html($plugin_data['Version']);

    ?>
    <div class="wrap">
        <h2><span class="dashicons dashicons-email-alt"></span> <?php echo esc_html($plugin_name); ?></h2>

        <?php
            // Check plugin Import and export users and customers
            if (is_plugin_active('import-users-from-csv-with-meta/import-users-from-csv-with-meta.php')) { ?>
                <div class="euc_info-box">
                    <div class="euc_info-icon">ℹ️</div>
                    <div class="euc_info-message">
                        <?php 
                        echo wp_kses(
                            sprintf(
                                __('Import and export users and customers detected: <a href="%1$s">Go to export page</a> or <strong>directly export all users from WordPress (in a CSV file) <a href="%2$s">from here</a></strong>.', 'email-user-cleaner'),
                                esc_url(admin_url('tools.php?page=acui&tab=export')),
                                esc_url(admin_url('admin.php?action=euc_export_users_csv'))
                            ),
                            array('a' => array('href' => array()), 'strong' => array())
                        );
                        ?>
                        <div id="suggested_columns" style="padding-top: 7px;">
                            <?php esc_html_e('Suggested columns to export:', 'email-user-cleaner'); ?>
                            <br><span style="padding-left: 5px;"><code>user_login,user_email,display_name,role,first_name,last_name</code></span>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="euc_info-box">
                    <div class="euc_info-icon">ℹ️</div>
                    <div class="euc_info-message">
                        <?php 
                        echo wp_kses(
                            sprintf(
                                __('If you want to export all users from WordPress (in a CSV file) <a href="%s">you can click here</a>.', 'email-user-cleaner'),
                                esc_url(admin_url('admin.php?action=euc_export_users_csv'))
                            ),
                            array('a' => array('href' => array()))
                        );
                        ?>
                    </div>
                </div>
            <?php }
        ?>

        <div class="euc_warning-box">
            <div class="euc_warning-icon">!</div>
            <div class="euc_warning-message">
                <?php esc_html_e('This operation cannot be undone.', 'email-user-cleaner'); ?>
                <br /><strong><?php esc_html_e('Backup your database and proceed with caution', 'email-user-cleaner'); ?></strong>.
            </div>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('euc_delete_users_nonce', '_wpnonce'); ?>
            <p><?php esc_html_e('Specify email addresses (one per line), click the button to start the deletion:', 'email-user-cleaner'); ?></p>
            <textarea name="emails" class="euc_textarea" rows="10"></textarea>
            <input type="submit" name="submit" value="<?php esc_attr_e('Delete Users', 'email-user-cleaner'); ?>" class="button-primary takespace" />

            <p class="euc_results">
                <?php
                    // Messages output
                    euc_delete_users();
                ?>
            </p>
        </form>

        <div class="footer" style="padding-top: 35px;">
            <hr>
            <span class="dashicons dashicons-hammer"></span> Gioxx, <?php echo esc_html(date('Y')); ?> &#x2022; <span class="dashicons dashicons-admin-home"></span> <a href="<?php echo esc_url('https://go.gioxx.org/emailusercleaner'); ?>">Gioxx.org</a> &#x2022; <span class="dashicons dashicons-media-code"></span> <a href="<?php echo esc_url('https://github.com/gioxx/email-user-cleaner'); ?>">GitHub</a>
        </div>
    </div>

    <?php
}

// Export all users in a CSV file
function euc_export_users_csv() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Access denied', 'email-user-cleaner'), 403);
    }

    // Sets the header of the CSV file.
    $csv_header = array(
        __('User Login', 'email-user-cleaner'),
        __('Email', 'email-user-cleaner'),
        __('Display Name', 'email-user-cleaner'),
        __('Role', 'email-user-cleaner'),
        __('First Name', 'email-user-cleaner'),
        __('Last Name', 'email-user-cleaner')
    );

    // Get all WordPress users
    $users = get_users();

    $csv_content = '';
    $csv_content .= '"' . implode('","', array_map('esc_html', $csv_header)) . '"' . "\n";

    // Cycle over all users and add their information to the contents of the CSV file
    foreach ($users as $user) {
        $user_info = array(
            sanitize_user($user->user_login),
            sanitize_email($user->user_email),
            sanitize_text_field($user->display_name),
            sanitize_text_field(implode(', ', $user->roles)),
            sanitize_text_field(get_user_meta($user->ID, 'first_name', true)),
            sanitize_text_field(get_user_meta($user->ID, 'last_name', true))
        );
        $csv_content .= '"' . implode('","', array_map('esc_html', $user_info)) . '"' . "\n";
    }

    // Send HTTP header to indicate that you are sending a CSV file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=wp_users_export.csv');
    
    // Output the CSV content
    echo wp_kses_post($csv_content);
    exit;
}

// Manage user deletion
function euc_delete_users() {
    if (isset($_POST['submit'])) {
        // Verify nonce
        if (!check_admin_referer('euc_delete_users_nonce')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'email-user-cleaner'));
        }
        
        // Verify that the user has the necessary permissions
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Access denied', 'email-user-cleaner'));
        }
        
        // Get email addresses specified
        $emails = isset($_POST['emails']) ? sanitize_textarea_field($_POST['emails']) : '';

        // Split email addresses into an array
        $emails_array = array_map('trim', explode("\n", $emails));
        $emails_array = array_filter($emails_array, 'is_email');

        // Get current user's email
        $current_user = wp_get_current_user();
        $current_user_email = $current_user->user_email;

        // Output messages
        $success_messages = [];
        $error_messages = [];

        // Cycles over email addresses and deletes matching users
        foreach ($emails_array as $email) {
            // Skip if the email matches the current user's email
            if ($email === $current_user_email) {
                $error_messages[] = sprintf(esc_html__('Skipped deleting your own account (%s) for safety.', 'email-user-cleaner'), $email);
                continue;
            }

            $user = get_user_by('email', $email);
            if ($user) {
                $deleted = wp_delete_user($user->ID);
                if ($deleted) {
                    $success_messages[] = sprintf(esc_html__('User with email %s successfully deleted!', 'email-user-cleaner'), $email);
                } else {
                    $error_messages[] = sprintf(esc_html__('Error while deleting user with email %s!', 'email-user-cleaner'), $email);
                }
            } else {
                $error_messages[] = sprintf(esc_html__('There are no users with email %s!', 'email-user-cleaner'), $email);
            }
        }

        // Messages output
        if (!empty($success_messages)) {
            echo '<div class="euc_success-box">';
            foreach ($success_messages as $message) {
                echo '<span class="dashicons dashicons-yes"></span>' . esc_html($message) . '<br />';
            }
            echo '</div>';
        }

        if (!empty($error_messages)) {
            echo '<div class="euc_error-box">';
            foreach ($error_messages as $message) {
                echo '<span class="dashicons dashicons-no-alt"></span>' . esc_html($message) . '<br />';
            }
            echo '</div>';
        }
    }
}

// Add user deletion function to admin_init hook
add_action('admin_action_euc_export_users_csv', 'euc_export_users_csv');