<?php
/**
 * Template part for displaying page content in page.php
 * @package seoboost
 * @version 1.2.1
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


	<div class="page-inner-wrap">
	
    	<?php if ( has_post_thumbnail() ) : ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div>
		<?php endif; ?>
        <div class="entry-content">
            <?php
                the_content();
    
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . __( 'Pages:', 'seoboost' ),
                    'after'  => '</div>',
                ) );
            ?>
        </div><!-- .entry-content -->
        <div class="entry-footer">
            <?php seoboost_edit_link( get_the_ID() ); ?>
        </div>
	</div>
</article><!-- #post-## -->
