<?php

// Fire all functions
// we're firing all out initial functions at the start
add_action('after_setup_theme','crippen_start', 16);

function crippen_start() {

    // launching operation cleanup
    add_action('init', 'crippen_head_cleanup');
    // remove WP version from RSS
    add_filter('the_generator', 'crippen_rss_version');
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'crippen_remove_wp_widget_recent_comments_style', 1 );
    // clean up comment styles in the head
    add_action('wp_head', 'crippen_remove_recent_comments_style', 1);

    // enqueue base scripts and styles
    add_action('wp_enqueue_scripts', 'crippen_scripts_and_styles', 999);
    // ie conditional wrapper

    // launching this stuff after theme setup
    crippen_theme_support();

    // adding sidebars to Wordpress (these are created in functions.php)
    add_action( 'widgets_init', 'crippen_register_sidebars' );

    // cleaning up random code around images
    add_filter('the_content', 'crippen_filter_ptags_on_images');
    // cleaning up excerpt
    add_filter('excerpt_more', 'crippen_excerpt_more');

} /* end crippen start */

/*********************
WP_HEAD GOODNESS
The default wordpress head is a mess. 
Let's clean it up by removing all the junk we don't need.
*********************/

function crippen_head_cleanup() {
  // category feeds
  // remove_action( 'wp_head', 'feed_links_extra', 3 );
  // post and comment feeds
  // remove_action( 'wp_head', 'feed_links', 2 );
  // EditURI link
  remove_action( 'wp_head', 'rsd_link' );
  // windows live writer
  remove_action( 'wp_head', 'wlwmanifest_link' );
  // index link
  remove_action( 'wp_head', 'index_rel_link' );
  // previous link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
  // start link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
  // links for adjacent posts
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
  // WP version
  remove_action( 'wp_head', 'wp_generator' );
} /* end crippen head cleanup */

// remove WP version from RSS
function crippen_rss_version() { return ''; }

// remove injected CSS for recent comments widget
function crippen_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );
   }
}

// remove injected CSS from recent comments widget
function crippen_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove injected CSS from gallery
function crippen_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}

