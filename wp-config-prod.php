<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'scruples_wpscrup');

/** MySQL database username */
define('DB_USER', 'scruples_wpscrup');

/** MySQL database password */
define('DB_PASSWORD', '(2(GZd27SP');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'didpqwyrv2vbgtk1easf24zq36vzosytrmaohzo3qkanhpoiguk1fx2qfyrq5qtx');
define('SECURE_AUTH_KEY',  'gudpgm1jn0hsmxotnocpjmrjd9fksqeccdwy4dfjdil57nsktpuxpevqld2e2nng');
define('LOGGED_IN_KEY',    'ihvzdxwmntdfkrpr3pmero6apa6nimnboj4q1gqlqa0owmqhlvqa30a5acki2p5i');
define('NONCE_KEY',        'rpcrljszdiufwbsd2bk4sxnrqvtzizmk8caiksdcq2ev4dikknvgkzp3zvstlafj');
define('AUTH_SALT',        'ezfbpz6vraniy4sq9avkbmt0dvsyk5u5u8fbj1d5svz0a7nml2n050ccmpljkkak');
define('SECURE_AUTH_SALT', 'piumsfi1rjndlpgxmgpsthojxzrdhfmzk35v5nle0vu0lrrxfnr6jyqnvshnutoa');
define('LOGGED_IN_SALT',   'ptwp1mvhiaagcw6gsafta3sugp8n3b6r1ggvhwfmvtzq7wfc5lpg29mz8yxyjatr');
define('NONCE_SALT',       'zelslgmbxc87guzxtr3eoeulreyzsmret3fpjxifojblcdsl1hpj9ju0grlh8eha');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sc';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */


/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
define( 'WP_AUTO_UPDATE_CORE', false );

define ('WPLANG', 'es_ES');

