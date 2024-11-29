<?php
/*
Plugin Name:       E-mail User Cleaner
Plugin URI:        https://go.gioxx.org/emailusercleaner
Description:       Delete users corresponding to the specified email addresses, but also search for duplicate users.
Version:           1.6
Author:            Gioxx
Author URI:        https://gioxx.org
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Add the plugin to the WordPress admin menu.
 *
 * This function adds an item in the WordPress admin menu, which
 * will lead to the plugin's main page, containing navigation tabs.
 *
 * @since 1.5
 */
function euc_admin_menu() {
    add_menu_page(
        'E-mail User Cleaner',
        'E-mail User Cleaner',
        'manage_options',
        'email-user-cleaner',
        'euc_admin_page', // Show the main page with navigation tabs
        'dashicons-superhero'
    );
}
add_action('admin_menu', 'euc_admin_menu');

/**
 * Enqueue the plugin's CSS file.
 *
 * This function is triggered by the 'admin_enqueue_scripts' action hook.
 *
 * @since 1.5
 */
function euc_enqueue_styles() {
    wp_enqueue_style(
        'euc-styles',
        plugins_url('email-user-cleaner.css', __FILE__),
        array(),
        '1.6'
    );
}
add_action('admin_enqueue_scripts', 'euc_enqueue_styles');

/**
 * Renders the footer section for the E-mail User Cleaner plugin admin page.
 *
 * This footer includes styled dashicons, the current year, and links to Gioxx.org
 * and the plugin's GitHub repository.
 */
function euc_render_footer() {
    ?>
    <div class="footer" style="padding-top: 35px;">
        <hr>
        <span class="dashicons dashicons-hammer"></span> Gioxx, <?php echo esc_html(date('Y')); ?> &#x2022; 
        <span class="dashicons dashicons-admin-home"></span> 
        <a href="<?php echo esc_url('https://go.gioxx.org/emailusercleaner'); ?>">Gioxx.org</a> &#x2022; 
        <span class="dashicons dashicons-media-code"></span> 
        <a href="<?php echo esc_url('https://github.com/gioxx/email-user-cleaner'); ?>">GitHub</a>
    </div>
    <?php
}

/**
 * Verify current user's permissions.
 *
 * This function checks if the current user has the 'manage_options' capability.
 * If the user does not have this capability, the function terminates execution
 * and displays an error message, indicating insufficient permissions.
 *
 * @since 1.5
 */
function euc_verify_permissions() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'email-user-cleaner'), 403);
    }
}

// Show the plugin page
function euc_admin_page() {
    euc_verify_permissions();

    // Determines which tab is active
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'delete_users';

    ?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <!-- Tab for "Delete Users" -->
            <a href="?page=email-user-cleaner&tab=delete_users" class="nav-tab <?php echo $active_tab === 'delete_users' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e('Delete Users', 'email-user-cleaner'); ?>
            </a>
            
            <!-- Tab for "Find Duplicates" -->
            <a href="?page=email-user-cleaner&tab=find_duplicates" class="nav-tab <?php echo $active_tab === 'find_duplicates' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e('Find Duplicated Users', 'email-user-cleaner'); ?>
            </a>
        </h2>

        <?php
        // Page content based on the active tab
        if ($active_tab === 'delete_users') {
            euc_delete_users_page(); // Show user deletion page
        } elseif ($active_tab === 'find_duplicates') {
            euc_show_duplicates_page(); // Show page to find duplicate users
        }

        euc_render_footer();

        ?>
    </div>
    <?php
}

/**
 * Shows the page to delete users by specifying their email addresses.
 *
 * This page is accessible by the administrator only.
 *
 * @since 1.0
 */
