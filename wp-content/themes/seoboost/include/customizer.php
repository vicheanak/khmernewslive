<?php
/**
 * seoboost: Customizer
 *
 * @package seoboost
 */

/**<?php
/**
 * seoboost: Customizer
 *
 * @package seoboost
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
 
add_action( 'customize_register', 'seoboost_child_customize_register' );

function seoboost_child_customize_register( WP_Customize_Manager $wp_customize ) {
/**
 * Category Dropdown.
 */
require_once trailingslashit( get_template_directory() ) .  '/include/dropdown-category.php';

}

function seoboost_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport          = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport   = 'postMessage';
	$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
	$wp_customize->get_setting( 'header_image'  )->transport = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
  
  $wp_customize->remove_control("header_image");
  $wp_customize->remove_section("colors");

	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'seoboost_customize_partial_blogname',
	) );
	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'seoboost_customize_partial_blogdescription',
	) );
	
	
	/**
	 * Theme options.
	 */
	 $default = seoboost_default_theme_options();
	 
	 $wp_customize->add_panel( 'theme_option_panel',
		array(
			'title'      => esc_html__( 'Theme Options', 'seoboost' ),
			'priority'   => 30,
			'capability' => 'edit_theme_options',
		)
	);
	
	// Header Section.
	$wp_customize->add_section( 'section_header',
		array(
			'title'      => esc_html__( 'Header Options', 'seoboost' ),
			'priority'   => 100,
			'capability' => 'edit_theme_options',
			'panel'      => 'theme_option_panel',
		)
	);
	

	
	// Setting show_top_header.
	$wp_customize->add_setting( 'show_top_header',
		array(
			'default'           => $default['show_top_header'],
			'sanitize_callback' => 'seoboost_sanitize_checkbox',
		)
	);
	
	$wp_customize->add_control( 'show_top_header',
		array(
			'label'    			=> esc_html__( 'Show Header - Top', 'seoboost' ),
			'section'  			=> 'section_header',
			'type'     			=> 'checkbox',
			'priority' 			=> 100,
		)
	);
	
	// Setting top left header.
	$wp_customize->add_setting( 'header_top_left_section',
		array(
			'default'           => $default['header_top_left_section'],
			'sanitize_callback' => 'seoboost_sanitize_select',
		)
	);
	
	$wp_customize->add_control( 'header_top_left_section',
		array(
			'label'    			=> esc_html__( 'Top Header Left Section', 'seoboost' ),
			'section'  			=> 'section_header',
			'type'     			=> 'select',
			'priority' 			=> 100,
			'choices'  			=> array(
									'tranding-news'  => esc_html__( 'Trending News', 'seoboost' ),
									'social-icons' => esc_html__( 'Social Icons', 'seoboost' ),
								),
			'active_callback' 	=> 'seoboost_is_top_header_active',
		)
	);
	
	// Setting copyright_text.
	$wp_customize->add_setting( 'header_top_trending_title',
		array(
			'default'           => $default['header_top_trending_title'],
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	
	$wp_customize->add_control( 'header_top_trending_title',
		array(
			'label'    => esc_html__( 'Title', 'seoboost' ),
			'section'  => 'section_header',
			'type'     => 'text',
			'priority' => 100,
		)
	);
	
	// Setting top right header.
	$wp_customize->add_setting( 'header_top_right_section',
		array(
			'default'           => $default['header_top_right_section'],
			'sanitize_callback' => 'seoboost_sanitize_select',
		)
	);
	
	$wp_customize->add_control( 'header_top_right_section',
		array(
			'label'    			=> esc_html__( 'Top Header Right Section', 'seoboost' ),
			'section'  			=> 'section_header',
			'type'     			=> 'select',
			'priority' 			=> 100,
			'choices'  			=> array(
									'tranding-news'  => esc_html__( 'Trending News', 'seoboost' ),
									'social-icons' => esc_html__( 'Social Icons', 'seoboost' ),
								),
			'active_callback' 	=> 'seoboost_is_top_header_active',
		)
	);
	
	$wp_customize->add_setting( 'header_top_dropdown_category', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new seoboost_Dropdown_Category_Control( $wp_customize, 'header_top_dropdown_category', array(
		'section'       => 'section_header',
		'label'         => esc_html__( 'Select Category', 'seoboost' ),
		'priority' 	  => 100,
	) ) );
	
	// Setting trending_post_number.
	$wp_customize->add_setting( 'seoboost_header_trending_post_number',
		array(
			'default'           => $default['seoboost_header_trending_post_number'],
			'sanitize_callback' => 'seoboost_sanitize_positive_integer',
		)
	);
	$wp_customize->add_control( 'seoboost_header_trending_post_number',
		array(
			'label'           => esc_html__( 'Number of Posts', 'seoboost' ),
			'section'         => 'section_header',
			'type'            => 'number',
			'priority'        => 100,
			'active_callback' => 'seoboost_is_top_header_active',
			'input_attrs'     => array( 'min' => 1, 'max' => 10, 'style' => 'width: 55px;' ),
		)
	);
	// Setting facebook.
	$wp_customize->add_setting( 'facebook_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'facebook_link',
		array(
			'label'    		=> esc_html__( 'facebook', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	// Setting twitter.
	$wp_customize->add_setting( 'twitter_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'twitter_link',
		array(
			'label'    		=> esc_html__( 'Twitter', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	// Setting instagram.
	$wp_customize->add_setting( 'instagram_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'instagram_link',
		array(
			'label'    		=> esc_html__( 'Instagram', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	// Setting google_plus.
	$wp_customize->add_setting( 'google_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'google_link',
		array(
			'label'    		=> esc_html__( 'Google Plus', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	
		// Setting Pinterest.
	$wp_customize->add_setting( 'pinterest_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'pinterest_link',
		array(
			'label'    		=> esc_html__( 'Pinterest', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	
		// Setting Bloglovin.
	$wp_customize->add_setting( 'bloglovin_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'bloglovin_link',
		array(
			'label'    		=> esc_html__( 'Bloglovin', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	
	
		// Setting Youtube.
	$wp_customize->add_setting( 'youtube_link',
		array(
		
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	
	$wp_customize->add_control( 'youtube_link',
		array(
			'label'    		=> esc_html__( 'Youtube', 'seoboost' ),
			'description'      =>  __( 'e.g: http://example.com', 'seoboost' ),
			'section'  		  => 'section_header',
			'type'     		 => 'url',
			'priority' 		 => 100,
			'active_callback'  => 'seoboost_is_top_header_active',
		)
	);
	
	
	
	// Breadcrumb Section.
	$wp_customize->add_section( 'section_breadcrumb',
		array(
			'title'      => esc_html__( 'Breadcrumb Options', 'seoboost' ),
			'priority'   => 100,
			'capability' => 'edit_theme_options',
			'panel'      => 'theme_option_panel',
		)
	);
	
	// Setting breadcrumb_type.
	$wp_customize->add_setting( 'breadcrumb_type',
		array(
			'default'           => $default['breadcrumb_type'],
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'seoboost_sanitize_select',
		)
	);
	
	$wp_customize->add_control( 'breadcrumb_type',
		array(
			'label'       => esc_html__( 'Breadcrumb Type', 'seoboost' ),
			'section'     => 'section_breadcrumb',
			'type'        => 'radio',
			'priority'    => 100,
			'choices'     => array(
				'disable' => esc_html__( 'Disable', 'seoboost' ),
				'normal'  => esc_html__( 'Normal', 'seoboost' ),
			),
		)
	);
	
	
		$wp_customize->add_section( 'seoboost_new_section_general' , array(
   		'title'      => 'Layout Settings',
   		'description'=> '',
   		'priority'   => 10,
		'capability' => 'edit_theme_options',
			'panel'      => 'theme_option_panel',
	) );
	
	
	
	    $wp_customize->add_setting(
	        'home_style',
	        array(
	            'default'     => 'Grid',
				'sanitize_callback' => 'sanitize_text_field',
			
	        )
	    );
    
	
	    $wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'home_layout',
				array(
					'label'          => 'Home Style Layout',
					'section'        => 'seoboost_new_section_general',
					'settings'       => 'home_style',
					'type'           => 'radio',
					'priority'	 => 3,
					'choices'        => array(
						'Grid'   => 'Grid Style Layout',
						'List'  => 'List Style Layout [Premium Version]',
						'Simple'  => 'Simple Style Layout [Premium Version]',

        
					)
				)
			)
		);
    
	
	$wp_customize->add_setting(
	        'home_sidebar',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		
		
			$wp_customize->add_setting(
	        'post_sidebar',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'archive_sidebar',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		
		$wp_customize->add_setting(
	        'pages_sidebar',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		
	
	$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'sidebar_homepage',
				array(
					'label'      => 'Disable Sidebar on Homepage',
					'section'    => 'seoboost_new_section_general',
					'settings'   => 'home_sidebar',
					'type'		 => 'checkbox',
					'priority'	 => 4
				)
			)
		);
		
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'sidebar_post',
				array(
					'label'      => 'Disable Sidebar on Posts',
					'section'    => 'seoboost_new_section_general',
					'settings'   => 'post_sidebar',
					'type'		 => 'checkbox',
					'priority'	 => 5
				)
			)
		);
		
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'sidebar_archive',
				array(
					'label'      => 'Disable Sidebar on Archives',
					'section'    => 'seoboost_new_section_general',
					'settings'   => 'archive_sidebar',
					'type'		 => 'checkbox',
					'priority'	 => 6
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'pages_archive',
				array(
					'label'      => 'Disable Sidebar on Pages',
					'section'    => 'seoboost_new_section_general',
					'settings'   => 'pages_sidebar',
					'type'		 => 'checkbox',
					'priority'	 => 6
				)
			)
		);
	
	// Footer Section.
	$wp_customize->add_section( 'section_footer',
		array(
			'title'      => esc_html__( 'Footer Options', 'seoboost' ),
			'priority'   => 100,
			'capability' => 'edit_theme_options',
			'panel'      => 'theme_option_panel',
		)
	);
	
	// Setting copyright_text.
	$wp_customize->add_setting( 'copyright_text',
		array(
			'default'           => $default['copyright_text'],
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	
	$wp_customize->add_control( 'copyright_text',
		array(
			'label'    => esc_html__( 'Copyright Text', 'seoboost' ),
			'section'  => 'section_footer',
			'type'     => 'text',
			'priority' => 100,
		)
	);
	
	
	// Back To Top Section.
	$wp_customize->add_section( 'section_back_to_top',
		array(
			'title'      => esc_html__( 'Back To Top Options', 'seoboost' ),
			'priority'   => 100,
			'capability' => 'edit_theme_options',
			'panel'      => 'theme_option_panel',
		)
	);
	
	// Setting breadcrumb_type.
	$wp_customize->add_setting( 'back_to_top_type',
		array(
			'default'           => $default['back_to_top'],
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'seoboost_sanitize_select',
		)
	);
	
	$wp_customize->add_control( 'back_to_top_type',
		array(
			'label'       => esc_html__( 'Active?', 'seoboost' ),
			'section'     => 'section_back_to_top',
			'type'        => 'radio',
			'priority'    => 100,
			'choices'     => array(
				'disable' => esc_html__( 'Disable', 'seoboost' ),
				'enable'  => esc_html__( 'Enable', 'seoboost' ),
			),
		)
	);
	
		//Color Section.
		

	

	
	
	
	
	
	
	
	
	
		// Post Settings
	
	$wp_customize->add_section( 'seoboost_new_section_post' , array(
   		'title'      => 'Post Settings',
   		'description'=> '',
   		'priority'   => 96,
		'capability' => 'edit_theme_options',
			'panel'      => 'theme_option_panel',
	) );
    
		// Post Settings
		$wp_customize->add_setting(
	        'article_tags',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'article_author',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'article_related_post',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'article_social_share',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'article_next_post',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'article_comment_link',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
    
    $wp_customize->add_setting(
	        'article_like_link',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );

    
		$wp_customize->add_setting(
	        'article_date_area',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
		$wp_customize->add_setting(
	        'post_categories',
	        array(
	            'default'     => false,
				'sanitize_callback' => 'seoboost_sanitize_checkbox',
	        )
	    );
    

    

    
    
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_cat',
				array(
					'label'      => 'Hide Category',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'post_categories',
					'type'		 => 'checkbox',
					'priority'	 => 3
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_date',
				array(
					'label'      => 'Hide Date',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_date_area',
					'type'		 => 'checkbox',
					'priority'	 => 2
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_tags',
				array(
					'label'      => 'Hide Tags',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_tags',
					'type'		 => 'checkbox',
					'priority'	 => 5
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_share',
				array(
					'label'      => 'Share Buttons [Only In Premium]',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_social_share',
					'type'		 => 'checkbox',
					'priority'	 => 6
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_share_author',
				array(
					'label'      => 'Hide Post Navi',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_next_post',
					'type'		 => 'checkbox',
					'priority'	 => 8
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_comment_link',
				array(
					'label'      => 'Hide Comment Link',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_comment_link',
					'type'		 => 'checkbox',
					'priority'	 => 4
				)
			)
		);
    

    
    
    

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_author',
				array(
					'label'      => 'Hide Author Name',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_author',
					'type'		 => 'checkbox',
					'priority'	 => 1
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'post_related',
				array(
					'label'      => 'Hide Related Posts Box',
					'section'    => 'seoboost_new_section_post',
					'settings'   => 'article_related_post',
					'type'		 => 'checkbox',
					'priority'	 => 9
				)
			)
		);


		
}





if ( class_exists( 'Kirki' ) ) {
/**
 * Add the theme configuration
 */
Kirki::add_config( 'seoboost_theme', array(
	'option_type' => 'theme_mod',
	'capability'  => 'edit_theme_options',
) );

	Kirki::add_panel( 'typo_settings', array(
	'priority'                  => 330,
	'title'                     => esc_html__( 'Typography Settings', 'seoboost' ),
	'description'               => esc_html__( 'This panel contains the Font Controls', 'seoboost' ),
) );
/**
 * Add the typography section
 */
 


Kirki::add_section( 'body_typo', array(
    'title'          => __( 'Manage Typrography' , 'seoboost'),
    'description'    => __( 'You can change all the elements Fonts from here.', 'seoboost' ),
    'panel'          => 'typo_settings', // Not typically needed.
    'priority'       => 160,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

Kirki::add_field( 'seoboost_theme', array(
    'settings' => 'typo-settings',
    'section'  => 'body_typo',
    'type'     => 'custom',
    'default'  => __( 'This is only available in Premium Version. <br/><a target="_blank" href="http://www.dashthemes.com/themes/lifestyle/"> + Upgrade to Premium Version</a>', 'seoboost' ),
) );









Kirki::add_panel( 'color_settings', array(
	'priority'                  => 330,
	'title'                     => esc_html__( 'Color Settings', 'seoboost' ),
	'description'               => esc_html__( 'This panel contains the Color Controls', 'seoboost' ),
) );

/* Colors Section */

Kirki::add_section( 'title_section', array(
    'title'          => __( 'Manage Colors' , 'seoboost'),
    'description'    => __( 'You can change all the elements colors from here.', 'seoboost' ),
    'panel'          => 'color_settings', // Not typically needed.
    'priority'       => 160,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

Kirki::add_field( 'my_custom_text', array(
    'settings' => 'my_custom_text',
    'section'  => 'title_section',
    'type'     => 'custom',
    'default'  => __( 'This is only available in Premium Version. <br/><a target="_blank" href="http://www.dashthemes.com/themes/lifestyle/"> + Upgrade to Premium Version</a>', 'seoboost' ),
) );








}

add_action( 'customize_register', 'seoboost_customize_register' );





/**
 * Sanitize the colorscheme.
 *
 * @param string $input Color scheme.
 */
function seoboost_sanitize_colorscheme( $input ) {
	$valid = array( 'light', 'dark', 'custom' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'light';
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @since seoboost 1.0
 * @see seoboost_customize_register()
 *
 * @return void
 */
function seoboost_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since seoboost 1.0
 * @see seoboost_customize_register()
 *
 * @return void
 */
function seoboost_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Return whether we're previewing the front page and it's a static page.
 */
function seoboost_is_static_front_page() {
	return ( is_front_page() && ! is_home() );
}

/**
 * Return whether we're on a view that supports a one or two column layout.
 */
function seoboost_is_view_with_layout_option() {
	// This option is available on all pages. It's also available on archives when there isn't a sidebar.
	return ( is_page() || ( is_archive() && ! is_active_sidebar( 'sidebar-1' ) ) );
}

if ( ! function_exists( 'seoboost_sanitize_checkbox' ) ) :

	/**
	 * Sanitize checkbox.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $checked Whether the checkbox is checked.
	 * @return bool Whether the checkbox is checked.
	 */
	function seoboost_sanitize_checkbox( $checked ) {

		return ( ( isset( $checked ) && true === $checked ) ? true : false );

	}

endif;


if ( ! function_exists( 'seoboost_sanitize_positive_integer' ) ) :

	/**
	 * Sanitize positive integer.
	 *
	 * @since 1.0.0
	 *
	 * @param int                  $input Number to sanitize.
	 * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
	 * @return int Sanitized number; otherwise, the setting default.
	 */
	function seoboost_sanitize_positive_integer( $input, $setting ) {

		$input = absint( $input );

		// If the input is an absolute integer, return it.
		// otherwise, return the default.
		return ( $input ? $input : $setting->default );

	}

endif;

if ( ! function_exists( 'seoboost_sanitize_select' ) ) :

	/**
	 * Sanitize select.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                $input The value to sanitize.
	 * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
	 * @return mixed Sanitized value.
	 */
	function seoboost_sanitize_select( $input, $setting ) {

		// Ensure input is clean.
		$input = sanitize_text_field( $input );

		// Get list of choices from the control associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

	}

endif;



if ( ! function_exists( 'seoboost_default_theme_options' ) ) :

	/**
	 * Get default theme options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
	function seoboost_default_theme_options() {

		$defaults = array();

		// Header.
		$defaults['show_top_header'] 	= false;
		$defaults['header_top_left_section'] 		= 'tranding-news';
		$defaults['header_top_trending_title']		= esc_html__( 'Trending', 'seoboost' );
		$defaults['header_top_right_section'] 		= 'social-icons';
		$defaults['seoboost_header_trending_post_number'] = 5;

		//Back To Top
		$defaults['back_to_top']  	= 'disable';

		// Footer.
		$defaults['copyright_text'] 	= esc_html__( 'Copyright &copy; All rights reserved.', 'seoboost' );

		// Breadcrumb.
		$defaults['breadcrumb_type'] 	= 'disable';
		
		//slider active
		$defaults['seoboost_feature_post_status'] = false;
		
		return $defaults;
	}

endif;

if ( ! function_exists( 'seoboost_is_top_header_active' ) ) :

	/**
	 * Check if featured slider is active.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function seoboost_is_top_header_active( $control ) {

		if ( true == $control->manager->get_setting( 'show_top_header' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'seoboost_get_option' ) ) :

	/**
	 * Get theme option.
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function seoboost_get_option( $key ) {

		if ( empty( $key ) ) {

			return;

		}

		$value 			= '';

		$default 		= seoboost_default_theme_options();

		$default_value 	= null;

		if ( is_array( $default ) && isset( $default[ $key ] ) ) {

			$default_value = $default[ $key ];

		}

		if ( null !== $default_value ) {

			$value = get_theme_mod( $key, $default_value );

		}else {

			$value = get_theme_mod( $key );

		}

		return $value;

	}

endif;
if ( ! function_exists( 'seoboost_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog.
 *
 * @see seoboost_custom_header_setup().
 */
function seoboost_header_style() { 

$header_text_color = get_header_textcolor();
	if( !empty( $header_text_color ) ): ?>
		<style type="text/css">
			   .site-header a,
			   .site-header p{
					color: #<?php echo esc_attr( $header_text_color ); ?>;
			   }
		</style>
			
		<?php
	endif;
}

endif;

/**
 * Bind JS handlers to instantly live-preview changes.
 */
function seoboost_customize_preview_js() {
	wp_enqueue_script( 'seoboost-customize-preview', get_theme_file_uri( '/assets/js/customize-preview.js' ), array( 'customize-preview' ), '1.0', true );
}
add_action( 'customize_preview_init', 'seoboost_customize_preview_js' );

/**
 * Load dynamic logic for the customizer controls area.
 */
function seoboost_panels_js() {
	wp_enqueue_script( 'seoboost-customize-controls', get_theme_file_uri( '/assets/js/customize-controls.js' ), array(), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'seoboost_panels_js' );
