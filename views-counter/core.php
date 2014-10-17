<?php 
    /*
    Plugin Name: Views Counter
    Plugin URI: http://www.shyamachuthan.com/views-counter-plugin
    Description: This plugin will count the number of visits to each post/page (custom post type) and will display the visits to each (frontend & backend).
    Author: Shyam Achuthan
    License: GPLv2 or later
    Version: 1.01
    Author URI: http://www.shyamachuthan.com
    */
?>
<?php 
include('base.php'); 

function initz()
{
	 	 global $wpdb;
   		 $sql="ALTER TABLE {$wpdb->prefix}posts ADD COLUMN vc_views INT DEFAULT 0";
   		 $wpdb->query($sql);
}
function vc_init()
{
	 global $wpdb;
	 if(is_single())
	 {
	 	$sql="UPDATE {$wpdb->prefix}posts SET vc_views=vc_views+1 where ID=".$GLOBALS['post']->ID;
  	 	$wpdb->query($sql);
	 }
	 
}
function add_views_counter($content)
{	
  
  $label='';
  if(get_option( 'vc_show_frontend' ))
  {
  	$label=<<<LABEL
  	<p class="vc-container">
  		<span class="vc-views-value">Views : {$GLOBALS['post']->vc_views}</span>
  	</p>
LABEL;
  }
  
  return $content.$label;
}




function VC_column_head($defaults)
{
    $defaults['views_counter'] = 'Views';
    return $defaults;
}
function VC_column_content($column_name, $post_ID)
{
    if ($column_name == 'views_counter') {
        $post_views = get_post($post_ID)->vc_views;
        echo $post_views;
        }
}

function views_counter_options()
{
	if(isset($_POST['visit_counter_save']))
	{
		if($_POST['showonfront']==1)
		{
			update_option( 'vc_show_frontend', TRUE );
		}
		else
		{
			update_option( 'vc_show_frontend', FALSE );			
		}
	}
	
	
	include('settings.php');
}

function views_counter_scripts() {
	wp_enqueue_style( 'views-counter', plugins_url( 'frontend.css' , __FILE__ ));
	
}

function vc_admin_init()
{
	add_options_page( 'Views Counter Options', 'Views Counter','manage_options', 'views-counter-settings','views_counter_options');
}
function vc_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=views-counter-settings">Configure</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

$plugin = plugin_basename(__FILE__);

 
add_filter("plugin_action_links_$plugin", 'vc_plugin_settings_link' );
register_activation_hook( __FILE__, 'initz' );
add_action( 'get_header', 'vc_init' );
add_action( 'admin_menu', 'vc_admin_init' );
add_action( 'wp_enqueue_scripts', 'views_counter_scripts' );
add_filter( 'the_content', 'add_views_counter' );
add_filter('manage_pages_columns', 'VC_column_head');
add_filter('manage_posts_columns', 'VC_column_head');
add_action('manage_posts_custom_column', 'VC_column_content', 3, 2);
add_action('manage_pages_custom_column', 'VC_column_content', 3, 2);



?>