<?php
/**
 * Template part for displaying posts
 * @package seoboost
 * @version 1.2.1
 */

?>

<?php 

if(get_theme_mod('home_style')=='Grid') : 

 $column = 'col-lg-6 masonry';
 
 else :
 
 $column = 'col-lg-6 masonry';
 
 endif;
 ?>
 
 
    <article id="post-<?php the_ID(); ?>" <?php post_class( $column ); ?>>
        <div class="post-wrapper">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="post-thumbnail">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                </div><!-- .post-thumbnail -->
            <?php endif; ?>
    		<div class="post-content-wrapper">
            
				
				    <?php
$the_cat = get_the_category();
$category_name = $the_cat[0]->cat_name;
$category_description = $the_cat[0]->category_description;
$category_link = get_category_link( $the_cat[0]->cat_ID );
?>
            
		<span class="meta-category"><a href="<?php echo $category_link; ?>"><?php
echo $category_name;?></a></span>
            
   
		
		
		
                <header class="entry-header">
                    
                    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
                
                    <ul class="entry-meta list-inline">
                        
                        <?php if ( 'post' === get_post_type() ): seoboost_posted_on(); endif; ?>
                        
                       
                    </ul>
                
                </header><!-- .entry-header -->
                
                <div class="entry-content">
                        <?php the_excerpt(); ?>
                </div><!-- .entry-content -->
            
        	</div>
        </div>
    </article><!-- #post-## -->
