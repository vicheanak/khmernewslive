<?php
/**
 * The template for displaying search results pages
 * @package seoboost
 * @version 1.2.1
 */

get_header(); ?>
<div id="content" class="site-content">
	<div class="container">
        <div class="row">
            <div class="col-md-8">
                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main">
                        <div class="seoboost-post-list">
                            <?php if ( have_posts() ) :
                                    /* Start the Loop */
                                    while ( have_posts() ) : the_post(); 
                                            
                                            get_template_part( 'template-parts/post/content', 'list');
                            
                                        endwhile; // End of the loop.
                            
                                else : ?>
                                    <div class="wrong-search-wrapper text-center">
                                        <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'seoboost' ); ?></p>
                                        <?php get_search_form(); ?>
                                    </div>
                            <?php endif; ?>
                        </div>
						
						<?php the_posts_pagination(); ?>
                    
                    </main>
                </div>
            </div>
           <div class="col-md-4">
                <?php get_sidebar(); ?>
           </div>
        </div><!-- .row -->
	</div>
</div>
<?php get_footer();
