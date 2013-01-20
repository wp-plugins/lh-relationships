<?php

add_action('init', 'lh_relationships_create_uri_post_type');


function lh_relationships_create_uri_post_type() {

  $labels = array(
    'name' => _x('Uris', 'post type general name'),
    'singular_name' => _x('Uri', 'post type singular name'),
    'add_new' => _x('Add New', 'uri'),
    'add_new_item' => __('Add new Uri'),
    'edit_item' => __('Edit Uri'),
    'new_item' => __('New Uri'),
    'view_item' => __('View Uri'),
    'search_items' => __('Search Uris'),
    'not_found' =>  __('No uris found'),
    'not_found_in_trash' => __('No uris found in Trash'), 
  );



    	$portfolio_args = array(
        	'label' => __('Uris'),
        	'singular_label' => __('Uri'),
        	'public' => true,
        	'show_ui' => true,
        	'capability_type' => 'post',
		'rewrite' => array(
			'slug' => 'uri',
			'with_front' => false
			),
		'hierarchical' => true,
 		'labels' => $labels,
        	'supports' => array('title', 'editor', 'author', 'custom-fields', 'thumbnail', 'page-attributes')
        );
    	register_post_type('lh-uri',$portfolio_args);
	}






function lh_relationships_add_guid_meta_editor(){

add_meta_box("Edit_GUID", "Edit GUID", "lh_relationships_edit_guid", "lh-uri", "advanced", "low");

}


add_action("admin_init", "lh_relationships_add_guid_meta_editor");

add_action('save_post', 'LH_relationships_update_post_GUID');

function lh_relationships_edit_guid(){
global $post;

?>

<div id="edit_guid">
<label>This is the current GUID URI</label>
<br/>
<input name="new_GUID" size="75" value="<?php echo $post->guid; ?>" />
</div>

<?php
}

function LH_relationships_update_post_GUID(){
global $post;
global $wpdb;


if ($_POST["new_GUID"]){

$query = "UPDATE ".$wpdb->prefix."posts SET guid = '".$_POST["new_GUID"]."' WHERE ID = '".$post->ID."'";

$wpdb->query($query);

}

}



?>