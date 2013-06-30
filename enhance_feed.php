<?php

function lh_relationships_add_rdf_nodes($subject){

global $post;

if (!$subject){

$subject = $post->guid;

}

$foo = lh_relationships_return_rdf($subject);




$j = 0;

while ($j < count($foo)) {

echo "<".$foo[$j]->prefix.":".$foo[$j]->fragment." rdf:resource=\"".htmlspecialchars($foo[$j]->object)."\" />\n";


$j++;
}




}


add_action( 'rdf_item', 'lh_relationships_add_rdf_nodes', 1);



function lh_relationships_add_rdf_vals() {


$lhrdfnamespaces = lh_relationships_return_compliant_namespace();

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

$results = $wpdb->get_results($sql);

$j = 0;

while ($j < count($results)) {

if(!lh_relationships_isvaliduRL($results[$j]->meta_value)){

echo "<".$results[$j]->meta_key.">".$results[$j]->meta_value."</".$results[$j]->meta_key.">";

}

$j++;

}

}

add_action( 'rdf_item', 'lh_relationships_add_rdf_vals', 1);



function lh_relationships_add_see_also() {

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


add_filter('rdf_header', 'lh_relationships_add_see_also');



function lh_relationships_the_taxonomy_to_rdf() {

global $post;
	
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

//add_action( 'rdf_item', 'lh_relationships_the_taxonomy_to_rdf', 1);

?>