function euc_delete_users_page() {
    euc_verify_permissions();
    
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
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.querySelector('input[name="submit"]');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(event) {
                    if (!confirm('Are you sure you want to delete these users? This operation cannot be undone.')) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>

    <?php
}

// Export all users in a CSV file
function euc_export_users_csv() {
    euc_verify_permissions();

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
            sanitize_user($user->user_login), // user_login
            sanitize_email($user->user_email), // user_email
            sanitize_text_field($user->display_name), // display_name
            sanitize_text_field(implode(', ', $user->roles)), // roles
            sanitize_text_field(get_user_meta($user->ID, 'first_name', true)), // first_name
            sanitize_text_field(get_user_meta($user->ID, 'last_name', true)) // last_name
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
        euc_verify_permissions();
        
        // Get email addresses specified
        $emails = isset($_POST['emails']) ? sanitize_textarea_field($_POST['emails']) : '';

        // Split email addresses into an array
        $emails_array = array_map('trim', explode("\n", $emails));
        $emails_array = array_filter($emails_array, function ($email) {
            $sanitized_email = sanitize_email($email);
            return is_email($sanitized_email) ? $sanitized_email : false;
        });

        // Avoid duplicates
        $emails_array = array_unique($emails_array);

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

            // Checks that the email is valid and belongs to a user
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
            echo '<div class="notice notice-success is-dismissible">';
            foreach ($success_messages as $message) {
                echo '<p>' . esc_html($message) . '</p>';
            }
            echo '</div>';
        }

        if (!empty($error_messages)) {
            echo '<div class="notice notice-error is-dismissible">';
            foreach ($error_messages as $message) {
                echo '<p>' . esc_html($message) . '</p>';
            }
            echo '</div>';
        }
    }
}

/**
 * Hook to capture user login and save the time to user meta.
 *
 * Capture the WordPress hook "wp_login" to save the last login time to user meta.
 *
 * @param string $user_login The user login.
 * @param WP_User $user WP_User object.
 */
function euc_save_last_login($user_login, $user) {
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}
add_action('wp_login', 'euc_save_last_login', 10, 2);

