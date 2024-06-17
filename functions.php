<?php
/**
 * HT Avada child theme
 *
 * @package hey-tony
 */
namespace HT;

/* If file is called directly abort. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Dependencies and core theme class */

require get_stylesheet_directory() . '/vendor/autoload.php';

/* Import */

use HT\HT as HT;

/* Instantiate theme class */

$ht = new HT();
