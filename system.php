<?php

function lh_relationships_place_output(){
global $post;

$custom = get_post_custom($post->ID);


if (!$custom["wgs84:lat"][0]){

$custom["wgs84:lat"][0] = "-34.397";

}

if (!$custom["wgs84:long"][0]){

$custom["wgs84:long"][0] = "150.644";

}


?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true">/**/</script>
<div id="map_canvas" style="width: 100%; height: 300px"></div>
<div id="geo_options">
<label>Located near this latitude</label>
<br/>
<input name="wgs84:lat" id="wgs84:lat" value="<?php echo $custom["wgs84:lat"][0]; ?>" />
<br/>
<label>Located near this longitude</label>
<br/>
<input name="wgs84:long" id="wgs84:long" value="<?php echo $custom["wgs84:long"][0]; ?>" />

</div>
<script type="text/javascript">
  function initialize() {
var myLatlng = new google.maps.LatLng(<?php echo $custom["wgs84:lat"][0]; ?>, <?php echo $custom["wgs84:long"][0]; ?>);
    var myOptions = {
      zoom: 12,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

start_marker = new google.maps.Marker({
			    			                    position: myLatlng,
title: 'Drag Me',
map: map,
draggable: true
            });

google.maps.event.addListener(start_marker, "dragend", function() {

start_marker.point = start_marker.getPosition();

document.getElementById('wgs84:lat').value = start_marker.point.lat();

document.getElementById('wgs84:long').value = start_marker.point.lng();


});

google.maps.event.addListener(map, "bounds_changed", function() {


});

google.maps.event.addListener(map, "rightclick", function(event) {
    
alert(event.latLng);

});

}

initialize();

</script>

<!--end place options-->   
<?php


}


add_action("admin_init", "lh_relationships_add_post_ui");


function lh_relationships_add_post_ui(){

add_meta_box("lh_relationships_statement_details", "RDF post relationships", "lh_relationships_print_rdf_relationships", "post", "normal", "low");

add_meta_box("lh_relationships_statement_details", "RDF post relationships", "lh_relationships_print_rdf_relationships", "page", "normal", "low");

add_meta_box("lh_relationships_statement_details", "RDF post relationships", "lh_relationships_print_rdf_relationships", "lh-uri", "normal", "low");

add_meta_box("lh_relationships_post_interface", "RDF post interface", "lh_relationships_print_rdf_interface", "post", "normal", "low");

add_meta_box("lh_relationships_post_interface", "RDF post interface", "lh_relationships_print_rdf_interface", "page", "normal", "low");

add_meta_box("lh_relationships_post_interface", "RDF post interface", "lh_relationships_print_rdf_interface", "lh-uri", "normal", "low");






}

function lh_relationships_print_rdf_interface(){
global $post;
global $wpdb;

$foo = lh_relationships_return_rdf_by_id($post->ID);


if ($foo[0]->object == "http://dbpedia.org/ontology/place"){


lh_relationships_place_output();


}

}

function lh_relationships_print_rdf_relationships(){
global $post;
global $wpdb;

$results = lh_relationships_return_rdf_by_id($post->ID);

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
<option value=\"#NONE#\">-- Select --</option>";


$j = 0;

while ($j < count($results)) {

echo "<option value =\"".$results[$j]->prefix.":".$results[$j]->fragment."\">".$results[$j]->prefix.":".$results[$j]->fragment."</option>";



$j++;
}



		
echo "</select><input name=\"OjectId\" length=\"8\" id=\"OjectId\" />";



}


function lh_relationships_update_place(){
global $post;

if ($_POST["wgs84:lat"]){
if ($_POST["wgs84:long"]){


update_post_meta($post->ID, "wgs84:lat", $_POST["wgs84:lat"]);
update_post_meta($post->ID, "wgs84:long", $_POST["wgs84:long"]);

}
}

}

add_action('save_post', 'lh_relationships_update_place');

function lh_relationships_add_author_rel_head(){

global $post;

if (is_singular()){

echo "<link rel=\"author\" href=\"".get_author_posts_url($post->post_author)."\"/>";

} else if (is_author()){

$subject = get_author_posts_url($post->post_author);

$triple = lh_relationships_return_sparql_triple($subject, "http://vocab.sindice.com/xfn#me");

if ($triple[0]){

$j = 0;

while ($j < count($triple)) {
echo "<link rel=\"me\" href=\"".$triple[$j]->object."\"/>";
$j++;
}

}


}


}

add_action('wp_head', 'lh_relationships_add_author_rel_head');


function lh_relationships_add_post_statement( $post_id ) {

global $post;



if ( !current_user_can( 'edit_post', $post_id ) ) {

return false;


} else {


if ($_POST['statement_details'] != "#NONE#"){

update_post_meta($post->ID, "foobar", "foob00");

$id = $post->ID;

$pieces = explode(":", $_POST['statement_details']);

$prefix = $pieces[0];

$fragment = $pieces[1];


//update_post_meta($post->ID, "foobar", $prefix);

lh_relationships_create_rdf_statement($id, $prefix, $fragment, $_POST['OjectId']);


}

}


}


add_action('save_post', 'lh_relationships_add_post_statement');



?>