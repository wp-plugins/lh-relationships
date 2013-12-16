<?php

function lh_relationships_type_metaboxes() {
	add_meta_box( 'lh_relationships_post_type', 'Post type', 'lh_relationships_type_form', 'post', 'side', 'high', array( 'id' => 'start') );
	add_meta_box( 'lh_relationships_post_type', 'Page type', 'lh_relationships_type_form', 'page', 'side', 'high', array( 'id' => 'start') );

}
add_action( 'admin_init', 'lh_relationships_type_metaboxes' );
// Metabox HTML
function lh_relationships_type_form($post, $args) {
	global $post, $wp_locale;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'lh_relationships_type_nonce' );

echo "<select name=\"lh_relationships_rdf_type\">
  <option value=\"\" selected=\"selected\">This is about a:</option>
  <option value=\"http://www.w3.org/2002/12/cal#Vevent\">Event</option>
  <option value=\"http://dbpedia.org/ontology/place\">Place</option>
</select>";



}

function lh_relationships_update_uri_type(){
global $post;
global $wpdb;

if ($_POST["lh_relationships_rdf_type"]){

$lhrdf_sql = "SELECT ID FROM ".$wpdb->prefix."posts WHERE  ".$wpdb->prefix."posts.guid ='".$_POST["lh_relationships_rdf_type"]."'";

$results = $wpdb->get_results($lhrdf_sql);

lh_relationships_create_rdf_statement($post->ID,"rdf","type",$results[0]->ID);

}

}

add_action('save_post', 'lh_relationships_update_uri_type');

add_action('save_page', 'lh_relationships_update_uri_type');




function lh_relationships_event_date($arg) {

	$metabox_id = $arg;
	global $post, $wp_locale;
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'ep_eventposts_nonce' );
	$time_adj = current_time( 'timestamp' );

$ical_time = get_post_meta( $post->ID, 'ical:dt'.$metabox_id, true );

if ($ical_time){ $dtcheck = get_date_from_gmt(date( 'Y-m-d H:i:s', strtotime($ical_time))); }


if ($ical_time){	$month = date( 'm', strtotime($dtcheck)); }
	if ( empty( $month ) ) {
		$month = gmdate( 'm', $time_adj );
	}

if ($ical_time){	$day = date( 'd', strtotime($dtcheck)); }
	if ( empty( $day ) ) {
		$day = gmdate( 'd', $time_adj );
	}

if ($ical_time){	$year = date( 'Y', strtotime($dtcheck)); }
	if ( empty( $year ) ) {
		$year = gmdate( 'Y', $time_adj );
	}

if ($ical_time){	$hour = date( 'H', strtotime($dtcheck)); }
    if ( empty($hour) ) {
        $hour = gmdate( 'H', $time_adj );
    }

if ($ical_time){	$min = date( 'i', strtotime($dtcheck)); }
    if ( empty($min) ) {
        $min = '00';
    }
	$month_s = '<select name="' . $metabox_id . '_month">';
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$month_s .= "\t\t\t" . '<option value="' . zeroise( $i, 2 ) . '"';
		if ( $i == $month )
			$month_s .= ' selected="selected"';
		$month_s .= '>' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
	}
	$month_s .= '</select>';
	echo $month_s;
	echo '<input type="text" name="' . $metabox_id . '_day" value="' . $day  . '" size="2" maxlength="2" />';
    echo '<input type="text" name="' . $metabox_id . '_year" value="' . $year . '" size="4" maxlength="4" /> @ ';
    echo '<input type="text" name="' . $metabox_id . '_hour" value="' . $hour . '" size="2" maxlength="2"/>:';
    echo '<input type="text" name="' . $metabox_id . '_minute" value="' . $min . '" size="2" maxlength="2" />';
}

// Save the Metabox Data
function lh_relationships_save_events_meta( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	if ( !isset( $_POST['ep_eventposts_nonce'] ) )
		return;
	if ( !wp_verify_nonce( $_POST['ep_eventposts_nonce'], plugin_basename( __FILE__ ) ) )
		return;
	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ) )
		return;
	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though
	$metabox_ids = array( 'start', 'end' );
	foreach ($metabox_ids as $key ) {
	    $aa = $_POST[$key . '_year'];
		$mm = $_POST[$key . '_month'];
		$jj = $_POST[$key . '_day'];
		$hh = $_POST[$key . '_hour'];
		$mn = $_POST[$key . '_minute'];
		$aa = ($aa <= 0 ) ? date('Y') : $aa;
		$mm = ($mm <= 0 ) ? date('n') : $mm;
		$jj = sprintf('%02d',$jj);
		$jj = ($jj > 31 ) ? 31 : $jj;
		$jj = ($jj <= 0 ) ? date('j') : $jj;
		$hh = sprintf('%02d',$hh);
		$hh = ($hh > 23 ) ? 23 : $hh;
		$mn = sprintf('%02d',$mn);
		$mn = ($mn > 59 ) ? 59 : $mn;
$events_meta['ical:dt'.$key] = date( 'Y-m-d\TH:i:s\Z', strtotime(get_gmt_from_date($aa."-".$mm."-".$jj." ".$hh.":".$mn.":00")));

        }
	// Add values of $events_meta as custom fields
	foreach ( $events_meta as $key => $value ) { // Cycle through the $events_meta array!
		if ( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode( ',', (array)$value ); // If $value is an array, make it a CSV (unlikely)
		if ( get_post_meta( $post->ID, $key, FALSE ) ) { // If the custom field already has a value
			update_post_meta( $post->ID, $key, $value );
		} else { // If the custom field doesn't have a value
			add_post_meta( $post->ID, $key, $value );
		}
		if ( !$value ) delete_post_meta( $post->ID, $key ); // Delete if blank
	}
}

add_action( 'save_post', 'lh_relationships_save_events_meta', 1, 2 );




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

if ($foo[0]->object == "http://www.w3.org/2002/12/cal#Vevent"){

echo "<h3>Event start</h3>";

lh_relationships_event_date("start");

echo "<h3>Event End</h3>";

lh_relationships_event_date("end");


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