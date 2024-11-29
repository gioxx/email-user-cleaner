=== E-mail User Cleaner ===
Contributors: gioxx
Tags: maintenance, email, user, cleaning, csv
Donate link: https://ko-fi.com/gioxx
Requires at least: 6.0
Tested up to: 6.7.1
Requires PHP: 7.4
Stable tag: 1.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Delete users corresponding to the specified email addresses.

== Description ==
The plugin provides the administrator with a convenient maintenance page, called E-mail User Cleaner, which will be navigable through the WordPress administrative interface.
From the E-mail User Cleaner page you will be able to:
 - generate and download a CSV file containing a complete list of registered users on the WordPress instance.
 - Specify one or more e-mail addresses (one address per line), corresponding to registered users on the WordPress instance, to be deleted from the system.
 - Identify duplicated users (based on criteria that checks first name, last name and e-mail address) and decide to select and delete them, also referring to the last login date to the WordPress installation.

== Installation ==
Install the plugin and activate it. You will find the E-mail User Cleaner entry in the sidebar of your WordPress Administrative Dashboard.

== Screenshots ==
1. The main screen of the plugin. From here you can export the CSV file containing the complete list of registered users in WordPress or you can specify the email addresses related to the users to be deleted.
2. A list of e-mail addresses (one address per line) corresponding to users to be deleted in WordPress.
3. The plugin correctly deleted all users corresponding to the specified e-mail addresses.
4. The plugin reports that the user corresponding to the administrator's e-mail address has not been deleted for security reasons.

== Changelog ==
= 1.6=
* You can now identify duplicated users (based on criteria that checks first name, last name and e-mail address) and decide to select and delete them, also referring to the last login date to the WordPress installation.
* You can use, for your tests, the Python script generateRandomCSV.py (found in the tools folder of the plugin's GitHub repository) which is able to generate CSVs of fake users complete with first name, last name, email address, which you can import into WordPress (test environment) to play around with this plugin and verify that everything is working properly (you can find two ready-made files already, example_include-names.csv and example_simple.csv).

= 1.5 =
* Initial release published in the WordPress plugin directory.