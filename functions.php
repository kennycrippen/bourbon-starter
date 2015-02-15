<?php

// Fire all functions
// enqueue base scripts and styles
add_action('wp_enqueue_scripts', 'crippen_scripts_and_styles', 999);

/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function crippen_scripts_and_styles() {
  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
  if (!is_admin()) {

	// removes WP version of jQuery
	wp_deregister_script('jquery');

    // register main stylesheet
    wp_enqueue_style( 'crippen-stylesheet', get_template_directory_uri() . '/library/css/style.css', array(), '', 'all' );

    //adding minified build js file in the footer
    wp_enqueue_script( 'crippen-js', get_template_directory_uri() . '/library/js/build/production.min.js', array( 'jquery' ), '', true );

    /*
    I recommend using a plugin to call jQuery
    using the google cdn. That way it stays cached
    and your site will load faster.
    */
    wp_enqueue_script( 'crippen-js' );

  }
}