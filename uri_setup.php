<?php

add_action('init', 'LH_relationships_create_uri_post_type');

function LH_relationships_create_uri_post_type() {

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
		'has_archive' => 'uris',
		'hierarchical' => true,
 		'labels' => $labels,
        	'supports' => array('title', 'editor', 'custom-fields', 'thumbnail')
        );
    	register_post_type('uri',$portfolio_args);
	}






function LH_relationships_add_guid_meta_editor(){

add_meta_box("Edit_GUID", "Edit GUID", "edit_guid", "uri", "advanced", "low");

}


add_action("admin_init", "LH_relationships_add_guid_meta_editor");
add_action('save_post', 'LH_relationships_update_post_GUID');

function edit_guid(){
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


add_action('admin_menu', 'LH_relationships_plugin_menus');

function LH_relationships_plugin_menus() {

add_submenu_page( 'edit.php?post_type=uri', 'LH Relationships Manage Namespaces', 'Namespaces', 'manage_options', 'LH_relationships_namespace-identifier', 'LH_relationships_namespace_options');

add_submenu_page( 'edit.php?post_type=uri', 'LH Relationships Manage Predicates', 'Predicates', 'manage_options', 'LH_relationships_predicate-identifier', 'LH_relationships_predicate_options');

}



function LH_relationships_namespace_options() {

if (!current_user_can('manage_options')){

wp_die( __('You do not have sufficient permissions to access this page.') );

}

$hidden_field_name = 'LH_relationships_submit_hidden';

echo "<h2>" . __( 'Add Namespace', 'menu-test' ) . "</h2>";



?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Post ID:", 'menu-test' ); ?> 
<input type="text" name="LH_relationships_PostsId" value="" size="20">
</p>

<p><?php _e("Prefix:", 'menu-test' ); ?> 
<input type="text" name="LH_relationships_prefix" value="" size="20">
</p>

<p><?php _e("Is this a WordPress native namespace", 'menu-test' ); ?> 
<select name="LH_relationships_wp_ns">
<option value="yes">Yes</option>
<option value="no" selected="yes">No</option>
</select>
</p>


<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>


<?php

global $wpdb;

if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

$lhrdf_sql = "INSERT INTO ".$wpdb->prefix."namespace ( Id , PostsId , prefix , wp_ns ) VALUES ( NULL , '".$_POST[LH_relationships_PostsId]."', '".$_POST[LH_relationships_prefix]."', '".$_POST[LH_relationships_wp_ns]."' )";

echo $lhrdf_sql;

$results = $wpdb->get_results($lhrdf_sql);

}





$lhrdf_sql = "SELECT * FROM ".$wpdb->prefix."posts a, ".$wpdb->prefix."namespace b WHERE  a.Id = b.PostsId";


echo "<table><tr><th>#</th><th>Post ID</th><th>Title</th><th>Prefix</th><th>GUID</th><th>Information</th></tr>";


$results = $wpdb->get_results($lhrdf_sql);

$i = 0;

while ($i < count($results)) {


echo "<tr>";

echo "<td>".$i."</td>";

echo "<td>".$results[$i]->ID."</td>";

echo "<td>".$results[$i]->post_title."</td>";

echo "<td>".$results[$i]->prefix."</td>";

echo "<td><a href=\"".$results[$i]->guid."\">".$results[$i]->guid."</a></td>";

$permalink = get_permalink($results[$i]->ID);

echo "<td>".$permalink."</td>";


echo "</tr>";



$i++;


}

echo "</table>";


}

function LH_relationships_predicate_options() {

if (!current_user_can('manage_options')){

wp_die( __('You do not have sufficient permissions to access this page.') );

}

$hidden_field_name = 'LH_relationships_submit_hidden';

echo "<h2>" . __( 'Add Predicate', 'menu-test' ) . "</h2>";

global $wpdb;



?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Namespace of attribute", 'menu-test' ); ?> 
<select name="LH_relationships_NamespaceId">

<?php

$lhrdf_sql = "SELECT * FROM ".$wpdb->prefix."posts a, ".$wpdb->prefix."namespace b WHERE  a.Id = b.PostsId";

$results = $wpdb->get_results($lhrdf_sql);

$i = 0;

while ($i < count($results)) {

echo "<option value=\"".$results[$i]->Id."\">".$results[$i]->post_title."</option>";

$i++;


}



?>

</select>
</p>

<p><?php _e("Fragment:", 'menu-test' ); ?> 
<input type="text" name="LH_relationships_fragment" value="" size="64">
</p>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>


<?php



if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

$results = LH_relationships_create_attribute_uri_post($_POST[LH_relationships_NamespaceId], $_POST[LH_relationships_fragment]);

}


$lhrdf_sql = "SELECT * FROM ".$wpdb->prefix."predicate a, ".$wpdb->prefix."namespace b, ".$wpdb->prefix."posts c where a.NamespaceId = b.Id and a.AttributeId = c.Id";

//echo $lhrdf_sql;

$results = $wpdb->get_results($lhrdf_sql);

//print_r($results);


echo "<table><tr><th>#</th><th>Namespace</th><th>Post ID</th><th>Title</th><th>Fragment</th><th>GUID</th><th>Information</th></tr>";



$i = 0;

while ($i < count($results)) {


echo "<tr>";

echo "<td>".$i."</td>";

echo "<td>".$results[$i]->prefix."</td>";

echo "<td>".$results[$i]->AttributeId."</td>";

echo "<td>".$results[$i]->post_title."</td>";

echo "<td>".$results[$i]->fragment."</td>";

echo "<td><a href=\"".$results[$i]->guid."\">".$results[$i]->guid."</a></td>";

$permalink = get_permalink($results[$i]->ID);

echo "<td>".$permalink."</td>";


echo "</tr>";



$i++;


}

echo "</table>";



}



?>