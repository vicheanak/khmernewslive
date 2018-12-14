<?php
/**
 *
 * seoboost functions and definitions
 * @package seoboost
 *
 */

/**
 * seoboost only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/include/back-compat.php';
	return;
}
//set font
$seoboost_theme_path = get_template_directory();

require( $seoboost_theme_path .'/include/fonts.php');

require( $seoboost_theme_path .'/include/tgm-plugin-activation.php');

// Widgets.



require( get_template_directory() . '/widgets/post_widget.php' );



// Notice after Theme Activation
function seoboost_activation_notice() {
    echo '<div class="notice notice-success is-dismissible">';
    echo '<p>'. esc_html__( 'Thank you for choosing Seoboost! Now, we highly recommend you to visit our welcome page.', 'seoboost' ) .'</p>';
    echo '<p><a href="'. esc_url( admin_url( 'themes.php?page=about-seoboost' ) ) .'" class="button button-primary">'. esc_html__( 'Get Started with seoboost', 'seoboost' ) .'</a></p>';
    echo '</div>';
}


require( get_template_directory() . '/include/about/about-seoboost.php' );



 //Sets up theme defaults and registers support for various WordPress features.
function seoboost_setup() {
	
	//Make theme available for translation.
	load_theme_textdomain( 'seoboost' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	//Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	//Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );
	
	add_image_size( 'seoboost-featured-image', 1450, 480, true );
	add_image_size( 'seoboost-thumbnail-1', 720, 480, true );
	add_image_size( 'seoboost-thumbnail-2', 600, 200, true );
	add_image_size( 'random-thumb', 520, 400, true );
	add_image_size( 'seoboost-thumbnail-3', 320, 240, true );
	add_image_size( 'seoboost-thumbnail-4', 360, 240, true );
	add_image_size( 'seoboost-thumbnail-5', 100, 75, true );
	add_image_size( 'seoboost-thumbnail-avatar', 100, 100, true );
	

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary'    => __( 'Primary Menu', 'seoboost' ),
		'social' => __( 'Social Links Menu', 'seoboost' ),
	) );

	//Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'audio',
	) );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
 	 */
	add_editor_style( array( 'assets/css/editor.css', seoboost_fonts_url() ) );

	

	
	
	$args = array(
					'flex-width'    => true,
					'width'         => 1450,
					'flex-height'    => true,
					'height'        => 480,
					'default-text-color' => '',
					'default-image' => get_template_directory_uri() . '/assets/images/header.jpg',
					'wp-head-callback' => 'seoboost_header_style',
	);
	register_default_headers( array(
		'default-image' => array(
			'url'           => '%s/assets/images/header.jpg',
			'thumbnail_url' => '%s/assets/images/header.jpg',
			'description'   => __( 'Default Header Image', 'seoboost' ),
		),
	) );
	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'seoboost_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 * @global int $content_width
 */
function seoboost_content_width() {

	$content_width = $GLOBALS['content_width'];

	// Get layout.
	$page_layout = get_theme_mod( 'page_layout' );

	// Check if layout is one column.
	if ( 'one-column' === $page_layout ) {
		if ( seoboost_is_frontpage() ) {
			$content_width = 644;
		} elseif ( is_page() ) {
			$content_width = 740;
		}
	}

	// Check if is single post and there is no sidebar.
	if ( is_single() && ! is_active_sidebar( 'sidebar-1' ) ) {
		$content_width = 740;
	}

	/**
	 * Filter seoboost content width of the theme.
	 * @param int $content_width Content width in pixels.
	 */
	$GLOBALS['content_width'] = apply_filters( 'seoboost_content_width', $content_width );
}
add_action( 'template_redirect', 'seoboost_content_width', 0 );


/**
 * Add preconnect for Google Fonts.
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function seoboost_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'seoboost-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'seoboost_resource_hints', 10, 2 );

/**
 * Register widget areas.
 */
