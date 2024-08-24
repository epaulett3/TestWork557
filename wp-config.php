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
define( 'DB_NAME', 'testwork557_db' );

/** Database username */
define( 'DB_USER', 'testwork557_user' );

/** Database password */
define( 'DB_PASSWORD', 'XaplB0knurMu7Ze_' );

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
define( 'AUTH_KEY',         'W}IT(@$HH;%t?Z<egYF$j.Lt^9gvaxYQ>wH94Rj6w1?VM=j4Dr0H`Fp=OU:_y*Wi' );
define( 'SECURE_AUTH_KEY',  '4&GW0djH>Vhuj0E(V<UaZ;ED4I{Bu=)5|bKljXrbRv*jJb[<edtB TI3;u,k__uJ' );
define( 'LOGGED_IN_KEY',    'Vil@57+dW1Mjt<G=f4$e :v/(am`KW|I_h+W8}Uu7<oB0U6(oKr=eFue>z9O8E/[' );
define( 'NONCE_KEY',        'wyDyFl[*Y+$CHsr%BK&kp@H TMz?!C)3rY@i7>RRyi%c!a-{O+V{8gsTRKBYV{lk' );
define( 'AUTH_SALT',        'o@A%W*#Ii8C[I]G!=/u|GCds}Eg-N9$@MJh?#DjUvr??LP` W!.B?vm=c9)R;@Q7' );
define( 'SECURE_AUTH_SALT', 'pyboe3{7Gwd3P+RFxLP!j)+jjm*8WFUyLU wFRgX9&S,Cdh|}jL4pDUh+8(&W*q+' );
define( 'LOGGED_IN_SALT',   '3(Pb}d%}pjIF5%0jE=TjxE/Dt@iQg1`4:7+fg1?pE2S)4/ogYZ<C61F3j-*G 2jd' );
define( 'NONCE_SALT',       '16Rir%RqephiV%+uz/Ee <B0@uk]~~*Y#~qgSCI=`CPWC{$2^4*b0wi4oN^>dHWL' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'tw_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
