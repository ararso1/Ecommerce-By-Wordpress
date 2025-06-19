<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tirushhr_wp411' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
/* define( 'DB_PASSWORD', '4]Sp9N(6FR' );
 */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'oi3zdxtfgrga1gphov9ijyfm7m0euve321a7pjuckoxfwl7v77b1dm2vz7reefbs' );
define( 'SECURE_AUTH_KEY',  'qdzmpnmzfkkt3cj7vxgauxvvzfunlsgy3czel9kkpkohgrejhuamfaaqoqkcywxk' );
define( 'LOGGED_IN_KEY',    'gmox0zgyzdrvixa7mkwuekjx85wn41f3ak5vfq14kdwk41ns2mapjtx6mt9twob9' );
define( 'NONCE_KEY',        'yp4wkzvagd1crjcdxi4lt6ssm0zhsrtxsteiagavflywhvwaq9sqsyaaj0tsqasc' );
define( 'AUTH_SALT',        'pqw2f4lus42synud3om9i6xeillfc2cf8m5szfdaphkevmbxjq0q2lypbiujfssw' );
define( 'SECURE_AUTH_SALT', 'r53nuf7noxops09gajxbgr5zaycoakxffohg3ucqyj3optinukinarahoita39gi' );
define( 'LOGGED_IN_SALT',   'dahljemafgueozzsuzpe2kxnulw1zmzsm4sm4rdbt3kfpsa5r2nqnwe5cwb8vgpx' );
define( 'NONCE_SALT',       '47zxreeeg8iwgcog0umadhhsoknmatyi9q9zzao2fo8xpqexen32bxywvr7gde2w' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp2w_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */

// Debugging
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

/* Add any custom values between this line and the "stop editing" line. */



define( 'SURECART_ENCRYPTION_KEY', 'gmox0zgyzdrvixa7mkwuekjx85wn41f3ak5vfq14kdwk41ns2mapjtx6mt9twob9' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}


/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