function seoboost_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'seoboost' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'seoboost' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	

	
	register_sidebar( array(
		'name'          => __( 'Header Widget - Advertisement Area', 'seoboost' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widget(Image) here to appear in your header. Recommand Size: 720x90 ', 'seoboost' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer 1', 'seoboost' ),
		'id'            => 'footer-1',
		'description'   => __( 'Add widgets here to appear in your footer.', 'seoboost' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'seoboost' ),
		'id'            => 'footer-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'seoboost' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer 3', 'seoboost' ),
		'id'            => 'footer-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'seoboost' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer 4', 'seoboost' ),
		'id'            => 'footer-4',
		'description'   => __( 'Add widgets here to appear in your footer.', 'seoboost' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'seoboost_widgets_init' );




/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 * @param string $link Link to single post/page.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function seoboost_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<div class="read-more"><a href="%1$s" class="link">%2$s</a></div>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Read More <i class="fa fa-angle-right"></i><span class="screen-reader-text"> "%s"</span>', 'seoboost' ), get_the_title( get_the_ID() ) )
	);
	return $link;
}
add_filter( 'excerpt_more', 'seoboost_excerpt_more' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 */
function seoboost_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'seoboost_javascript_detection', 0 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function seoboost_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url(get_bloginfo( 'pingback_url' )) );
	}
}
add_action( 'wp_head', 'seoboost_pingback_header' );

/**
 * Enqueue scripts and styles.
 */

