<?php

function LH_relationships_create_place_post_type() {

  $labels = array(
    'name' => _x('Places', 'post type general name'),
    'singular_name' => _x('Place', 'post type singular name'),
    'add_new' => _x('Add New', 'place'),
    'add_new_item' => __('Add new Place'),
    'edit_item' => __('Edit Place'),
    'new_item' => __('New Place'),
    'view_item' => __('View Place'),
    'search_items' => __('Search Places'),
    'not_found' =>  __('No Places found'),
    'not_found_in_trash' => __('No places found in Trash'), 
  );



    	$portfolio_args = array(
        	'label' => __('Places'),
        	'singular_label' => __('Place'),
        	'public' => true,
        	'show_ui' => true,
        	'capability_type' => 'post',
		'rewrite' => array(
			'slug' => 'uri',
			'with_front' => false
			),
		'has_archive' => 'uris',
		'hierarchical' => true,
 		'labels' => $labels,
        	'supports' => array('title', 'editor', 'custom-fields', 'thumbnail')
        );
    	register_post_type('place',$portfolio_args);
	}


add_action('init', 'LH_relationships_create_place_post_type');



function lh_relationships_add_guid_meta_editor_place(){

add_meta_box("Edit_GUID", "Edit GUID", "lh_relationships_edit_guid", "place", "advanced", "low");

}


add_action("admin_init", "lh_relationships_add_guid_meta_editor_place");


?>