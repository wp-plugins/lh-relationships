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




?>