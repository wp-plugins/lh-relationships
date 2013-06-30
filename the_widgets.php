<?php





//XFN Me widget/////

function lh_relationships_print_author_link() {

global $post;

$subject = get_author_posts_url($post->post_author);

$triple = lh_relationships_return_sparql_triple_by_post_ID($subject, "http://vocab.sindice.com/xfn#me");

$i = 0;

while ($i < count($triple)) {

$postsarray[$i] = $triple[$i]->objectid;

++$i;

}

$profileposts = new WP_Query( array( 'post__in' => $postsarray, 'post_type' => array( 'post', 'page', 'uri' )) );

 while ($profileposts->have_posts()) : $profileposts->the_post(); ?>
    <li><a href="<?php the_GUID() ?>" rel="me"><?php the_title(); ?></a></li>
<?php endwhile; 

}


function lh_relationships_author_widget($args) {
  extract($args);
  echo $before_widget;
  lh_relationships_print_author_link();
  echo $after_widget;
}
 


function lh_relationships_author_widget_init(){

register_sidebar_widget(__('LH Relationships Author Widget'), 'lh_relationships_author_widget');

}

add_action("plugins_loaded", "lh_relationships_author_widget_init");



/////////Related Posts Widget///////////////////



function lh_relationships_print_related_post_widget($atts){

global $post;

$triple = lh_relationships_return_sparql_triple_by_post_ID($post->guid, "http://rdfs.org/sioc/ns#related_to");

if ($triple[0]){

$i = 0;

while ($i < count($triple)) {

$postsarray[$i] = $triple[$i]->objectid;

++$i;

}

$relatedposts = new WP_Query( array( 'post__in' => $postsarray, 'post_type' => array( 'post', 'page', 'uri' )) );

ob_start();

?>

<h3>Related Posts</h3>


<?php while ($relatedposts->have_posts()) : $relatedposts->the_post(); ?>
    <li>
<?php if ( has_post_thumbnail() ) {    ?>

<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" ><?php the_post_thumbnail('thumbnail'); ?></a>

<?php } ?>
<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
<?php the_excerpt(); ?>
</li>
<?php endwhile;

}

$foo = ob_get_contents();

ob_end_clean();

return $foo;

}


?>