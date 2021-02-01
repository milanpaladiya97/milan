<?php
/** Enable W3 Total Cache */
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
define('DB_NAME', 'dev_bizzsecure');
/** MySQL database username */
define('DB_USER', 'biz_secure_dev');
/** MySQL database password */
define('DB_PASSWORD', 'V2I(p;UHy{}g');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');
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
define('AUTH_KEY',         '@jB3&*/v<pTTHs_-x[6|SbOnemOS|$rX=OWhcC;FGF(w/a/_Sk;*^7J.;=K{Y~6H');
define('SECURE_AUTH_KEY',  '7<;:iuMn8<dhi)Pf_%V(^6> D~BtC6f[hgd8JR58Xre*!Qm?@pz];[,PvnQ,-8?e');
define('LOGGED_IN_KEY',    '5_bB%,U9Y4$p&fG/^un1=9_]1P`[/HMX3Q$0hYoJXP9Ss6s1cUBf^k6[#gsZ?a%N');
define('NONCE_KEY',        '[OM^#|~|GPj?bYJ}@068fyBsZCd:;A,f1Z[~JjE6s1dKl$]~OA,XcG 1*@}+/)Zv');
define('AUTH_SALT',        'lQ_XX2-10dd+msvR/G3|$;{?WW9<k$MvQ;c4!1z*qG,5|9q[!%R]xXu{YM>OwUqv');
define('SECURE_AUTH_SALT', '+{G2d8uYq+c>0=Emc3e2d1JW%,@Ens)UppQUFyC4H7jBE8b>`={0Wk}a%J m$h.~');
define('LOGGED_IN_SALT',   'N4lZ:-nIzL[_c&_p EvMU7DI_FwC(7*Hd_A|<S TrJ^ ;cC*kIqT<k.GW]NrW_j.');
define('NONCE_SALT',       'y|M24/e)U?OT4M,90f8r$mfIvDAd{n%Mg@6z!af1lIn>:) &tVE1j$8]mGh(c#oZ');
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = '_bz_scur_wp_';
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
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
// define( 'WPMS_ON', true );
/** Multisite */
define('WP_ALLOW_MULTISITE', true);
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
