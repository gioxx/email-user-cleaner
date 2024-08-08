<?php
/*
Plugin Name:       E-mail User Cleaner
Plugin URI:        https://go.gioxx.org/emailusercleaner
Description:       Delete users corresponding to the specified email addresses.
Version:           1.2
Author:            Gioxx
Author URI:        https://gioxx.org
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

// WordPress admin menu item
add_action('admin_menu', 'email_user_cleaner_menu');

function email_user_cleaner_menu() {
    add_menu_page(
        'E-mail User Cleaner',
        'E-mail User Cleaner',
        'manage_options',
        'email-user-cleaner',
        'email_user_cleaner_page',
        'dashicons-superhero'
    );
}

// Show the plugin page
function email_user_cleaner_page() {
    
    $plugin_data = get_plugin_data(__FILE__);
    $plugin_name = $plugin_data['Name'];
    $plugin_version = $plugin_data['Version'];

    ?>
    <style>
        /* Warning& Info box style */        
        .usercleaner_warning-box,
        .usercleaner_info-box {
            background-color: #ffeeba;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 10px;
            margin-bottom: 20px;
            margin-top: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            max-width: 600px;
        }

        .usercleaner_info-box {
            background-color: #e7f3fe;
            border: 1px solid #a0cfff;
            color: #2e74b2;
        }

        .usercleaner_info-icon {
            font-size: 24px;
            margin-right: 10px;
        }

        .usercleaner_warning-icon {
            background-color: #ffc107;
            color: white;
            width: 30px;
            height: 30px;
            font-size: 20px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
        }

        /* Success & error box style */
        .usercleaner_success-box,
        .usercleaner_error-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            max-width: 600px;
        }

        .usercleaner_error-box {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .usercleaner_textarea {
            width: 600px;
        }

        .usercleaner_results {
            margin-top: 10px;
        }

        .button-primary.takespace {
            display: block;
            margin-top: 10px;
        }

        .wrap h2 span.dashicons.dashicons-email-alt {
            font-size: 35px;
            padding-right: 10px;
            margin-right: 10px;
            vertical-align: top;
        }
    </style>

    <div class="wrap">
        <h2><span class="dashicons dashicons-email-alt"></span> E-mail User Cleaner</h2>

        <?php
            // Check plugin Import and export users and customers
            if (is_plugin_active('import-users-from-csv-with-meta/import-users-from-csv-with-meta.php')) { ?>
                <div class="usercleaner_info-box">
                    <div class="usercleaner_info-icon">ℹ️</div>
                    <div class="usercleaner_info-message">
                        Import and export users and customers detected: <a href="tools.php?page=acui&tab=export">Go to export page</a> or <strong>directly export all users from WordPress (in a CSV file) <a href="<?php echo esc_url(admin_url('admin.php?action=export_users_csv')); ?>">from here</a></strong>.
                        <div id="suggested_columns" style="padding-top: 7px;">
                            Suggested columns to export:
                            <br><span style="padding-left: 5px;"><code>user_login,user_email,display_name,role,first_name,last_name</code></span>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="usercleaner_info-box">
                    <div class="usercleaner_info-icon">ℹ️</div>
                    <div class="usercleaner_info-message">
                        If you want to export all users from WordPress (in a CSV file) <a href="<?php echo esc_url(admin_url('admin.php?action=export_users_csv')); ?>">you can click here</a>.
                    </div>
                </div>
            <?php }
        ?>

        <div class="usercleaner_warning-box">
            <div class="usercleaner_warning-icon">!</div>
            <div class="usercleaner_warning-message">
                This operation cannot be undone.
                <br /><strong>Backup your database and proceed with caution</strong>.
            </div>
        </div>

        <form method="post" action="">
            <p>Specify email addresses (one per line), click the button to start the deletion:</p>
            <textarea name="emails" class="usercleaner_textarea" rows="10"></textarea>
            <input type="submit" name="submit" value="Delete Users" class="button-primary takespace" />

            <p class="usercleaner_results">
                <?php
                    // Messages output
                    wp_nonce_field('delete_users_by_email_nonce', '_wpnonce');
                    delete_users_by_email();
                ?>
            </p>
        </form>

        <div class="footer" style="pading-top: 35px;">
            <hr>
            <span class="dashicons dashicons-hammer"></span> Gioxx, 2024 &#x2022; <span class="dashicons dashicons-admin-home"></span> <a href="https://go.gioxx.org/emailusercleaner">Gioxx.org</a> &#x2022; <span class="dashicons dashicons-media-code"></span> <a href="https://github.com/gioxx/email-user-cleaner">GitHub</a>
        </div>
    </div>

    <?php
}

// Export all users in a CSV file
function export_users_to_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('Access denied');
    }

    // Sets the header of the CSV file.
    $csv_header = array(
        'User Login',
        'Email',
        'Display Name',
        'Role',
        'First Name',
        'Last Name'
    );

    // Get all WordPress users
    $users = get_users();

    $csv_content = '';
    $csv_content .= '"' . implode('","', $csv_header) . '"' . "\n";

    // Cycle over all users and add their information to the contents of the CSV file
    foreach ($users as $user) {
        $user_info = array(
            sanitize_text_field($user->user_login),
            sanitize_email($user->user_email),
            sanitize_text_field($user->display_name),
            sanitize_text_field(implode(', ', $user->roles)),
            sanitize_text_field(get_user_meta($user->ID, 'first_name', true)),
            sanitize_text_field(get_user_meta($user->ID, 'last_name', true))
        );
        $csv_content .= '"' . implode('","', $user_info) . '"' . "\n";
    }

    // Send HTTP header to indicate that you are sending a CSV file and send the contents of the CSV file to the browser
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=wp_users_export.csv');
    echo $csv_content;
    exit;
}


// Manage user deletion
function delete_users_by_email() {
    if (isset($_POST['submit'])) {
        // Verify nonce
        $nonce = $_POST['_wpnonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'delete_users_by_email_nonce')) {
            wp_die('Security check failed. Please try again.');
        }
        
        // Verify that the user has the necessary permissions
        if (!current_user_can('manage_options')) {
            wp_die('Access denied');
        }
        
        // Get email addresses specified
        $emails = isset($_POST['emails']) ? sanitize_textarea_field($_POST['emails']) : '';

        // Split email addresses into an array
        $emails_array = explode("\n", $emails);

        // Output messages
        $success_messages = [];
        $error_messages = [];

        // Cycles over email addresses and deletes matching users
        foreach ($emails_array as $email) {
            $email = trim($email);
            if (!empty($email)) {
                $user = get_user_by('email', $email);
                if ($user) {
                    $deleted = wp_delete_user($user->ID);
                    if ($deleted) {
                        $success_messages[] = 'User with email ' . $email . ' successfully deleted!';
                    } else {
                        $error_messages[] = 'Error while deleting user with email ' . $email . '!';
                    }
                } else {
                    $error_messages[] = 'There are no users with email ' . $email . '!';
                }
            }
        }

        // Messages output
        if (!empty($success_messages)) {
            echo '<div class="usercleaner_success-box">';
            foreach ($success_messages as $message) {
                echo '<span class="dashicons dashicons-yes"></span>' . esc_html($message) . '<br />';
            }
            echo '</div>';
        }

        if (!empty($error_messages)) {
            echo '<div class="usercleaner_error-box">';
            foreach ($error_messages as $message) {
                echo '<span class="dashicons dashicons-no-alt"></span>' . esc_html($message) . '<br />';
            }
            echo '</div>';
        }

    }
}

// Add user deletion function to admin_init hook
add_action('admin_action_export_users_csv', 'export_users_to_csv');