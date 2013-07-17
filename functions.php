<?php


function lh_relationships_create_attribute_guid($namespace_guid, $fragment) {

$str = $namespace_guid;
$last = $str[strlen($str)-1];

if ($last == "/"){

$last = $namespace_guid.$fragment;

} else if ($last == "#"){

$last = $namespace_guid.$fragment;

} else {

$last = $namespace_guid."/".$fragment;

}

return $last;

}


function lh_relationships_create_attribute_uri_post($namespaceId, $fragment, $urionly=null) {



global $wpdb;

global $user_ID; 

$lhrdf_sql = "SELECT a.ID, a.guid FROM ".$wpdb->prefix."posts a, ".$wpdb->prefix."namespace b WHERE  a.ID = b.PostsId and b.Id = '".$namespaceId."'";

$results = $wpdb->get_results($lhrdf_sql);

$parent = $results[0]->ID;

if ($results[0]->guid){

$post_guid = lh_relationships_create_attribute_guid($results[0]->guid, $fragment); 

$lhrdf_sql = "SELECT ID FROM ".$wpdb->prefix."posts where guid = '".$post_guid."'";

$results = $wpdb->get_results($lhrdf_sql);

if (!$results[0]->ID){

$new_post = array(  

'post_title' => $fragment." attribute",  

'post_content' => 'Lorem ipsum dolor sit amet...',  

'post_status' => 'publish',  

'post_name' => $fragment,

'post_parent' => $parent,

'guid' => $post_guid,

'post_author' => $user_ID,  

'post_type' => 'lh-uri'
);  

$post_id = wp_insert_post($new_post);

} else {

$post_id = $results[0]->ID;

  $my_post = array();
  $my_post['ID'] = $post_id;
  $my_post['post_parent'] = $parent;
  wp_update_post( $my_post );


}

if (!$urionly){

$lhrdf_sql = "INSERT INTO ".$wpdb->prefix."predicate ( Id, NamespaceId, fragment, AttributeId) VALUES (NULL, '".$namespaceId."', '".$fragment."', '".$post_id."')";

$results = $wpdb->get_results($lhrdf_sql);

}

}

}





function lh_relationships_create_namespace_post($namespace_name, $namespace_guid, $prefix, $wp_ns) {

global $wpdb;

$lh_sql = "SELECT ID, guid FROM ".$wpdb->prefix."posts where guid = '".$namespace_guid."'"; 


$results = $wpdb->get_results($lh_sql);

//$post_id = $results[0]->ID;

if (!$results[0]->guid){

global $user_ID;  

$new_post = array(  

'post_title' => $namespace_name,

'post_content' => $namespace_name." info",  

'post_status' => 'publish',  

'post_name' => $prefix,

'guid' => $namespace_guid,

'post_author' => $user_ID,  

'post_type' => 'lh-uri'
);  

$post_id = wp_insert_post($new_post);

$lhrdf_sql = "INSERT INTO ".$wpdb->prefix."namespace ( Id , PostsId , prefix , wp_ns ) VALUES ( NULL , '".$post_id."', '".$prefix."', '".$wp_ns."' )";

$results = $wpdb->get_results($lhrdf_sql);

$lastid = $wpdb->insert_id;

return $lastid;

} else {

$lhrdf_sql = "UPDATE shf_namespace SET PostsId = '".$results[0]->ID."' WHERE prefix = '".$prefix."'";
$foobar = $wpdb->get_results($lhrdf_sql);


$lhrdf_sql = "SELECT b.Id FROM ".$wpdb->prefix."posts a, ".$wpdb->prefix."namespace b WHERE  a.Id = b.PostsId and a.guid ='".$results[0]->guid."'";

$results = $wpdb->get_results($lhrdf_sql);


$return = $results[0]->Id;

return $return;


}

}


function lh_relationships_create_plain_uri_post($uri_name, $uri_guid) {

global $wpdb;

$lh_sql = "SELECT * FROM ".$wpdb->prefix."posts where guid = '".$uri_guid."'"; 


$results = $wpdb->get_results($lh_sql);


if (!$results[0]->guid){

global $user_ID;  

$new_post = array(  

'post_title' => $uri_name,

'post_content' => $uri_name." post to creat statmenst with",  

'post_status' => 'publish',  

'guid' => $uri_guid,

'post_author' => $user_ID,  

'post_type' => 'uri'
);  

$post_id = wp_insert_post($new_post);

return $post_id;

} else {


return $results[0]->ID;


}

}



function lh_relationships_isValidURL($url){
return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}





?>