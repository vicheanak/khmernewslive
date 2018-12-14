<?php

if ( ! function_exists( 'seoboost_top_header_action' ) ) :
	
	function seoboost_top_header_action() {
		
		$seoboost_header_top_left = seoboost_get_option( 'header_top_left_section' );
		$seoboost_header_top_right = seoboost_get_option( 'header_top_right_section' );
		
		if( $seoboost_header_top_left == 'tranding-news' ){
			
			do_action( 'seoboost_top_header_trending_news' );
		
		}else{
			
			do_action( 'seoboost_top_header_social_icon' );
		
		}
		
		if( $seoboost_header_top_right == 'social-icons' ){
			
			do_action( 'seoboost_top_header_social_icon' );
		
		}else{
			
			do_action( 'seoboost_top_header_trending_news' );
		
		}
	}
	
endif;
add_action('seoboost_top_header', 'seoboost_top_header_action');








//=============================================================
// Trending news hook of the theme
//=============================================================
if ( ! function_exists( 'seoboost_top_header_trending_news_action' ) ) :
     
     
    function seoboost_top_header_trending_news_action() { ?>
		<div class="col-md-6 col-sm-12">
            <div class="news-ticker-wrap">
                <?php 
    
                $trending_title         = seoboost_get_option( 'header_top_trending_title' );
                $trending_category      = seoboost_get_option( 'header_top_dropdown_category' );
                $trending_post_number   = seoboost_get_option( 'seoboost_header_trending_post_number' );
    
                if( !empty( $trending_title ) ): ?>
    
                    <span class="news-ticker-title theme-bg text-white"><?php echo esc_html( $trending_title ); ?></span>
                    
                <?php endif;
    
                $query_args = array(
                                'posts_per_page'        => absint( $trending_post_number ),
                                'no_found_rows'         => true,
                                'post__not_in'          => get_option( 'sticky_posts' ),
                                'ignore_sticky_posts'   => true,
                            );
    
                if ( absint( $trending_category ) > 0 ) {
    
                    $query_args['cat'] = absint( $trending_category );
                    
                } 
    
                $all_posts = new WP_Query( $query_args );
    
                if ( $all_posts->have_posts() ) : ?>
                	<div class="news-ticker-list">
                        <div class="news-ticker">  
                            <ul class="news-ticker-items">
                                <?php
                                while ( $all_posts->have_posts() ) :
                                    
                                    $all_posts->the_post(); ?>
                                    
                                    <li class="news-item">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </li>
            
                                    <?php
            
                                endwhile; 
            
                                wp_reset_postdata(); ?>
                                  
                            </ul>
                       </div>
                   </div>
				<?php endif; ?>
              </div>
        </div>
		<?php
    }
endif;

add_action( 'seoboost_top_header_trending_news', 'seoboost_top_header_trending_news_action' );


//=============================================================
// Social Icon hook of the theme
//=============================================================
if ( ! function_exists( 'seoboost_top_header_social_icon_action' ) ) :
	
	function seoboost_top_header_social_icon_action() {
		
		$facebook_link  = seoboost_get_option( 'facebook_link' );
		$twitter_link  = seoboost_get_option( 'twitter_link' );
		$instagram_link  = seoboost_get_option( 'instagram_link' );
		$google_link  = seoboost_get_option( 'google_link' );
		$pinterest_link  = seoboost_get_option( 'pinterest_link' );
		$bloglovin_link  = seoboost_get_option( 'bloglovin_link' );
		$youtube_link  = seoboost_get_option( 'youtube_link' );
		?>
		<div class="col-md-6 col-sm-12">
        	<ul class="social-info list-inline">
			<?php if( !empty( $facebook_link ) ){ ?>
                    <li class="facebook list-inline-item">
                        <a href="<?php echo esc_url( $facebook_link ); ?>">
                            <i class="fa fa-facebook aria-hidden="true""></i>
                        </a>
                    </li>
			<?php } ?>
    
			<?php if( !empty( $twitter_link ) ){ ?>
                <li class="twitter list-inline-item">
                    <a href="<?php echo esc_url( $twitter_link ); ?>">
                        <i class="fa fa-twitter aria-hidden="true""></i>
                    </a>
                </li>
            <?php } ?>
    
			<?php if( !empty( $instagram_link ) ){ ?>
                <li class="instagram list-inline-item">
                    <a href="<?php echo esc_url( $instagram_link ); ?>">
                        <i class="fa fa-instagram aria-hidden="true""></i>
                    </a>
                </li>
            <?php } ?>
            
			<?php if( !empty( $google_link ) ){ ?>
                <li class="google list-inline-item">
                    <a href="<?php echo esc_url( $google_link ); ?>">
                        <i class="fa fa-google-plus" aria-hidden="true"></i> 
                    </a>
                </li>
            <?php } ?>
			
			<?php if( !empty( $pinterest_link ) ){ ?>
                <li class="pinterest list-inline-item">
                    <a href="<?php echo esc_url( $pinterest_link ); ?>">
                        <i class="fa fa-pinterest" aria-hidden="true"></i> 
                    </a>
                </li>
            <?php } ?>
			
			
					<?php if( !empty( $bloglovin_link ) ){ ?>
                <li class="pinterest list-inline-item">
                    <a href="<?php echo esc_url( $bloglovin_link ); ?>">
                        <i class="fa fa-heart" aria-hidden="true"></i> 
                    </a>
                </li>
            <?php } ?>
			
			
				<?php if( !empty( $youtube_link ) ){ ?>
                <li class="pinterest list-inline-item">
                    <a href="<?php echo esc_url( $youtube_link ); ?>">
                        <i class="fa fa-youtube" aria-hidden="true"></i> 
                    </a>
                </li>
            <?php } ?>
                            
		</ul>	
	</div>
	<?php }

