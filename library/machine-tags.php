<?php

function lh_relationships_delete_machine_tag($postid, $taxonomy){

echo "start delete machine tag ".$taxonomy."\n";

$posttags = get_the_tags($postid);

$stack = array();

if ($posttags) {

foreach($posttags as $tag) {

echo $tag->slug." ".$tag->name."\n";

if ($tag->slug != $taxonomy){

array_push($stack,  $tag->name);

}

}

}


$post['ID'] = $postid;

$post['tags_input'] = $stack;

wp_update_post( $post );


}


function lh_relationships_machine_tags_add_relationship($subjectid, $predicateid, $objectid){

global $wpdb;

$sql = "SELECT Id FROM ".$wpdb->prefix."statement where SubjectId = '".$subjectid."' and PredicateId = '".$predicateid."' and OjectId = '".$objectid."'";

echo $sql;

$results = $wpdb->get_results($sql);


if (!$results[0]->Id){


$sql = "INSERT INTO ".$wpdb->prefix."statement ( Id , SubjectId , PredicateId , OjectId ) VALUES ( '', '".$subjectid."', '".$predicateid."', '".$objectid."')";

echo $sql;

$results = $wpdb->get_results($sql);

} else {


echo "relationship already exists";

}


}



function lh_relationships_run_machine_tags(){

global $wpdb;

$query = "SELECT  ".$wpdb->prefix."terms.name, ".$wpdb->prefix."terms.slug FROM ".$wpdb->prefix."terms WHERE ".$wpdb->prefix."terms.name LIKE '%:%=%'";

echo $query;

$results = $wpdb->get_results($query);

print_r($results);

$machinetags = array();

$i = 0;

foreach( $results as $result){

$machinetags[$i] = $result->slug; 

$i++;

}

print_r($machinetags);

$post_types = get_post_types();

$hey = array_values($post_types);

print_r($hey);

$myquery['tax_query'] = array(
    array(
        'taxonomy' => 'post_tag',
        'terms' => $machinetags,
        'field' => 'slug',
    ),
);

$myquery['post_type'] =  $hey;
$myquery['post_status'] = array('publish', 'future', 'private', 'inherit');   

$postinfo = query_posts($myquery);

if (!$postinfo[0]->ID){

echo "no tagged posts";

$bar = get_term_by( "slug", $machinetags[0], "post_tag");

print_r($bar);

if (wp_delete_term( $bar->term_id , "post_tag")){

echo "term deleted";

}

} else {

$subjectid = $postinfo[0]->ID;

echo "the subjectid is ".$subjectid;

$posttags = wp_get_object_terms($postinfo[0]->ID, 'post_tag');

print_r($posttags);

foreach( $posttags as $posttag){

if (strpos($posttag->name,'=') !== false) {

if (strpos($posttag->name,':') !== false) {


$machinetagslug = $posttag->slug;

$machinetagname = $posttag->name;

}

}

}

echo "the slug is".$machinetagslug;

$pieces = explode("=", $machinetagname);

print_r($pieces);

$object = trim($pieces[1], "\"");

echo $object;

$pieces = explode(":", $pieces[0]);

$prefix = $pieces[0];

echo $prefix;

$fragment = $pieces[1];

echo $fragment;

$sql = "SELECT a.ID AS predicateId, b.prefix, a.fragment FROM ".$wpdb->prefix."predicate a, ".$wpdb->prefix."namespace b, ".$wpdb->prefix."posts c WHERE a.NamespaceId = b.Id AND a.AttributeId = c.ID AND b.prefix = '".$prefix."' and a.fragment = '".$fragment."'";

echo $sql;

$predicatearray = $wpdb->get_results($sql);

$predicateid = $predicatearray[0]->predicateId;

echo "predicateid is ".$predicateid;

$sql = "SELECT ID FROM ".$wpdb->prefix."posts a WHERE a.guid = '".$object."'";

echo $sql;

$results = $wpdb->get_results($sql);

$objectid = $results[0]->ID;

echo "objectid is ".$objectid;

if ($subjectid && $predicateid && $objectid){

lh_relationships_machine_tags_add_relationship($subjectid, $predicateid, $objectid);


} else {

if ($subjectid && $predicatearray[0]->prefix && $predicatearray[0]->fragment && !lh_relationships_isValidURL($object)){

echo "not a url";

delete_post_meta($subjectid, $predicatearray[0]->prefix.":".$predicatearray[0]->fragment);

add_post_meta($subjectid, $predicatearray[0]->prefix.":".$predicatearray[0]->fragment, $object);

}

}


lh_relationships_delete_machine_tag($postinfo[0]->ID, $machinetagslug);

}


}


//Cron the run lh_relationships_run_machine_tags function so that this runs automatically


if( !wp_next_scheduled( 'lh_relationships_hourly_event' ) ) {
wp_schedule_event( time(), 'hourly', 'lh_relationships_hourly_event' );
}

add_action( 'lh_relationships_hourly_event', 'lh_relationships_run_machine_tags' );




?>