<?php
/*
Plugin Name: LH Relationships
Plugin URI: http://localhero.biz/plugins/lh-relationships/
Description: Add RDF relationship support to Wordpress
Version: 0.21
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

= 0.07 =
Enable author relationships

= 0.08 =
Fixed Bugs

= 0.09 =
Added first widget

= 0.10 =
Added second widget

= 0.11 =
Bug fixes, places post type

= 0.12 =
Dbpedia place linked to post type

= 0.13 =
Open Archives module and FOAF primaryTopic attribute

= 0.14 =
Major rewrite of codebase

= 0.15 =
Function rewrites

= 0.16 =
Error fix

= 0.17 =
Error fix

= 0.18 =
Machine Tag support

= 0.19 =
Fixed HTML namespacing

= 0.20 =
Add events handler

= 0.21 =
Add rdf type metabox

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

include_once('uri_setup.php');

include_once('functions.php');

include_once('activate.php');

include_once('library/machine-tags.php');


register_activation_hook(__FILE__, 'lh_relationships_register_activation_tables' );

register_activation_hook(__FILE__, 'lh_relationships_register_activation_ontologies' );


include_once('menu_items.php');

include_once('the_widgets.php');

function lh_relationships_return_rdf($guid){

global $wpdb;

$lhrdf_sql = "SELECT b.guid AS subject, e.guid as predicate, d.prefix AS prefix, c.fragment AS fragment, f.guid AS object FROM ".$wpdb->prefix."statement a, ".$wpdb->prefix."posts b, ".$wpdb->prefix."predicate c, ".$wpdb->prefix."namespace d, ".$wpdb->prefix."posts e, ".$wpdb->prefix."posts f WHERE  a.SubjectId = b.ID AND a.PredicateId = c.Id AND c.AttributeId = e.ID AND c.NamespaceId = d.Id AND a.OjectId = f.ID AND b.guid = '".$guid."'";



$results = $wpdb->get_results($lhrdf_sql);

return $results;

}

function lh_relationships_return_rdf_by_id($id){

global $wpdb;

$lhrdf_sql = "SELECT b.guid AS subject, e.guid as predicate, d.prefix AS prefix, c.fragment AS fragment, f.guid AS object FROM ".$wpdb->prefix."statement a, ".$wpdb->prefix."posts b, ".$wpdb->prefix."predicate c, ".$wpdb->prefix."namespace d, ".$wpdb->prefix."posts e, ".$wpdb->prefix."posts f WHERE  a.SubjectId = b.ID AND a.PredicateId = c.Id AND c.AttributeId = e.ID AND c.NamespaceId = d.Id AND a.OjectId = f.ID AND b.ID = '".$id."'";

$results = $wpdb->get_results($lhrdf_sql);

return $results;

}

function lh_relationships_return_sparql_triple($subject, $predicate, $object){

global $wpdb;

$lhrdf_sql = "SELECT b.guid AS subject, e.guid as predicate, d.prefix AS prefix, c.fragment AS fragment, f.guid AS object FROM ".$wpdb->prefix."statement a, ".$wpdb->prefix."posts b, ".$wpdb->prefix."predicate c, ".$wpdb->prefix."namespace d, ".$wpdb->prefix."posts e, ".$wpdb->prefix."posts f WHERE  a.SubjectId = b.ID AND a.PredicateId = c.Id AND c.AttributeId = e.ID AND c.NamespaceId = d.Id AND a.OjectId = f.ID";

if ($subject){

$lhrdf_sql .= " AND b.guid = '".$subject."'";

}

if ($predicate){

$lhrdf_sql .= " and e.guid = '".$predicate."'";

}

if ($object){

$lhrdf_sql .= " and f.guid = '".$object."'";

}

$results = $wpdb->get_results($lhrdf_sql);

return $results;

}



function lh_relationships_return_sparql_triple_by_post_ID($subject, $predicate = NULL, $object = NULL){

global $wpdb;

$lhrdf_sql = "SELECT b.ID AS subjectid, e.ID as predicateid, f.ID AS objectid FROM ".$wpdb->prefix."statement a, ".$wpdb->prefix."posts b, ".$wpdb->prefix."predicate c, ".$wpdb->prefix."namespace d, ".$wpdb->prefix."posts e, ".$wpdb->prefix."posts f WHERE  a.SubjectId = b.ID AND a.PredicateId = c.Id AND c.AttributeId = e.ID AND c.NamespaceId = d.Id AND a.OjectId = f.ID";

if ($subject){

$lhrdf_sql .= " AND b.guid = '".$subject."'";

}

if ($predicate){

$lhrdf_sql .= " and e.guid = '".$predicate."'";

}

if ($object){

$lhrdf_sql .= " and f.guid = '".$object."'";

}

$results = $wpdb->get_results($lhrdf_sql);

return $results;

}


function lh_relationships_return_unique_sparql_object_by_post_ID($subject){

global $wpdb;

$lhrdf_sql = "SELECT DISTINCT f.ID AS objectid FROM ".$wpdb->prefix."statement a, ".$wpdb->prefix."posts b, ".$wpdb->prefix."predicate c, ".$wpdb->prefix."namespace d, ".$wpdb->prefix."posts e, ".$wpdb->prefix."posts f WHERE  a.SubjectId = b.ID AND a.PredicateId = c.Id AND c.AttributeId = e.ID AND c.NamespaceId = d.Id AND a.OjectId = f.ID";


$lhrdf_sql .= " AND b.guid = '".$subject."'";


$results = $wpdb->get_results($lhrdf_sql);

return $results;

}




//Add extra namespaces and data to the normal rdf feed

function lh_relationships_return_namespace(){

global $wpdb;

$sql = "SELECT b.guid as namespace, a.prefix as prefix FROM ".$wpdb->prefix."namespace a, ".$wpdb->prefix."posts b WHERE a.PostsId = b.Id and a.wp_ns = 'no'";

//echo $sql;

$results = $wpdb->get_results($sql);

return $results;

}

//Add extra namespaces and data to the lh-relationship compliant rdf feeds


function lh_relationships_return_compliant_namespace(){

global $wpdb;

$sql = "SELECT b.guid as namespace, a.prefix as prefix FROM ".$wpdb->prefix."namespace a, ".$wpdb->prefix."posts b WHERE a.PostsId = b.Id";

$results = $wpdb->get_results($sql);


return $results;

}



function lh_relationships_create_rdf_statement($subject,$prefix,$fragment,$object){

if ($subject && $prefix && $fragment && $object){

global $wpdb;

$sql = "SELECT a.Id AS predicateId, b.prefix, a.fragment FROM ".$wpdb->prefix."predicate a, ".$wpdb->prefix."namespace b, ".$wpdb->prefix."posts c WHERE a.NamespaceId = b.Id AND a.AttributeId = c.ID and b.prefix = '".$prefix."' and a.fragment = '".$fragment."'";

$results = $wpdb->get_results($sql);

$sql = "SELECT Id from ".$wpdb->prefix."statement where SubjectId = '".$subject."' and PredicateId = '".$results[0]->predicateId."' and OjectId = '".$object."'";

$foo = $wpdb->get_results($sql);

if (!$foo[0]->Id){

$sql = "INSERT INTO ".$wpdb->prefix."statement ( Id , SubjectId , PredicateId , OjectId ) VALUES ( '', '".$subject."', '".$results[0]->predicateId."', '".$object."')";

$results = $wpdb->get_results($sql);

if ($prefix == "foaf" && $fragment == "primaryTopic"){

lh_relationships_create_rdf_statement($subject,"sioc","topic",$object);

}


}

}

}




function lh_relationships_add_rdf_namespace() {

$lhrdfnamespaces = lh_relationships_return_namespace();

$j = 0;

while ($j < count($lhrdfnamespaces)) {

echo "xmlns:".$lhrdfnamespaces[$j]->prefix."=\"".$lhrdfnamespaces[$j]->namespace."\"
";

$j++;
}

}

function lh_relationships_add_compliant_rdf_namespace() {



$lhrdfnamespaces = lh_relationships_return_compliant_namespace();

$j = 0;

while ($j < count($lhrdfnamespaces)) {

echo "xmlns:".$lhrdfnamespaces[$j]->prefix."=\"".$lhrdfnamespaces[$j]->namespace."\"
";

$j++;
}

}

add_action( 'rdf_ns', 'lh_relationships_add_rdf_namespace', 1);



function lh_relationships_add_rdf_namespace_to_html($attr) {

$lhrdfnamespaces = lh_relationships_return_namespace();

$j = 0;

while ($j < count($lhrdfnamespaces)) {

$attr .= "\n xmlns:".$lhrdfnamespaces[$j]->prefix."=\"".$lhrdfnamespaces[$j]->namespace."\"";

$j++;

}

return $attr;


}


add_filter('language_attributes', 'lh_relationships_add_rdf_namespace_to_html');



include_once('enhance_feed.php');


include_once('user_relationships.php');





?>