// Function to identify duplicated users
function euc_show_duplicates_page() {
    // Verify if the current user has permission to view this page
    euc_verify_permissions();

    // Retrieve duplicate users
    $duplicates = euc_find_duplicate_users(); // This function should be defined elsewhere to find duplicates

    ?>
    <div class="wrap">
        <h2><span class="dashicons dashicons-groups"></span> <?php esc_html_e('Show users (probably) duplicated', 'email-user-cleaner'); ?></h2>
        
        <form method="post" action="">
            <?php wp_nonce_field('euc_delete_duplicates_nonce', '_wpnonce'); ?>

            <?php if (!empty($duplicates)) : ?>
                <div class="euc_warning-box">
                    <div class="euc_warning-icon">!</div>
                    <div class="euc_warning-message">
                        <?php esc_html_e('Remember that the deletion operation, if requested, cannot be undone.', 'email-user-cleaner'); ?>
                        <br /><strong><?php esc_html_e('Backup your database and proceed with caution', 'email-user-cleaner'); ?></strong>.
                    </div>
                </div>

                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-cb" style="width: 2%;"><input type="checkbox" id="cb-select-all"></th>
                            <th scope="col" class="manage-column column-username"><?php esc_html_e('Username', 'email-user-cleaner'); ?></th>
                            <th scope="col" class="manage-column column-email"><?php esc_html_e('Email', 'email-user-cleaner'); ?></th>
                            <th scope="col" class="manage-column column-name"><?php esc_html_e('Full Name', 'email-user-cleaner'); ?></th>
                            <th scope="col" class="manage-column column-reason"><?php esc_html_e('Duplicate Criteria', 'email-user-cleaner'); ?></th>
                            <th scope="col" class="manage-column column-last-login"><?php esc_html_e('Last Login', 'email-user-cleaner'); ?></th> <!-- New column for Last Login -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($duplicates as $duplicate_group) : ?>
                            <?php foreach ($duplicate_group['users'] as $user) : ?>
                                <?php 
                                // Retrieve the last login time from user meta
                                $last_login = get_user_meta($user->ID, 'last_login', true);
                                
                                // Format the last login date if available
                                $last_login_display = !empty($last_login) ? date('Y-m-d H:i:s', strtotime($last_login)) : __('Never logged in', 'email-user-cleaner');
                                ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="users_to_delete[]" value="<?php echo esc_attr($user->ID); ?>">
                                    </th>
                                    <td><?php echo esc_html($user->user_login); ?></td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></td>
                                    <td><?php echo esc_html($duplicate_group['criteria']); ?></td>
                                    <td><?php echo esc_html($last_login_display); ?></td> <!-- Display last login -->
                                </tr>
                            <?php endforeach; ?>
                            <tr><td colspan="6"><hr></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top: 20px;">
                    <input type="submit" name="delete_selected" value="<?php esc_attr_e('Delete Selected Users', 'email-user-cleaner'); ?>" class="button-primary" />
                </div>
            <?php else : ?>
                <p><?php esc_html_e('No duplicated users found.', 'email-user-cleaner'); ?></p>
            <?php endif; ?>
        </form>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('cb-select-all');
            const checkboxes = document.querySelectorAll('input[name="users_to_delete[]"]');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                });
            }
        });
    </script>

    <?php
    // Handle the deletion of selected users
    if (isset($_POST['delete_selected'])) {
        // Verify nonce
        if (!check_admin_referer('euc_delete_duplicates_nonce')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'email-user-cleaner'));
        }

        // Verify that the user has the necessary permissions
        euc_verify_permissions();

        // Get the IDs of the users to delete
        $users_to_delete = isset($_POST['users_to_delete']) ? array_map('intval', $_POST['users_to_delete']) : [];

        // Delete selected users
        foreach ($users_to_delete as $user_id) {
            $deleted = wp_delete_user($user_id);
            if ($deleted) {
                echo '<div class="notice notice-success is-dismissible">' . esc_html(sprintf(__('User with ID %d successfully deleted.', 'email-user-cleaner'), $user_id)) . '</div>';
            } else {
                echo '<div class="notice notice-error is-dismissible">' . esc_html(sprintf(__('Error deleting user with ID %d.', 'email-user-cleaner'), $user_id)) . '</div>';
            }
        }
    }
}

// Helper function to add duplicates to the duplicates list
function add_duplicates_to_list($map, $criteria, &$duplicates) {
    foreach ($map as $key => $users_with_same_key) {
        // Check if there are multiple users with the same key (e.g., email or full name)
        if (count($users_with_same_key) > 1) {
            // Add these users to the duplicates list with the appropriate criteria
            $duplicates[] = [
                'criteria' => $criteria,
                'users' => $users_with_same_key
            ];
        }
    }
}

// Main function to find duplicate users based on different criteria
function euc_find_duplicate_users() {
    $users = get_users();
    $duplicates = [];
    $email_map = [];
    $name_map = [];

    // Find duplicates based on email and full names in a single loop
    foreach ($users as $user) {
        // Check for duplicates by email
        $user_email = $user->user_email;
        if (isset($email_map[$user_email])) {
            // If email already exists in the map, add this user to the existing array
            $email_map[$user_email][] = $user;
        } else {
            // Otherwise, create a new array with this user
            $email_map[$user_email] = [$user];
        }

        // Check for duplicates by full name
        $full_name = strtolower(trim($user->first_name . ' ' . $user->last_name));
        if (isset($name_map[$full_name])) {
            // If full name already exists in the map, add this user to the existing array
            $name_map[$full_name][] = $user;
        } else {
            // Otherwise, create a new array with this user
            $name_map[$full_name] = [$user];
        }
    }

    // Use the helper function to add duplicate users to the final duplicates list
    add_duplicates_to_list($email_map, 'Email', $duplicates);
    add_duplicates_to_list($name_map, 'Full Name', $duplicates);

    return $duplicates;
}

// Add user deletion function to admin_init hook
add_action('admin_action_euc_export_users_csv', 'euc_export_users_csv');