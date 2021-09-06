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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'server052800_mikola-dev' );

/** MySQL database username */
define( 'DB_USER', 'server052800_mikola-dev' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Mik11ola' );

/** MySQL hostname */
define( 'DB_HOST', 'mariadb105.server052800.nazwa.pl' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Hy1MgT:4.9>a.UnzoY0)X?ac9RnZk^ r_vsTt_eBvqI-D0~`G.80Y{2*c<b`SfYM' );
define( 'SECURE_AUTH_KEY',  'qTcm*a+f`m0KX6M sn2L>,YrQ!bwyyyadpm|ea8%tKAB5L+[D;Pmx]nQWl[Fps 1' );
define( 'LOGGED_IN_KEY',    '_mIER=HJXae)4fv}4v_HVY.i9Um<a #~d=9 1xW-+R/{-1Z<!u@V;-I}xEQ]igW-' );
define( 'NONCE_KEY',        'jA,ik}1vMOToYuD,$gPi*#@Z+8).d=W H{esJ:;Ban7c:x3= muj5`C&yprW[y9[' );
define( 'AUTH_SALT',        'YJ`b0,J^KhLZSXF07%P=g4GDWR3@K5X:Y]oe&uUcz|r5JH&_.GRRF0V`^F{wfL{%' );
define( 'SECURE_AUTH_SALT', '78p)S H1`.8aa6cJiu,zA`mE+9f-8xj]CAPmD#KYZWAlP_@(M982[z8g2oTjsjM~' );
define( 'LOGGED_IN_SALT',   '9EtxeXX!z8+M|:8at2mi$.cv_&|Lx_SH49EZyE>dG>5ad)V0YrF<gFVuS|;8_Jw2' );
define( 'NONCE_SALT',       '[e,fu]Kp?-Z7P;i+P&DL%{PfmEj3>p-j`Y&e&11O}-44g,Ljmc}29-JsWAkRf!mg' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
