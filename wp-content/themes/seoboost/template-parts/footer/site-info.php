<?php
/**
 * Displays footer site info
 *
 * @package seoboost
 * @version 1.2.1
 */

?>

<div class="site-info">
	<?php $copyright_text = seoboost_get_option( 'copyright_text' ); 
    
        if ( ! empty( $copyright_text ) ) : ?>
    
            <p><?php echo wp_kses_data( $copyright_text ); ?></p> 
    
    <?php endif; ?>

        <?php printf( esc_html__( ' %1$s by%2$s', 'seoboost' ), 'Designed', '<a href="http://www.dashthemes.com/" rel="designer">DashThemes</a>' ); ?>
		  
		  
</div><!-- .site-info -->
