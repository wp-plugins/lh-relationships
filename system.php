<?php


add_action("admin_init", "add_rdf_statement_ui");


function add_rdf_statement_ui(){

add_meta_box("statement_details", "Add RDF post relationship", "add_rdf_statement", "post", "normal", "low");

}


function add_rdf_statement(){
global $post;
global $wpdb;

$results = return_rdf($post->guid);

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



$sql = "SELECT a.ID AS predicateId, b.prefix, a.fragment FROM ".$wpdb->prefix."predicate a, ".$wpdb->prefix."namespace b, ".$wpdb->prefix."posts c WHERE a.NamespaceId = b.Id AND a.AttributeId = c.ID";


$results = $wpdb->get_results($sql);

//print_r($results);

echo "<br/><select id=\"statement_details\" name=\"statement_details\">
<option value=\"#NONE#\">&mdash; Select &mdash;</option>";


$j = 0;

while ($j < count($results)) {

echo "<option value =\"".$results[$j]->predicateId."\">".$results[$j]->prefix.":".$results[$j]->fragment."</option>";

$j++;
}



		
echo "</select><input name=\"OjectId\" length=\"8\" id=\"OjectId\" />";



}


?>