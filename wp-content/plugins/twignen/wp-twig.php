<?php
/*
Plugin Name: WordPress Twig templating engine
Plugin URI: http://inchoo.net
Description: Engine for creating twig templates for wordpress
Version: 1.0
Author: Darko Goles
Author URI: http://inchoo.net/author/darko.goles/
License: MIT
*/

//Main twig library autoloader file
require_once dirname(__FILE__) . '/lib/Twig/Autoloader.php';
//My custom class made, I also want it to be autoloaded
require_once dirname(__FILE__) . '/Wp_TwigEngine.php';

function twigAutoLoad() {
	Twig_Autoloader::register();
	Wp_TwigEngine_Autoloader::register();
}

add_action('init', 'twigAutoLoad');

?>