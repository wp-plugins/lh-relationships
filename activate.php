<?php



function lh_relationships_register_activation_tables() {

global $wpdb;

$query = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."namespace ( `Id` bigint(20) unsigned NOT NULL auto_increment, `PostsId` bigint(20) NOT NULL, `prefix` varchar(7) character set utf8 NOT NULL, `wp_ns` enum('yes','no') NOT NULL default 'no', PRIMARY KEY  (`Id`), UNIQUE KEY `PostsId` (`PostsId`), UNIQUE KEY `prefix` (`prefix`)) DEFAULT CHARSET=utf8";

$results = $wpdb->get_results($query);

$query = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."predicate (`Id` bigint(20) unsigned NOT NULL auto_increment, `NamespaceId` bigint(20) NOT NULL, `fragment` varchar(64) character set utf8 NOT NULL, `AttributeId` bigint(20) NOT NULL, PRIMARY KEY  (`Id`), UNIQUE KEY AttributeId (`AttributeId`)) DEFAULT CHARSET=utf8";

$results = $wpdb->get_results($query);

$query = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."statement` ( `Id` bigint(20) unsigned NOT NULL auto_increment, `SubjectId` bigint(20) unsigned NOT NULL, `PredicateId` bigint(20) unsigned NOT NULL, `OjectId` bigint(20) unsigned NOT NULL, PRIMARY KEY  (`Id`), UNIQUE KEY `SubjectId` (`SubjectId`,`PredicateId`,`OjectId`)) DEFAULT CHARSET=utf8";


$results = $wpdb->get_results($query);

}




function lh_relationships_register_activation_ontologies() {


$foo = lh_relationships_create_namespace_post("RDF NS", "http://www.w3.org/1999/02/22-rdf-syntax-ns#", "rdf", "yes");

$bar = lh_relationships_create_attribute_uri_post($foo, "type");

$foo = lh_relationships_create_namespace_post("Friend of a friend", "http://xmlns.com/foaf/0.1/", "foaf", "no"); 

$bar = lh_relationships_create_attribute_uri_post($foo, "primaryTopic");

$foo = lh_relationships_create_namespace_post("SIOC ns", "http://rdfs.org/sioc/ns#", "sioc", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "related_to");

$bar = lh_relationships_create_attribute_uri_post($foo, "topic");

$foo = lh_relationships_create_namespace_post("RDF types ns", "http://www.w3.org/2000/01/rdf-schema#", "rdfs", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "seeAlso");

$foo = lh_relationships_create_namespace_post("SKOS schema", "http://www.w3.org/2004/02/skos/core#", "skos", "no");

$foo = lh_relationships_create_namespace_post("Meaning of tag", "http://moat-project.org/ns#", "moat", "no");

$foo = lh_relationships_create_namespace_post("LocalHero namespace", "http://localhero.biz/uri/localhero-namespace/", "lh", "no");

$foo = lh_relationships_create_namespace_post("LH Formats", "http://localhero.biz/namespace/lhformats/", "lhfts", "yes");

$foo = lh_relationships_create_namespace_post("Admin namespace", "http://webns.net/mvcb/", "admin", "yes");

$foo = lh_relationships_create_namespace_post("Content module", "http://purl.org/rss/1.0/modules/content/", "content", "yes");

$foo = lh_relationships_create_namespace_post("Dublin Core module", "http://purl.org/dc/elements/1.1/", "dc", "yes");

$foo = lh_relationships_create_namespace_post("Dublin Core terms module", "http://purl.org/dc/terms/", "dcterms", "no");

$foo = lh_relationships_create_namespace_post("SIOC terms module", "http://rdfs.org/sioc/types#", "sioct", "no");

$foo = lh_relationships_create_namespace_post("tag module", "http://www.holygoat.co.uk/owl/redwood/0.1/tags/", "tag", "no");

//Geo Ontologies and attributes

$foo = lh_relationships_create_namespace_post("Georss module", "http://www.georss.org/georss", "georss", "no");

$foo = lh_relationships_create_namespace_post("World Geodetic System module", "http://www.w3.org/2003/01/geo/wgs84_pos#", "wgs84", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "lat");

$bar = lh_relationships_create_attribute_uri_post($foo, "long");

$foo = lh_relationships_create_namespace_post("XFN module", "http://vocab.sindice.com/xfn#", "xfn", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "me");

$foo = lh_relationships_create_namespace_post("Owl module", "http://www.w3.org/2002/07/owl#", "owl", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "sameAs");

$foo = lh_relationships_create_namespace_post("Open Archives module", "http://www.openarchives.org/ore/terms/", "ore", "no");

$foo = lh_relationships_create_namespace_post("Event Module", "http://purl.org/NET/c4dm/event.owl#", "event", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "Event");

$foo = lh_relationships_create_namespace_post("Vocabulary of Interlinked Datasets", "http://rdfs.org/ns/void#", "void", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "Dataset");

$foo = lh_relationships_create_namespace_post("Semantic Web Crawling: Sitemap Extension", "http://sw.deri.org/2007/07/sitemapextension/scschema.xsd", "sc", "no");

$foo = lh_relationships_create_namespace_post("RDF calender", "http://www.w3.org/2002/12/cal#", "ical", "no");

$bar = lh_relationships_create_attribute_uri_post($foo, "Vevent");



}







?>