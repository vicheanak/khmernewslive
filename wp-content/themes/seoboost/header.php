<?php
/**
 * The header for our theme
 *
 * @package seoboost
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<?php 
		// For top header
		$header_status = seoboost_get_option( 'show_top_header' );
		
		
	?>
    
	<div class="mobile-menu-wrap">
		<div class="mobile-menu"></div>
	</div>
	<header id="masthead" class="site-header" role="banner">
		<div class="header-wrapper">
			
			<div class="mobile-header">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="menu-bar-wrap text-center">
								<div class="mobile-nav">
									<span class="menu-icon"><i class="fa fa-bars" aria-hidden="true"></i></span>
								</div>
								<div class="mobile-logo">
									<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
								</div>
								<div class="mobile-search">
									<div class="search-icon">
										<a href="#" id="mobile-search"><i class="fa fa-search" aria-hidden="true"></i></a>
									</div>
							
									 <div id="mobile-search-popup" class="search-off full-search-wrapper">
										<div id="search" class="open">
											<button data-widget="remove" id="close-icon" class="close" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
											<?php echo get_search_form(); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if( $header_status == '1' ): ?>
                <div class="header-top">
                    <div class="container">
                        <div class="row">
                            <?php do_action( 'seoboost_top_header' ); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
			<div class="header-logo">
				<div class="container">
					<div class="row">
						<div class="col-lg-4 col-sm-12">
							<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
						</div>
						<?php
	            			if( is_active_sidebar( 'sidebar-3' ) ) :
	            		?>
						<div class="col-lg-8 col-sm-12">
							<div class="header-seoboost-ads">
								<?php
	                    			dynamic_sidebar( 'sidebar-3' );
	                    		?>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			
            <div class="header-menu">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            
                            <?php if ( has_nav_menu( 'primary' ) ) : ?>
                                <div class="navigation-section">
                                	
                                    <nav id="site-navigation" class="main-navigation" role="navigation">
                                        <?php wp_nav_menu( array(
                                            'theme_location' => 'primary',
                                            'menu_id'        => 'primary-menu',
                                            'menu_class' 	 => 'main-menu',
                                        ) ); 
                                        ?>
                                    </nav>
                                </div><!-- .navigation-section -->
                            <?php endif; ?>
							
							<div class="mini-search-icon">
								<a href="#" id="search-icon"><i class="fa fa-search" aria-hidden="true"></i></a>
							</div>
							
							 <div id="search-popup" class="search-off full-search-wrapper">
            					<div id="search" class="open">
            						<button data-widget="remove" id="removeClass" class="close" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
									<?php echo get_search_form(); ?>
								</div>
							</div>
							
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</header><!-- #masthead -->
    
	<?php if( !is_front_page()):  ?>
        <section class="page-header jumbotron">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <?php if(is_page() || is_single() ){ ?>
                                <h2 class="page-title"><?php echo esc_html( get_the_title() ); ?></h2>
    
                            <?php } elseif( is_search() ){ ?>
        
                            <h2 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'seoboost' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
        
                            <?php }elseif( is_404() ){ ?>
        
                            <h2 class="page-title"><?php echo esc_html( 'Page Not Found: 404', 'seoboost'); ?></h2>
        
                            <?php }elseif( is_home() ){ ?>
        
                            <h2 class="page-title"><?php single_post_title(); ?></h2>
        
                            <?php } else{
        
                                the_archive_title( '<h2 class="page-title">', '</h2>' );
                            }
                        ?>
                       
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
	
	