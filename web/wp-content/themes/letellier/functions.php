<?php
// enqueue the parent theme stylesheet

function wpm_enqueue_styles(){
wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'wpm_enqueue_styles' );

// enqueue the child theme stylesheet

function thememedicpress_enqueue_styles() {
wp_enqueue_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css');
}
add_action( 'wp_enqueue_scripts', 'thememedicpress_enqueue_styles' );

function medicpress_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'medicpress-pt', $lang );
}
add_action( 'after_setup_theme', 'medicpress_lang_setup' );

function proteuswidgets_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'medicpress-pt', $lang );
	load_child_theme_textdomain( 'proteuswidgets', $lang.'/proteuswidgets' );
}
add_action( 'after_setup_theme', 'medicpress_lang_setup' );

/*
function my_child_theme_setup() {
    load_child_theme_textdomain( 'my-child-theme', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'my_child_theme_setup' );
*/

require_once( get_stylesheet_directory() . '/inc/filters.php');
/*require_once( get_stylesheet_directory() . '/inc/widgets-views/widget-latest-news.php');*/