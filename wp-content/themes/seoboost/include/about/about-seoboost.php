<?php // About Seoboost

// Add About Seoboost Page
function seoboost_about_page() {
    add_theme_page( esc_html__( 'About Seoboost', 'seoboost' ), esc_html__( 'About Seoboost', 'seoboost' ), 'edit_theme_options', 'about-seoboost', 'seoboost_about_page_output' );
}
add_action( 'admin_menu', 'seoboost_about_page' );

// Render About seoboost HTML
function seoboost_about_page_output() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Welcome to seoboost!', 'seoboost' ); ?></h1>
        <p class="welcome-text">
            <?php esc_html_e( 'Seoboost is free personal and multi-author WordPress Blog theme. It\'s perfect for any kind of blog: personal, multi-author, food, lifestyle, etc... Is fully Responsive and Retina Display ready, clean, modern and minimal. Coded with latest WordPress\' standards.', 'seoboost' ); ?>
        </p>

        <!-- Tabs -->
        <?php $active_tab = isset( $_GET[ 'tab' ] ) ?  wp_unslash( $_GET['tab'] ) : 'seoboost_tab_1'; ?>

        <div class="nav-tab-wrapper">
            <a href="<?php echo esc_url('?page=about-seoboost&tab=seoboost_tab_1')?>" class="nav-tab <?php echo $active_tab == 'seoboost_tab_1' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e( 'Getting Started', 'seoboost' ); ?>
            </a>
            <a href="<?php echo esc_url('?page=about-seoboost&tab=seoboost_tab_2')?>" class="nav-tab <?php echo $active_tab == 'seoboost_tab_2' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e( 'Recommended Plugins', 'seoboost' ); ?>
            </a>
            <a href="<?php echo esc_url('?page=about-seoboost&tab=seoboost_tab_3')?>" class="nav-tab <?php echo $active_tab == 'seoboost_tab_3' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e( 'Support', 'seoboost' ); ?>
            </a>
            <a href="<?php echo esc_url('?page=about-seoboost&tab=seoboost_tab_4')?>" class="nav-tab <?php echo $active_tab == 'seoboost_tab_4' ? 'nav-tab-active' : ''; ?>">
                <?php esc_html_e( 'Free vs Premium', 'seoboost' ); ?>
            </a>
        </div>

        <!-- Tab Content -->
        <?php if ( $active_tab == 'seoboost_tab_1' ) : ?>

            <div class="three-columns-wrap">

                <br>

                <div class="column-wdith-3">
                    <h3><?php esc_html_e( 'Theme Documentation', 'seoboost' ); ?></h3>
                    <p>
                        <?php esc_html_e( 'Highly recommended to begin with this, read the full theme documentation to understand the basics and even more details about how to use seoboost. It is worth to spend 10 minutes and know almost everything about the theme.', 'seoboost' ); ?>
                    </p>
                    <a target="_blank" href="http://support.dashthemes.com/category/seoboost/" class="button button-primary"><?php esc_html_e( 'Read Documentation', 'seoboost' ); ?></a>
                </div>


                <div class="column-wdith-3">
                    <h3><?php esc_html_e( 'Theme Customizer', 'seoboost' ); ?></h3>
                    <p>
                        <?php esc_html_e( 'All theme options are located here. After reading the Theme Documentation we recommend you to open the Theme Customizer and play with some options. You will enjoy it.', 'seoboost' ); ?>
                    </p>
                    <a target="_blank" href="<?php echo esc_url( wp_customize_url() );?>" class="button button-primary"><?php esc_html_e( 'Customize Your Site', 'seoboost' ); ?></a>
                </div>

            </div>

            <div class="four-columns-wrap">

                <h2><?php esc_html_e( 'Seoboost Premium - Predefined Styles', 'seoboost' ); ?></h2>
                <p>
                    <?php esc_html_e( 'Seoboost Premium\'s powerful setup allows you to easily create unique looking sites. Here are a few included examples that can be installed with one click in the ', 'seoboost' ); ?>
                    <a target="_blank" href="http://www.dashthemes.com/themes/seoboost/"><?php esc_html_e( 'Seoboost Premium Theme.', 'seoboost' ); ?></a>
                    <?php esc_html_e( 'More details in the theme Documentation.', 'seoboost' ); ?>
                </p>

                <div class="column-wdith-4">
                    <div class="active-style"><?php esc_html_e( 'Active', 'seoboost' ); ?></div>

                    <div>
                        <h2><?php esc_html_e( 'Style 1', 'seoboost' ); ?></h2>
                        <a href="http://demo.dashthemes.com/seoboost/" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'seoboost' ); ?></a>
                    </div>
                </div>
                <div class="column-wdith-4">

                    <div>
                        <h2><?php esc_html_e( 'Style 2', 'seoboost' ); ?></h2>
                        <a href="http://demo.dashthemes.com/seoboost/?list-style=yes" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'seoboost' ); ?></a>
                    </div>
                </div>
                <div class="column-wdith-4">

                    <div>
                        <h2><?php esc_html_e( 'Style 3', 'seoboost' ); ?></h2>
                        <a href="http://demo.dashthemes.com/seoboost/?grid-full-style=yes" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'seoboost' ); ?></a>
                    </div>
                </div>
				 <div class="column-wdith-4">

                    <div>
                        <h2><?php esc_html_e( 'Style 4', 'seoboost' ); ?></h2>
                        <a href="http://demo.dashthemes.com/seoboost/?simple-style=yes" target="_blank" class="button button-primary"><?php esc_html_e( 'Live Preview', 'seoboost' ); ?></a>
                    </div>
                </div>


            </div>

        <?php elseif ( $active_tab == 'seoboost_tab_2' ) : ?>

            <div class="three-columns-wrap">

                <br>
                <p><?php esc_html_e( 'Recommended Plugins are fully supported by seoboost theme. They well build the theme by giving more and more features. These are highly recommended to install.', 'seoboost' ); ?></p>
                <br>

                <?php


                // Kirki
                seoboost_recommended_plugin( 'kirki', 'index', esc_html__( 'Kirki', 'seoboost' ), esc_html__( 'Theme advanced customizer options.', 'seoboost' ) );

                // MailChimp
                seoboost_recommended_plugin( 'mailchimp-for-wp', 'mailchimp-for-wp', esc_html__( 'Mailchimp', 'seoboost' ), esc_html__( 'Mail newsletters. Simple but flexible.', 'seoboost' ) );

          
                ?>


            </div>

        <?php elseif ( $active_tab == 'seoboost_tab_3' ) : ?>

            <div class="three-columns-wrap">

                <br>

                <div class="column-wdith-3">
                    <h3>
                        <span class="dashicons dashicons-sos"></span>
                        <?php esc_html_e( 'Forums', 'seoboost' ); ?>
                    </h3>
                    <p>
                        <?php esc_html_e( 'Before asking a questions it\'s highly recommended to search on forums, but if you can\'t find the solution feel free to create a new topic.', 'seoboost' ); ?>
                    <hr>
                    <a target="_blank" href="http://support.dashthemes.com/"><?php esc_html_e( 'Go to Support Forums', 'seoboost' ); ?></a>
                    </p>
                </div>

                <div class="column-wdith-3">
                    <h3>
                        <span class="dashicons dashicons-book"></span>
                        <?php esc_html_e( 'Documentation', 'seoboost' ); ?>
                    </h3>
                    <p>
                        <?php esc_html_e( 'Need more details? Please check out seoboost Theme Documentation for detailed information.', 'seoboost' ); ?>
                    <hr>
                    <a target="_blank" href="http://support.dashthemes.com/category/seoboost/"><?php esc_html_e( 'Read Full Documentation', 'seoboost' ); ?></a>
                    </p>
                </div>


                <div class="column-wdith-3">
                    <h3>
                        <span class="dashicons dashicons-smiley"></span>
                        <?php esc_html_e( 'Donation', 'seoboost' ); ?>
                    </h3>
                    <p>
                        <?php esc_html_e( 'Even a small sum can help us a lot with theme development. If the seoboost theme is useful and our support is helpful, please don\'t hesitate to donate a little bit, at least buy us a Coffee or a Beer :)', 'seoboost' ); ?>
                    <hr>
                    <a target="_blank" href="<?php echo esc_url('https://www.paypal.me/themexpose'); ?>"><?php esc_html_e( 'Donate with PayPal', 'seoboost' ); ?></a>
                    </p>
                </div>

            </div>

        <?php elseif ( $active_tab == 'seoboost_tab_4' ) : ?>

            <br><br>

            <table class="free-vs-pro form-table">
                <thead>
                <tr>
                    <th>
                        <a href="http://www.dashthemes.com/themes/seoboost/" target="_blank" class="button button-primary button-hero">
                            <?php esc_html_e( 'Get Seoboost Premium', 'seoboost' ); ?>
                        </a>

                    </th>
                    <th><?php esc_html_e( 'seoboost', 'seoboost' ); ?></th>
                    <th><?php esc_html_e( 'seoboost Premium', 'seoboost' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <h3><?php esc_html_e( '100% Responsive and Retina Ready', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Theme adapts to any kind of device screen, from mobile phones to high resolution Retina displays.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Translation Ready', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Each hard-coded string is ready for translation, means you can translate everything. Language "seoboost.pot" file included.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'MailChimp Support', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'The most popular email client plugin. Very simple but super flexible.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Image &amp; Text Logos', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Upload your logo image(set the size) or simply type your text logo.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Featured Posts Slider', 'seoboost' ); ?></h3>
                        <p>
                            <?php esc_html_e( 'Showcase unlimited number of Blog Posts in header area. There are three Slider designs.', 'seoboost' ); ?>
                        </p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Background Image/Color', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Set the custom body Background image or Color.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Header Background Image/Color', 'seoboost' ); ?></h3>
                        <p>
                            <?php esc_html_e( 'Set the custom header Background image or Color.', 'seoboost' ); ?>
                        </p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Instagram Widget Area', 'seoboost' ); ?></h3>
                        <p>
                            <?php esc_html_e( 'Set your Instagram Images in the footer.', 'seoboost' ); ?>
                        </p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>

                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Post Layouts', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Standard, List and Grid Blog Feed layout.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Multi-level Sub Menu Support', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Unlimited level of sub menus. Add as much as you need.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Right Sidebar', 'seoboost' ); ?></h3>
                        <p>
                            <?php esc_html_e( 'Right Widgetised area.', 'seoboost' ); ?>
                        </p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <!-- Only Pro -->
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Unlimited Colors', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Tons of color options. You can customize your Header, Content and Footer separately as much as possible.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
				
                <tr>
                    <td>
                        <h3><?php esc_html_e( '800+ Google Fonts', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Rich Typography options. Choose from more than 800 Google Fonts, adjust Size, Line Height, Font Weight, etc...', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Grid Layout', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Choose from 1 up to 4 columns grid layout for your Blog Feed.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'List Layout', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Choose from 1 up to 4 columns grid layout for your Blog Feed.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>
                <tr>
                    <td>
                        <h3><?php esc_html_e( 'Advanced Footer Options', 'seoboost' ); ?></h3>
                        <p><?php esc_html_e( 'Theme and Author credit links in the footer are automatically removed.', 'seoboost' ); ?></p>
                    </td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-no"></span></td>
                    <td class="compare-icon"><span class="dashicons-before dashicons-yes"></span></td>
                </tr>


                <tr>
                    <td></td>
                    <td colspan="2">
                        <a href="http://www.dashthemes.com/themes/seoboost/" target="_blank" class="button button-primary button-hero">
                            <?php esc_html_e( 'Get Seoboost Premium', 'seoboost' ); ?>
                        </a>
                        <br>

                    </td>
                </tr>
                </tbody>
            </table>

        <?php endif; ?>

    </div><!-- /.wrap -->
    <?php
} // end seoboost_about_page_output

