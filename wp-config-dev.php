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
define('DB_NAME', 'scruples');
/** MySQL database username */
define('DB_USER', 'root');
/** MySQL database password */
define('DB_PASSWORD', 'root');
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
define('AUTH_KEY',         '$rN-O_6E53n :`:w;yV}RZ_j/S]a;$=l.<Da[;n*C-y5RkLn{Ir-W;>}5Wf]_Y.c');
define('SECURE_AUTH_KEY',  'WUi+Ddh}Ci&sk*4<RdOlWLX{F+_~&MvdK^gq{cz`f&D)|O+Ju^aBF9Z|VzrqOW1q');
define('LOGGED_IN_KEY',    '{A`Y]F;NOy?r-?TNsWKZ>xLH+mq`sM+_kMTtl&wH&3l+nCL<0m8:]=@/|6!V6Y+i');
define('NONCE_KEY',        '3To}sAA&|p+#lv|<=%_J>`n srRGG=kdP2-BW1||H@2^.#e9hfp!z+59^#%MK7Eg');
define('AUTH_SALT',        'Mo Ps])lU]VO[~X0M QEK~{9*(tE!Q:-.kf!mA9zH-9T~AZAZ(fOA%H h}sdC K+');
define('SECURE_AUTH_SALT', '-ml|(eNm>{}x.e[-dZ:T.Ogu`A3q$||]L[`W~=3.qcEHjp^Hbj3pn/-*Y#a)y9_v');
define('LOGGED_IN_SALT',   'e+~`_|cN[IO-N2/QfV_.Sz0Jq;7c8lB&|6q&2;4?& asSv*<lz77UO q9GUaCKdi');
define('NONCE_SALT',       'J1sK/ ez+@mq+(;7i7 K^pXZODC`%gTq$GH_os30B(DQ}jQi~>z>3B&:uOZ7fPPE');
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