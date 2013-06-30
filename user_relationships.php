<?php

add_action( 'show_user_profile', 'lh_relationships_edit_user_statements' );
add_action( 'edit_user_profile', 'lh_relationships_edit_user_statements' );

function lh_relationships_edit_user_statements( $user ) {

global $wpdb;

echo "<h3>User Relationships</h3>";

$guid = get_author_posts_url($user->data->ID);

$results = lh_relationships_return_rdf($guid);

$j = 0;

echo "<ol>";

while ($j < count($results)) {

echo "<li>";

echo "<a href =\"".$results[$j]->namespace.$results[$j]->predicate."\">".$results[$j]->prefix.":".$results[$j]->fragment."</a>";

echo " - ";

echo "<a href =\"".$results[$j]->object."\">".$results[$j]->object."</a>";

echo "</li>";

$j++;
}

echo "</ol>";


echo "<h3>Add Relationships</h3>";


$sql = "SELECT a.ID AS predicateId, b.prefix, a.fragment FROM ".$wpdb->prefix."predicate a, ".$wpdb->prefix."namespace b, ".$wpdb->prefix."posts c WHERE a.NamespaceId = b.Id AND a.AttributeId = c.ID";


$results = $wpdb->get_results($sql);

//print_r($results);

echo "<br/><select id=\"statement_details\" name=\"statement_details\">
<option value=\"#NONE#\">&mdash; Select &mdash;</option>";


$j = 0;

while ($j < count($results)) {

echo "<option value =\"".$results[$j]->prefix.":".$results[$j]->fragment."\">".$results[$j]->prefix.":".$results[$j]->fragment."</option>";

$j++;
}



		
echo "</select><input name=\"OjectId\" length=\"8\" id=\"OjectId\" />";



 }


add_action( 'personal_options_update', 'lh_relationships_add_user_statement' );
add_action( 'edit_user_profile_update', 'lh_relationships_add_user_statement' );

function lh_relationships_add_user_statement( $user_id ) {


if ( !current_user_can( 'edit_user', $user_id ) ){
		return false;

} else {

if ($_POST['statement_details'] != "#NONE#"){

$uri_guid = get_author_posts_url($user_id);

$id = lh_relationships_create_plain_uri_post("foo", $uri_guid);

$pieces = explode(":", $_POST['statement_details']);

$prefix = $pieces[0];

$fragment = $pieces[1];

lh_relationships_create_rdf_statement($id, $prefix, $fragment, $_POST['OjectId']);



}

}
}




?>