endif;

add_action('seoboost_top_header_social_icon', 'seoboost_top_header_social_icon_action');

if ( ! function_exists( 'seoboost_featured_post_action' ) ) :
		
	function seoboost_featured_post_action(){
		
		
		$args = array(
				'post_type' 			=> 'post',
				'posts_per_page'        => 4,
				'ignore_sticky_posts'   => true,
				'post_status'		   	=> 'publish',
				'meta_query' 			=> array( 
					array(
						'key' => 'seoboost_feature_post',
						'value' => '1'
					)
				)
		);
		$post_img = get_template_directory_uri() . '/assets/images/post-thumbnail.png';
		$post_query = new WP_Query( $args );
		
		if ( $post_query->have_posts() ) : ?>
			<div class="banner-grid-parent clearfix">
			<!-- the loop -->
			<?php $count= 0; while ( $post_query->have_posts() ) : $post_query->the_post(); ?>
				

					<?php 
						$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'seoboost-thumbnail-1' );
						if($count == 0):  
					?>
						
						<div class="banner-grid-item banner-grid-50x100 banner-grid-itemw2" data-url="<?php echo esc_url( $image[0] ); ?>">
							<div class="banner-post-grid">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="banner-grid-thumb">
										<div class="overlay"></div>
									</div><!-- .post-thumbnail -->
								<?php else: ?>
									<div class="banner-grid-thumb">
										<div class="overlay"></div>
										<img src="<?php echo esc_url( $post_img )?>" class="img-responsive"/>
									</div>
								<?php endif; ?>
								<div class="banner-grid-details">
									<div class="post-meta">
										<?php if( has_category()):
												echo '<div class="banner-post-categories">';
													the_category();
												echo '</div>';
										endif; ?>
										<?php the_title( '<h5 class="banner-post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					
					<?php if( $count > 0 && $count <= 1): ?>
						<div class="banner-grid-item banner-grid-50x50 banner-grid-itemw2" data-url="<?php echo esc_url( $image[0] ); ?>">
							<div class="banner-post-grid">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="banner-grid-thumb">
										<div class="overlay"></div>
									</div><!-- .post-thumbnail -->
								<?php else: ?>
									<div class="banner-grid-thumb">
										<div class="overlay"></div>
										<img src="<?php echo esc_url( $post_img )?>" class="img-responsive" />
									</div>
								<?php endif; ?>
								<div class="banner-grid-details">
									<div class="post-meta">
										<?php if( has_category()):
												echo '<div class="banner-post-categories">';
													the_category();
												echo '</div>';
										endif; ?>
										<?php the_title( '<h5 class="banner-post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					
					<?php if( $count > 1 ): ?>
						<div class="banner-grid-item banner-grid-50x50 banner-grid-itemw4" data-url="<?php echo esc_url( $image[0] ); ?>">
							<div class="banner-post-grid">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="banner-grid-thumb">
										<div class="overlay"></div>
									</div><!-- .post-thumbnail -->
								<?php else: ?>
									<div class="banner-grid-thumb">
										<div class="overlay"></div>
										<img src="<?php echo esc_url( $post_img )?>" class="img-responsive" />
									</div>
								<?php endif; ?>
								<div class="banner-grid-details">
									<div class="post-meta">
										<?php if( has_category()):
												echo '<div class="banner-post-categories">';
													the_category();
												echo '</div>';
										endif; ?>
										<?php the_title( '<h5 class="banner-post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif;   $count++;  ?>

			<?php endwhile;  ?>
			<!-- end of the loop -->

			<?php wp_reset_postdata(); ?>
			</div>
	<?php else : ?>
		<p> <?php esc_html_e( 'Sorry, There is no post set as feature yet.', 'seoboost' ); ?> </p>
	<?php endif;
	} 
endif;	
add_action('seoboost_featured_post', 'seoboost_featured_post_action');
