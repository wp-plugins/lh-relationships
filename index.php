<?php
/*
Plugin Name: LH Relationships
Plugin URI: http://localhero.biz/
Description: Add RDF relationship support to Wordpress
Version: 0.06
Author: Peter Shaw
Author URI: http://shawfactor.com/

== Changelog ==

= 0.01 =
* Added query to return RDF

= 0.02 =
Namespace Listing's

= 0.03 =
Basic menu's

= 0.04 =
Improved menu's

= 0.05 =
Attribute Listing's

= 0.06 =
Automatically create namespaces and attributes

Copyright 2011  Peter Shaw  (email : pete@localhero.biz)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


include_once('system.php');

function LH_relationships_create_attribute_guid($namespace_guid, $fragment) {

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


function LH_relationships_create_attribute_uri_post($namespaceId, $fragment) {

global $wpdb;

global $user_ID; 

$lhrdf_sql = "SELECT a.ID, a.guid FROM ".$wpdb->prefix."posts a, ".$wpdb->prefix."namespace b WHERE  a.ID = b.PostsId and b.Id = '".$namespaceId."'";

$results = $wpdb->get_results($lhrdf_sql);

$parent = $results[0]->ID;

if ($results[0]->guid){

$post_guid = LH_relationships_create_attribute_guid($results[0]->guid, $fragment); 

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

'post_type' => 'uri'
);  

$post_id = wp_insert_post($new_post);

} else {

$post_id = $results[0]->ID;

  $my_post = array();
  $my_post['ID'] = $post_id;
  $my_post['post_parent'] = $parent;
  wp_update_post( $my_post );


}

$lhrdf_sql = "INSERT INTO ".$wpdb->prefix."predicate ( Id, NamespaceId, fragment, AttributeId) VALUES (NULL, '".$namespaceId."', '".$fragment."', '".$post_id."')";

$results = $wpdb->get_results($lhrdf_sql);

}

}





function LH_relationships_create_namespace_post($namespace_name, $namespace_guid, $prefix, $wp_ns) {

global $wpdb;

$lh_sql = "SELECT guid FROM ".$wpdb->prefix."posts where guid = '".$namespace_guid."'"; 


$results = $wpdb->get_results($lh_sql);


if (!$results[0]->guid){

global $user_ID;  

$new_post = array(  

'post_title' => $namespace_name,

'post_content' => $namespace_name." info",  

'post_status' => 'publish',  

'post_name' => $prefix,

'guid' => $namespace_guid,

'post_author' => $user_ID,  

'post_type' => 'uri'
);  

$post_id = wp_insert_post($new_post);

$lhrdf_sql = "INSERT INTO ".$wpdb->prefix."namespace ( Id , PostsId , prefix , wp_ns ) VALUES ( NULL , '".$post_id."', '".$prefix."', '".$wp_ns."' )";

$results = $wpdb->get_results($lhrdf_sql);

$lastid = $wpdb->insert_id;

return $lastid;

} else {

$lhrdf_sql = "SELECT b.Id FROM ".$wpdb->prefix."posts a, ".$wpdb->prefix."namespace b WHERE  a.Id = b.PostsId and a.guid ='".$results[0]->guid."'";

$results = $wpdb->get_results($lhrdf_sql);

$return = $results[0]->Id;

return $return;


}

}


function isValidURL($url){
return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}


function lhrdf_register_activation_hook() {

global $wpdb;

$query = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."namespace ( `Id` bigint(20) unsigned NOT NULL auto_increment, `PostsId` bigint(20) NOT NULL, `prefix` varchar(7) character set utf8 NOT NULL, `wp_ns` enum('yes','no') NOT NULL default 'no', PRIMARY KEY  (`Id`), UNIQUE KEY `PostsId` (`PostsId`), UNIQUE KEY `prefix` (`prefix`)) DEFAULT CHARSET=utf8";

$results = $wpdb->get_results($query);

$query = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."predicate (`Id` bigint(20) unsigned NOT NULL auto_increment, `NamespaceId` bigint(20) NOT NULL, `fragment` varchar(64) character set utf8 NOT NULL, `AttributeId` bigint(20) NOT NULL, PRIMARY KEY  (`Id`), UNIQUE KEY AttributeId (`AttributeId`)) DEFAULT CHARSET=utf8";

$results = $wpdb->get_results($query);

$query = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."statement` ( `Id` bigint(20) unsigned NOT NULL auto_increment, `SubjectId` bigint(20) unsigned NOT NULL, `PredicateId` bigint(20) unsigned NOT NULL, `OjectId` bigint(20) unsigned NOT NULL, `namespace_val` varchar(250) default NULL, `prefix_val` varchar(4) default NULL, `attribute_val` varchar(20) default NULL, PRIMARY KEY  (`Id`)) DEFAULT CHARSET=utf8";

$results = $wpdb->get_results($query);

$foo = LH_relationships_create_namespace_post("RDF NS", "http://www.w3.org/1999/02/22-rdf-syntax-ns#", "rdf", "yes");

$foo = LH_relationships_create_namespace_post("Friend of a friend", "http://xmlns.com/foaf/0.1/", "foaf", "no"); 

$foo = LH_relationships_create_namespace_post("SIOC ns", "http://rdfs.org/sioc/ns#", "sioc", "no");

$bar = LH_relationships_create_attribute_uri_post($foo, "topic");

$foo = LH_relationships_create_namespace_post("RDF types ns", "http://www.w3.org/2000/01/rdf-schema#", "rdfs", "no");

$bar = LH_relationships_create_attribute_uri_post($foo, "seeAlso");

$foo = LH_relationships_create_namespace_post("SKOS schema", "http://www.w3.org/2004/02/skos/core#", "skos", "no");

$foo = LH_relationships_create_namespace_post("Meaning of tag", "http://moat-project.org/ns#", "moat", "no");

$foo = LH_relationships_create_namespace_post("LocalHero namespace", "http://localhero.biz/uri/localhero-namespace/", "lh", "no");

$foo = LH_relationships_create_namespace_post("Admin namespace", "http://webns.net/mvcb/", "admin", "yes");

$foo = LH_relationships_create_namespace_post("Content module", "http://purl.org/rss/1.0/modules/content/", "content", "yes");

$foo = LH_relationships_create_namespace_post("Dublin Core module", "http://purl.org/dc/elements/1.1/", "dc", "yes");

$foo = LH_relationships_create_namespace_post("Dublin Core terms module", "http://purl.org/dc/terms/", "dcterms", "no");

$foo = LH_relationships_create_namespace_post("SIOC terms module", "http://rdfs.org/sioc/types#", "sioct", "no");

$foo = LH_relationships_create_namespace_post("tag module", "http://www.holygoat.co.uk/owl/redwood/0.1/tags/", "tag", "no");

$foo = LH_relationships_create_namespace_post("Georss module", "http://www.georss.org/georss", "georss", "no");


}



register_activation_hook(__FILE__, 'lhrdf_register_activation_hook' );


function return_rdf($guid){

global $wpdb;

$lhrdf_sql = "SELECT b.guid AS subject, e.guid as predicate, d.prefix AS prefix, c.fragment AS fragment, f.guid AS object FROM ".$wpdb->prefix."statement a, ".$wpdb->prefix."posts b, ".$wpdb->prefix."predicate c, ".$wpdb->prefix."namespace d, ".$wpdb->prefix."posts e, ".$wpdb->prefix."posts f WHERE  a.SubjectId = b.ID AND a.PredicateId = c.Id AND c.AttributeId = e.ID AND c.NamespaceId = d.Id AND a.OjectId = f.ID AND b.guid = '".$guid."'";

$results = $wpdb->get_results($lhrdf_sql);

return $results;

}

//Add extra namespaces and data to the normal rdf feed

function return_namespace(){

global $wpdb;

$sql = "SELECT b.guid as namespace, a.prefix as prefix FROM ".$wpdb->prefix."namespace a, ".$wpdb->prefix."posts b WHERE a.PostsId = b.Id and a.wp_ns = 'no'";

//echo $sql;

$results = $wpdb->get_results($sql);

return $results;

}

//Add extra namespaces and data to the lh-relationship compliant rdf feeds


function LH_relationships_return_compliant_namespace(){

global $wpdb;

$sql = "SELECT b.guid as namespace, a.prefix as prefix FROM ".$wpdb->prefix."namespace a, ".$wpdb->prefix."posts b WHERE a.PostsId = b.Id";

$results = $wpdb->get_results($sql);


return $results;

}



function create_rdf_statement($subject,$prefix,$fragment,$object){

if ($subject && $prefix && $fragment && $object){

global $wpdb;

$sql = "SELECT ".$wpdb->prefix."predicate.Id from ".$wpdb->prefix."predicate, ".$wpdb->prefix."namespace, ".$wpdb->prefix."attribute where ".$wpdb->prefix."predicate.NamespaceId = ".$wpdb->prefix."namespace.Id and ".$wpdb->prefix."predicate.AttributeId = ".$wpdb->prefix."attribute.Id and ".$wpdb->prefix."namespace.prefix = '".$prefix."' and ".$wpdb->prefix."attribute.fragment = '".$fragment."'";

$results = $wpdb->get_results($sql);

$sql = "SELECT Id from ".$wpdb->prefix."statement where SubjectId = '".$subject."' and PredicateId = '".$results[0]->Id."' and OjectId = '".$object."'";

$foo = $wpdb->get_results($sql);

if (!$foo[0]->Id){

$sql = "INSERT INTO ".$wpdb->prefix."statement ( Id , SubjectId , PredicateId , OjectId , namespace_val , prefix_val , attribute_val ) VALUES ( '', '".$subject."', '".$results[0]->Id."', '".$object."', NULL , NULL , NULL )";

$results = $wpdb->get_results($sql);


}

}

}




function add_rdf_namespace() {

$lhrdfnamespaces = return_namespace();

$j = 0;

while ($j < count($lhrdfnamespaces)) {

echo "xmlns:".$lhrdfnamespaces[$j]->prefix."=\"".$lhrdfnamespaces[$j]->namespace."\"
";

$j++;
}

}

function LH_relationships_add_compliant_rdf_namespace() {



$lhrdfnamespaces = LH_relationships_return_compliant_namespace();

$j = 0;

while ($j < count($lhrdfnamespaces)) {

echo "xmlns:".$lhrdfnamespaces[$j]->prefix."=\"".$lhrdfnamespaces[$j]->namespace."\"
";

$j++;
}

}



function add_rdf_nodes() {

global $post;

$foo = return_rdf($post->guid);

//echo "here we add";


$j = 0;

while ($j < count($foo)) {

echo "<".$foo[$j]->prefix.":".$foo[$j]->fragment." rdf:resource=\"".htmlspecialchars($foo[$j]->object)."\" />";


$j++;
}


}






function add_rdf_vals() {

global $lhrdfnamespaces;

global $post;

global $wpdb;

$j = 0;

$sql = "SELECT meta_key, meta_value FROM ".$wpdb->prefix."postmeta where post_id = '".$post->ID."' and (meta_key like";

while ($j < count($lhrdfnamespaces)) {


if ($j < 1){

$sql .= " '".$lhrdfnamespaces[$j]->prefix.":%'";

} else {

$sql .= " or meta_key like '".$lhrdfnamespaces[$j]->prefix.":%'";

}


$j++;
}

$sql .= ")";

//echo $sql;

$results = $wpdb->get_results($sql);

$j = 0;

while ($j < count($results)) {

if(!isValidURL($results[$j]->meta_value)){

echo "<".$results[$j]->meta_key.">".$results[$j]->meta_value."</".$results[$j]->meta_key.">";

}

$j++;

}

}



function add_see_also() {

if (!is_single()){

$post_type = get_query_var('post_type');

if (!$post_type){

$post_type = "post";


}

$count_posts = wp_count_posts($post_type);

$published_posts = $count_posts->publish;

$pageNumber = (get_query_var('paged')) ? get_query_var('paged') : 1;

$per_page = get_query_var('posts_per_page');



$pages =  $published_posts / $per_page;

$pages = ceil($pages);


if ($pageNumber == '1'){

$j = 1;

while ($j <= $pages) {

if ($j == '1'){


} else {

echo "<rdfs:seeAlso rdf:resource=\"";
bloginfo('url');
echo "/page/".$j."/?feed=rdf";

if ($post_type != 'post'){

echo "&post_type=".$post_type;

}

echo "\"/>\n";
}
$j++;
}

}
}
}

function lh_the_taxonomy_to_rdf() {

global $post;
	
$categories = get_the_category();

$j = 0;

while ($j < count($categories)) {


echo "\t\t<sioc:topic>\n<skos:Concept rdf:about=\"";

echo get_category_link($categories[$j]->cat_ID);

echo"\">";

echo "<skos:prefLabel xml:lang=\"en\">".$categories[$j]->category_nicename."</skos:prefLabel>";

echo "</skos:Concept>\n</sioc:topic>\n";

$j++;
}

$tags = get_the_tags();

if (is_array($tags)){

$tags = array_values($tags);

}

if ($tags[0]){


$j = 0;

while ($j < count($tags)) {

echo "<tag:RestrictedTagging>\n<tag:taggedResource rdf:about=\"";

the_permalink_rss();

echo "\">\n";

echo "<tag:associatedTag rdf:resource=\"".get_tag_link($tags[$j]->term_id)."\"/>\n";





echo "<tag:associatedTag rdf:resource=\"";

echo get_tag_link($tags[$j]->term_id);

echo "\"/>\n";


echo "<foaf:maker rdf:resource=\"";


echo get_author_posts_url($post->post_author);


echo "\"/>\n";

echo "<moat:tagMeaning rdf:resource=\"";

echo bloginfo('url');

echo "/dereferencer/taxonomy/tag/".$tags[$j]->name."/";

echo get_the_author_meta('user_nicename',$post->post_author)."/";

echo "\"/>\n";

echo "</tag:taggedResource>\n</tag:RestrictedTagging>\n";

$j++;

}

}

}



add_filter('rdf_header', 'add_see_also');

add_action( 'rdf_ns', 'add_rdf_namespace', 1);

add_filter('language_attributes', 'add_rdf_namespace');

add_action( 'rdf_item', 'add_rdf_nodes', 1);

add_action( 'rdf_item', 'add_rdf_vals', 1);

add_action( 'rdf_item', 'lh_the_taxonomy_to_rdf', 1);

add_action( 'rss2_item', 'skos_the_category_rdf', 1);







function return_sparql($coordinates,$bounds){

$sparql = "PREFIX lh: <http://shawcup.localhero.biz/namespace/lhero/#> prefix dc: <http://purl.org/dc/elements/1.1/> SELECT ?a ?db_id ?date ?o ?lat ?lng WHERE {?a lh:db_id ?db_id . ?a dc:date ?date . ?a lh:has_related_location ?o . ?o lh:geo_near_lat ?lat . ?o lh:geo_near_lng ?lng . FILTER (?lng >= $sw_longitude) . FILTER (?lng <= $ne_longitude) . FILTER (?lat >= $sw_latitude) . FILTER (?lat <= $ne_latitude) } ORDER BY desc(?date) LIMIT 10";





}

include_once('uri_setup.php');




?>