function seoboost_scripts() {
	
		if ( is_rtl() ){
        wp_enqueue_style( 'bootstrap-rtl', get_template_directory_uri() . '/assets/css/bootstrap-rtl.css');
    }

	//Bootstrap stylesheet.
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css' );
	

	// Theme stylesheet.
	wp_enqueue_style( 'seoboost', get_stylesheet_uri() );
	

	//Fontawesome web stylesheet.
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/assets/css/font-awesome.css' );
	
	
	//Animate
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/assets/css/animate.css' );

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'seoboost-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'seoboost-style' ), '1.0' );
		wp_style_add_data( 'seoboost-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'seoboost-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'seoboost-style' ), '1.0' );
	wp_style_add_data( 'seoboost-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'seoboost-skip-link-focus-fix', get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ), array(), '1.0', true );

	$seoboost_l10n = array(
		'quote'          => seoboost_get_svg( array( 'icon' => 'quote-right' ) ),
	);

	if ( has_nav_menu( 'top' ) ) {
		wp_enqueue_script( 'seoboost-navigation', get_theme_file_uri( '/assets/js/navigation.js' ), array( 'jquery' ), '1.0', true );
		$seoboost_l10n['expand']         = __( 'Expand child menu', 'seoboost' );
		$seoboost_l10n['collapse']       = __( 'Collapse child menu', 'seoboost' );
		$seoboost_l10n['icon']           = seoboost_get_svg( array( 'icon' => 'angle-down', 'fallback' => true ) );
	}

	wp_enqueue_script( 'seoboost-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '1.0', true );

	wp_enqueue_script( 'jquery-scrollto', get_theme_file_uri( '/assets/js/jquery.scrollTo.js' ), array( 'jquery' ), '2.1.2', true );
	
	
	wp_enqueue_script( 'bootstrap', get_theme_file_uri( '/assets/js/bootstrap.js' ), array( 'jquery' ), '1.0', true );
	
	wp_enqueue_script( 'masonry' );
	
	wp_enqueue_script( 'jquery-easing', get_theme_file_uri( '/assets/js/jquery.easing.js' ), array( 'jquery' ), '1.0', true );
        
    wp_enqueue_script( 'jquery-easy-ticker', get_theme_file_uri( '/assets/js/jquery.easy-ticker.js' ), array( 'jquery' ), '1.0', true );
	
	wp_enqueue_script( 'seoboost-theme', get_theme_file_uri( '/assets/js/theme.js' ), array( 'jquery' ), '1.0', true );

	wp_localize_script( 'seoboost-skip-link-focus-fix', 'seoboostScreenReaderText', $seoboost_l10n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'seoboost_scripts' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images.
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *	values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function seoboost_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	if ( 740 <= $width ) {
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if ( is_active_sidebar( 'sidebar-1' ) || is_archive() || is_search() || is_home() || is_page() ) {
		if ( ! ( is_page() && 'one-column' === get_theme_mod( 'page_options' ) ) && 767 <= $width ) {
			 $sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'seoboost_content_image_sizes_attr', 10, 2 );

/**
 * Filter the `sizes` value in the header image markup.
 * @param string $html   The HTML image tag markup being filtered.
 * @param object $header The custom header object returned by 'get_custom_header()'.
 * @param array  $attr   Array of the attributes for the image tag.
 * @return string The filtered header image HTML.
 */
function seoboost_header_image_tag( $html, $header, $attr ) {
	if ( isset( $attr['sizes'] ) ) {
		$html = str_replace( $attr['sizes'], '100vw', $html );
	}
	return $html;
}
add_filter( 'get_header_image_tag', 'seoboost_header_image_tag', 10, 3 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality for post thumbnails.
 * @param array $attr       Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size       Registered image size or flat array of height and width dimensions.
 * @return array The filtered attributes for the image markup.
 */
function seoboost_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( is_archive() || is_search() || is_home() ) {
		$attr['sizes'] = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
	} else {
		$attr['sizes'] = '100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'seoboost_post_thumbnail_sizes_attr', 10, 3 );

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function seoboost_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template',  'seoboost_front_page_template' );

/**
 * Modifies tag cloud widget arguments to display all tags in the same font size and use list format for better accessibility.
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array The filtered arguments for tag cloud widget.
 */
function seoboost_widget_tag_cloud_args( $args ) {
	$args['largest']  = 12;
	$args['smallest'] = 12;
	$args['unit']     = 'px';
	$args['format']   = 'list';

	return $args;
}
add_filter( 'widget_tag_cloud_args', 'seoboost_widget_tag_cloud_args' );

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path( '/include/template-tags.php' );

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path( '/include/template-functions.php' );

/**
 * Customizer additions.
 */
require get_parent_theme_file_path( '/include/customizer.php' );



/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path( '/include/icon-functions.php' );

/**
 * breadcrumb.
 */
require get_parent_theme_file_path( '/template-parts/header/breadcrumb.php' );


/**
 * hooks function.
 */
require get_parent_theme_file_path( '/include/hooks.php' );



/**
 * Filter the except length to 30 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function seoboost_custom_excerpt_length( $length ) {
	if( is_admin() ) return $length;
    return 20 ;
}
add_filter( 'excerpt_length', 'seoboost_custom_excerpt_length');


function seoboost_social_title( $title ) {
    $title = html_entity_decode( $title );
    $title = urlencode( $title );
    return $title;
}



function seoboost_seoboost_category_display() {
    
// SHOW YOAST PRIMARY CATEGORY, OR FIRST CATEGORY
$category = get_the_category();
$useCatLink = true;
// If post has a category assigned.
if ($category){
	$seoboost_seoboost_category_display = '';
	$category_link = '';
	if ( class_exists('WPSEO_Primary_Term') )
	{
		// Show the post's 'Primary' category, if this Yoast feature is available, & one is set
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term = get_term( $wpseo_primary_term );
		if (is_wp_error($term)) { 
			// Default to first category (not Yoast) if an error is returned
			$seoboost_seoboost_category_display = $category[0]->name;
			$category_link = get_category_link( $category[0]->term_id );
		} else { 
			// Yoast Primary category
			$seoboost_seoboost_category_display = $term->name;
			$category_link = get_category_link( $term->term_id );
		}
	} 
	else {
		// Default, display the first category in WP's list of assigned categories
		$seoboost_seoboost_category_display = $category[0]->name;
		$category_link = get_category_link( $category[0]->term_id );
	}

	// Display category
	if ( !empty($seoboost_seoboost_category_display) ){
	    if ( $useCatLink == true && !empty($category_link) ){
		echo '<span class="post-category">';
		echo '<a href="'. esc_url($category_link) . '">'. esc_html($seoboost_seoboost_category_display).'</a>';
		echo '</span>';
	    } else {
		echo '<span class="post-category">'. esc_html($seoboost_seoboost_category_display).'</span>';
	    }
	}
	
}
}
add_action( 'display_category', 'seoboost_seoboost_category_display' );

