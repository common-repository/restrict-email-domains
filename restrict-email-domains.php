<?php

/*

Plugin Name: Restrict Email Domains

Description: Plugin to restrict email domain while user registering, add the allowed domains in the Email Domains section in the dashboard. For any inquiries or you want to hire me please contact me through email <a href="mailto:kasstechweb@gmail.com">kasstechweb@gmail.com</a>

Version: 1.0

Author: Mahmoud Kassab (kasstechweb@gmail.com)

License: GPLv2 or later

*/

function red_register_post_type() {
    // domains
    $labels = array(
        'name' => __( 'Email Domains' , 'mk' ),
        'singular_name' => __( 'Domain' , 'mk' ),
        'add_new' => __( 'Add Domain' , 'mk' ),
        'add_new_item' => __( 'Add Domain' , 'mk' ),
        'edit_item' => __( 'Edit Domain' , 'mk' ),
        'new_item' => __( 'Add Domain' , 'mk' ),
        'view_item' => __( 'View Domain' , 'mk' ),
        'search_items' => __( 'Search Domains' , 'mk' ),
        'not_found' =>  __( 'No Restricted Domains' , 'mk' ),
        'not_found_in_trash' => __( 'No Domains found in Trash' , 'mk' ),
    );
    $args = array(
        'labels' => $labels,
        'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
        'publicly_queryable' => false,  // you should be able to query it
        'show_ui' => true,  // you should be able to edit it in wp-admin
        'exclude_from_search' => true,  // you should exclude it from search results
        'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
        'has_archive' => false,  // it shouldn't have archive page
        'rewrite' => false,  // it shouldn't have rewrite rules
        'menu_icon' => 'dashicons-email-alt',
        'supports' => array(
            'title',
        ),

    );
    register_post_type( 'mk_domain', $args );
}
add_action( 'init', 'red_register_post_type' );

function red_is_valid_email_domain($login, $email, $errors ){
  $posts = new WP_Query(
    array(
      'post_type' => 'mk_domain',
      'posts_per_page'   => -1,
    )
  ); //retrieve posts
  $domains = array();
  if($posts->have_posts() ) {
      while($posts->have_posts() ) {
          $posts->the_post();
  				array_push($domains,strtolower(get_the_title()));
      }
  }
   $valid_email_domains = $domains;// whitelist email domain lists
   $valid = false;
   foreach( $valid_email_domains as $d ){
     $d_length = strlen( $d );
     $current_email_domain = strtolower( substr( $email, -($d_length), $d_length));
     if( $current_email_domain == strtolower($d) ){
       $valid = true;
       break;
     }
   }
   // if invalid, return error message
   if( $valid === false ){
       $error_str = "<strong>ERROR</strong>: you can only register using";
       foreach( $domains as $d ){
           $error_str .=" @$d, ";
       }
       $error_str .= "emails";
       $errors->add('domain_whitelist_error',__( $error_str ));
   }
}
add_action('register_post', 'red_is_valid_email_domain',10,3 );

function red_wpb_admin_notice_info() {
echo '<div class="notice notice-info is-dismissible">
      <p>Thank you for using Restrict Email Domains plugin, For any inquiries or you want to hire me please contact me through email <a href="mailto:kasstechweb@gmail.com">kasstechweb@gmail.com</a></p>
      </div>';
}
add_action( 'admin_notices', 'red_wpb_admin_notice_info' );
