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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'gB$OK%HcF~<OWvv=-c|SvF74WvCL6.Y)UU.5mJvDJr,x$YWDXQ]W}& g>$C_z&UZ');
define('SECURE_AUTH_KEY',  'w##4e.h[TYLi?1K:p$#OmM$$hAWz8q} [{KxgRmiZ1E-k&!-ZU6++o;;X7?Yen@D');
define('LOGGED_IN_KEY',    '.d7Yaup,7r$4Hk*K$f<SV5&49sp|H7FF5#21#,UcR;y<}>9CxV8[`[,IGb|u}+Gn');
define('NONCE_KEY',        'r]w}R8(=98hjDj/W-G07(+A9Z/4Rm2YRn88mL0m_Df)&4#X@o)P>&nkesGe#u,)r');
define('AUTH_SALT',        'IG}oDv=0YT<b=]QhfO>]WX`|_$r`[@0]@Y<MmJjZ#.b m4zv).mqlTVQ86}R9>m#');
define('SECURE_AUTH_SALT', '!4WLu>HEo?YW:(LH>f%f}+#AAfp22%~_UM|pvJxb.x;yRer1A;^/f`z2YwV-uFg^');
define('LOGGED_IN_SALT',   'MZsn%:H(syfzn_[O?/pS*1AU,yoBkH88Ki-Y^EB;!QU_~s<@<VEDzi!|?<*rQ$d+');
define('NONCE_SALT',       '($abW[0#$NUwPxrh=.?57?Ff#*n5pJ3L_+XWTo7f?cT5KMobV!IW[{ y6}kBO31g');

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