/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function crippen_scripts_and_styles() {
  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
  if (!is_admin()) {

    // removes WP version of jQuery
    wp_deregister_script('jquery');

    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/library/bower_components/jquery/dist/jquery.js', array(), '2.1.3', false );

    // modernizr (without media query polyfill)
    wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/library/bower_components/modernizr/modernizr.js', array(), '2.8.3', false );

    // register main stylesheet
    wp_enqueue_style( 'crippen-stylesheet', get_template_directory_uri() . '/library/css/style.css', array(), '', 'all' );

    //adding minified build js file in the footer
    wp_enqueue_script( 'crippen-js', get_template_directory_uri() . '/library/js/build/production.min.js', array( 'jquery' ), '', true );

  }
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function crippen_theme_support() {

  // wp thumbnails (sizes handled in functions.php)
  add_theme_support('post-thumbnails');

  // default thumb size
  set_post_thumbnail_size(125, 125, true);

  // rss 
  add_theme_support('automatic-feed-links');

  // to add header image support go here: http://themble.com/support/adding-header-background-image-support/

  // wp menus
  add_theme_support( 'menus' );
  
  //html5 support (http://themeshaper.com/2013/08/01/html5-support-in-wordpress-core/)
  add_theme_support( 'html5', 
           array( 
            'comment-list', 
            'comment-form', 
            'search-form', 
           ) 
  );
  

} /* end crippen theme support */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs
function crippen_filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function crippen_excerpt_more($more) {
  global $post;
  // edit here if you like
return '...  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __('Read', 'crippentheme') . get_the_title($post->ID).'">'. __('Read more &raquo;', 'crippentheme') .'</a>';
}

/*********************
MENUS & NAVIGATION
*********************/
// REGISTER MENUS
register_nav_menus(
  array(
    'main-nav' => __( 'The Main Menu' ),   // main nav in header
    'footer-links' => __( 'Footer Links' ) // secondary nav in footer
  )
);

// THE MAIN MENU
function crippen_main_nav() {
    wp_nav_menu(array(
      'container' => false,                           // remove nav container
      'container_class' => '',           // class of container (should you choose to use it)
      'menu' => __( 'The Main Menu', 'crippentheme' ),  // nav name
      'menu_class' => 'navigation-menu',         // adding custom nav class
      'theme_location' => 'main-nav',                 // where it's located in the theme
      'before' => '',                                 // before the menu
        'after' => '',                                  // after the menu
        'link_before' => '',                            // before each link
        'link_after' => '',                             // after each link
      'fallback_cb' => 'crippen_main_nav_fallback'      // fallback function
  ));
} /* end crippen main nav */

// THE FOOTER MENU
function crippen_footer_links() {
    wp_nav_menu(array(
      'container' => '',                              // remove nav container
      'container_class' => 'footer-links clearfix',   // class of container (should you choose to use it)
      'menu' => __( 'Footer Links', 'crippentheme' ),   // nav name
      'menu_class' => 'sub-nav',      // adding custom nav class
      'theme_location' => 'footer-links',             // where it's located in the theme
      'before' => '',                                 // before the menu
        'after' => '',                                  // after the menu
        'link_before' => '',                            // before each link
        'link_after' => '',                             // after each link
        'depth' => 0,                                   // limit the depth of the nav
      'fallback_cb' => 'crippen_footer_links_fallback'  // fallback function
  ));
} /* end crippen footer link */

// HEADER FALLBACK MENU
function crippen_main_nav_fallback() {
  wp_page_menu( array(
    'show_home' => false,
      'menu_class' => '',      // adding custom nav class
    'include'     => '',
    'exclude'     => '',
    'echo'        => true,
        'link_before' => '',                            // before each link
        'link_after' => ''                             // after each link
  ) );
}

// FOOTER FALLBACK MENU
function crippen_footer_links_fallback() {
  /* you can put a default here if you like */
}

/*********************
SIDEBARS
*********************/

// SIDEBARS AND WIDGETIZED AREAS
function crippen_register_sidebars() {
  register_sidebar(array(
    'id' => 'sidebar1',
    'name' => __('Sidebar 1', 'crippentheme'),
    'description' => __('The first (primary) sidebar.', 'crippentheme'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  register_sidebar(array(
    'id' => 'offcanvas',
    'name' => __('Offcanvas', 'crippentheme'),
    'description' => __('The offcanvas sidebar.', 'crippentheme'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  /*
  to add more sidebars or widgetized areas, copy
  and edit the above sidebar code. In order to call
  your new sidebar use the following code:

  Change the name to whatever your new
  sidebar's id is, for example:

  register_sidebar(array(
    'id' => 'sidebar2',
    'name' => __('Sidebar 2', 'crippentheme'),
    'description' => __('The second (secondary) sidebar.', 'crippentheme'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  To call the sidebar in your template, you can copy
  the sidebar.php file and rename it to your sidebar's name.
  So using the above example, it would be:
  sidebar-sidebar2.php

  */
} // don't remove this bracket!

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function crippen_page_navi($before = '', $after = '') {
  global $wpdb, $wp_query;
  $request = $wp_query->request;
  $posts_per_page = intval(get_query_var('posts_per_page'));
  $paged = intval(get_query_var('paged'));
  $numposts = $wp_query->found_posts;
  $max_page = $wp_query->max_num_pages;
  if ( $numposts <= $posts_per_page ) { return; }
  if(empty($paged) || $paged == 0) {
    $paged = 1;
  }
  $pages_to_show = 7;
  $pages_to_show_minus_1 = $pages_to_show-1;
  $half_page_start = floor($pages_to_show_minus_1/2);
  $half_page_end = ceil($pages_to_show_minus_1/2);
  $start_page = $paged - $half_page_start;
  if($start_page <= 0) {
    $start_page = 1;
  }
  $end_page = $paged + $half_page_end;
  if(($end_page - $start_page) != $pages_to_show_minus_1) {
    $end_page = $start_page + $pages_to_show_minus_1;
  }
  if($end_page > $max_page) {
    $start_page = $max_page - $pages_to_show_minus_1;
    $end_page = $max_page;
  }
  if($start_page <= 0) {
    $start_page = 1;
  }
  echo $before.'<nav class="page-navigation"><ul class="pagination">'."";
  if ($start_page >= 2 && $pages_to_show < $max_page) {
    $first_page_text = __( "First", 'jointstheme' );
    echo '<li><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
  }
  echo '<li>';
  previous_posts_link('<<');
  echo '</li>';
  for($i = $start_page; $i  <= $end_page; $i++) {
    if($i == $paged) {
      echo '<li class="current"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
    } else {
      echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
    }
  }
  echo '<li>';
  next_posts_link('>>');
  echo '</li>';
  if ($end_page < $max_page) {
    $last_page_text = __( "Last", 'jointstheme' );
    echo '<li><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
  }
  echo '</ul></nav>'.$after."";
} /* end page navi */