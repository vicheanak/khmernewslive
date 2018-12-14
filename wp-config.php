<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'khmernewslive');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'tU/x@168rY');

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

define('AUTH_KEY',         'Nc#j,N$ @x{Jkh?|@ &QB||9|c>(lxjb*%4jf6.<+iJo@3[wTqyN([h>Mhq_q^xT');
define('SECURE_AUTH_KEY',  'KDoOg;`0`VL[i<?1o^pZ[jNkt@B;+ JSgg g0_?jV:j$E[=oWWzlhkFa(&Mj=1H}');
define('LOGGED_IN_KEY',    'Sq#t(uX~$Svb7J!%_r!+h}#<#D`),:P%b.p~TM;*d7+vV{v7H-<Gh4?UP%TliF)u');
define('NONCE_KEY',        'xZ_pC<eMS)]nT4^si1c=,aa-?}L0oCj[m~pB_4!+oIq1G+zD39ZGO%4!C~g9^s>m');
define('AUTH_SALT',        'e:a{+PYsXOSP,vt%[L&Mmg!/4)VhHB.$(oM3HJ!=?aHw9-3wi=EO=}?jm{|uzl{2');
define('SECURE_AUTH_SALT', 'U/bHYAi[t HD `M5b6PvE9YWSJmoGHD~(96Rs ,#cip&]$CUBesKr;~Fg];P4n7^');
define('LOGGED_IN_SALT',   'vGUNcc!QM>gfE%+TC>mu2i;6/S8(Rmn`!twbebgI$&lNk{=M16PDwei/|{Mqx6)c');
define('NONCE_SALT',       'h+vJ.gzX8pD%+f+K4Opwq97.hhw%<j-1(2{=>py?9uisB*FB0VZTi|N6_wrZp*hb');

define('WP_CACHE_KEY_SALT', 'khmernewslive24.com');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define ('WP_POST_REVISIONS', FALSE);
define('WP_ALLOW_REPAIR', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
