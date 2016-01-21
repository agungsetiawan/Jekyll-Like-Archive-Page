<?php
/*
 Plugin Name: Jekyll Like Archive Page
 Plugin URI: http://agung-setiawan.com
 Description: This plugin offers a Jekyll minimalistic yet nice style archives page for WordPress.
 Version: 1.0.0
 Author: Agung Setiawan
 Author URI: http://agung-setiawan.com
 License: GPL2
 
Jekyll Like Archive Page is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Jekyll Like Archive Page is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Jekyll Like Archive Page. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// Get the year and post data
function getArchive(){

	// Get years that have posts
	global $wpdb;
    $years = $wpdb->get_results( "SELECT YEAR(post_date) AS year FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year DESC" );

    // Beginning of archive div
    echo '<div class="jekyll-like-body">';

    //  Loop to display years
    foreach ( $years as $year ) {

        // Get posts for each year
        $posts_this_year = $wpdb->get_results( "SELECT ID, post_title, DATE(post_date) as post_date FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish' AND YEAR(post_date) = '" . $year->year . "' ORDER BY post_date DESC" );

        // Display the year
        echo '<h2 class="jekyll-like-year">' . $year->year . '</h2>';

        // Beginning of the ul
        echo '<ul class="jekyll-like-ul">';

        // Loop to display posts on a year
        foreach ( $posts_this_year as $post ) {

            // Formatting the date
            $date = date_create($post->post_date);
			$date = date_format($date,"M d Y");

            // Display post title and its published date
            echo '<li class="jekyll-like-li"><a class="jekyll-like-link" href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a><div class="jekyll-like-date">'. $date .'</div></li>';
        }

        // End of the ul
        echo '</ul>';

        // Give line to looks nicec
        echo '<hr>';
    }

    // End of archive div
    echo '</div>';
	
}

// Make this plugin as shortcode
add_shortcode('jekyll_it', 'getArchive');
// End of Get the year and post data



// Load Noto Serif google font
add_action('wp_head', 'loadGoogleFont');

function loadGoogleFont(){
    echo '<link href="https://fonts.googleapis.com/css?family=Noto+Serif" rel="stylesheet" type="text/css">';
}
// End of Load Noto Serif google font


// Load CSS style
add_action( 'wp_enqueue_scripts', 'loadStyle' );

function loadStyle()
{
    // Register the style
    wp_register_style( 'style', plugins_url( '/css/style.css', __FILE__ ), array(), '20160115', 'all' );
    
    // Enqueue the style:
    wp_enqueue_style( 'style' );

    // Get saved colors or the default
    $jlap_year_color = (get_option('jlap_year_color') != '') ? get_option('jlap_year_color') : '#8e95b8';
    $jlap_title_color = (get_option('jlap_title_color') != '') ? get_option('jlap_title_color') : '#1F1B1B';
    $jlap_date_color = (get_option('jlap_date_color') != '') ? get_option('jlap_date_color') : '#666';

    // Change the color based on above variables
    $custom_css = "
            .jekyll-like-year{
                color: {$jlap_year_color};
            }

            .jekyll-like-li a{
                color: {$jlap_title_color};
            }

            .jekyll-like-date{
                color: {$jlap_date_color};
            }

            .jekyll-like-li a:hover{
                color: {$jlap_title_color};
            }";

    // Load above css as inline
    wp_add_inline_style( 'style', $custom_css );
}
// End of Load CSS style



// Plugin options page
add_action('admin_menu', 'jekyll_like_archive_page_plugin_settings');

// Register the options page
function jekyll_like_archive_page_plugin_settings() {
    add_menu_page('Jekyll Like Archive Page', 'Jekyll Like Archive Page', 'administrator', 'jekyll_like_archive_page_settings', 'jekyll_like_archive_page_display_settings');
    add_action( 'admin_init', 'register_mysettings' );
}

// Register options to be used to hold the colors
function register_mysettings() {
    register_setting( 'myoption-group', 'jlap_year_color' );
    register_setting( 'myoption-group', 'jlap_title_color' );
    register_setting( 'myoption-group', 'jlap_date_color' );
 } 

// Display the options page
function jekyll_like_archive_page_display_settings(){

    // Get saved colors or the default to be displayed on the form
    $jlap_year_color = (get_option('jlap_year_color') != '') ? get_option('jlap_year_color') : '#8e95b8';
    $jlap_title_color = (get_option('jlap_title_color') != '') ? get_option('jlap_title_color') : '#1F1B1B';
    $jlap_date_color = (get_option('jlap_date_color') != '') ? get_option('jlap_date_color') : '#666';

    // Options form
    echo "<h2>Jekyll Like Archive Page Settings</h2>";
    echo '<form action="options.php" method="post">';
    settings_fields('myoption-group');
    echo 'Year color : <input type="text" name="jlap_year_color" value="'.$jlap_year_color.'"/> <br>';
    echo 'Title color : <input type="text" name="jlap_title_color" value="'.$jlap_title_color.'"/><br>';
    echo 'Date color : <input type="text" name="jlap_date_color" value="'.$jlap_date_color.'"/>';
    submit_button();
    echo '</form>'; 

    echo '*Empty the input box and save to change to default color';


}

// Notification
if($_GET['settings-updated']){
    add_action( 'admin_notices', 'my_update_notice' );
}

function my_update_notice() {
    ?>
    <div class="updated notice">
        <p>The colors have been updated, excellent!</p>
    </div>
    <?php
}
// End of Notification

// End of Plugin options page