// Check if plugin is installed
function seoboost_check_installed_plugin( $slug, $filename ) {
    return file_exists( ABSPATH . 'wp-content/plugins/' . $slug . '/' . $filename . '.php' ) ? true : false;
}

// Generate Recommended Plugin HTML
function seoboost_recommended_plugin( $slug, $filename, $name, $description) {

    if ( $slug === 'facebook-pagelike-widget' ) {
        $size = '128x128';
    } else {
        $size = '256x256';
    }

    ?>

    <div class="plugin-card">
        <div class="name column-name">
            <h3>
                <?php echo esc_html( $name ); ?>
                <img src="<?php echo esc_url('https://ps.w.org/'. $slug .'/assets/icon-'. $size .'.png') ?>" class="plugin-icon" alt="">
            </h3>
        </div>
        <div class="action-links">
            <?php if ( seoboost_check_installed_plugin( $slug, $filename ) ) : ?>
                <button type="button" class="button button-disabled" disabled="disabled"><?php esc_html_e( 'Installed', 'seoboost' ); ?></button>
            <?php else : ?>
                <a class="install-now button-primary" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin='. $slug ), 'install-plugin_'. $slug ) ); ?>" >
                    <?php esc_html_e( 'Install Now', 'seoboost' ); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="desc column-description">
            <p><?php echo esc_html( $description ); ?></p>
        </div>
    </div>

    <?php
}

// enqueue ui CSS/JS
function seoboost_enqueue_about_page_scripts($hook) {

    if ( 'appearance_page_about-seoboost' != $hook ) {
        return;
    }

    // enqueue CSS
    wp_enqueue_style( 'seoboost-about-page-css', get_theme_file_uri( '/include/about/css/about-seoboost-page.css' ) );

}
add_action( 'admin_enqueue_scripts', 'seoboost_enqueue_about_page_scripts' );