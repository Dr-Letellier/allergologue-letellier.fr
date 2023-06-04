<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'allergologuelet');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'allergologuelet');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'Ezs8EbHm');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|!Uj*TJ#w11&^0nTcm*(ICC)X_t&@28lvZ@Z`OvB5.PcufFu1&MO=)32pthtdPWx');
define('SECURE_AUTH_KEY',  'uol=45,0/E-_]J>kw(FhL|p)VSsTzc-2}Ekxwp~%7+f2h2pY/0/=vZy26=A4O/(K');
define('LOGGED_IN_KEY',    '-%%FeNf}1+a&xf+v-iMFjRfB_QAS(sq.?>;YBJ)=}9lM{;gsDKMjm<j653])h0Q~');
define('NONCE_KEY',        'I!`+W^@+<Ha T/{$4k-WP[Sqfdp|^.I4L5zlCbZq*7-5T4:3GYID{Nw7k9U0Z$mC');
define('AUTH_SALT',        'csqn+e+fXY2;rIDR1OtI?r c^(GGZAzvC!|m{B0Pb,KHy+HsZ2n@vnCT/tLUF^}e');
define('SECURE_AUTH_SALT', 'S-II=yiGJW0xC]qp`,|^](<6,`g q[-VVd=RIa1>%SB^Yx.JJ+HG=]W|/hwyIW I');
define('LOGGED_IN_SALT',   '@, ooJ&|CEET?~[Mpd@#;ASU)*H+/XG!-GPFD=+#uT ?~C5]@-~cd+_=Z>;sYTX;');
define('NONCE_SALT',       'FKx-o}_>|E*;eON41iyT%ZC$>K4&]~32i.xn]w|5Z6{Qp5q!V<@QyER+DTf9dX4F');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wpLt_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
define('DISALLOW_FILE_EDIT',true);
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
