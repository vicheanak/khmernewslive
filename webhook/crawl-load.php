<?php
define( 'WP_USE_THEMES', false ); 
require( '../wp-load.php' );
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

require_once "vendor/autoload.php";


use Stichoza\GoogleTranslate\TranslateClient;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

$client = new Client();
$guzzleClient = new GuzzleClient(array(
    'timeout' => 250,
));
$client->setClient($guzzleClient);

$proxies = explode("\n", file_get_contents('proxies.txt'));
$GLOBALS['arr_proxies'] = array();
foreach ($proxies as $p){
	if ( preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\:?([0-9]{1,5})?/', $p, $match) ) {
	   $GLOBALS['arr_proxies'][] = $match['1'] . (isset($match['2']) ? ':' . $match['2'] : '');
	}
}


// print_r($GLOBALS['arr_proxies'][rand(0,9)]);
$GLOBALS['post_id'] = '';
$GLOBALS['cats'] = array(
	array('id'=>6, 'keywords' => array('ប្លែកៗ','ថ្មីៗ','ផ្សេងៗ')),
	array('id'=>2, 'keywords' => array('ព័ត៌មានសង្គម','កីឡា','ព័ត៌មាន')),
	array('id'=>1, 'keywords' => array('យល់ដឹង', 'គំនិត', 'ចំណេះដឹង','បទពិសោធន៍')),
	array('id'=>3, 'keywords' => array('សិល្បៈ និងកម្សាន្ត', 'សិល្បះ និង កម្សាន្ត', 'វីដេអូ', 'កម្សាន្ត')),
	array('id'=>4, 'keywords' => array('សុខភាព', 'ចែកចាយ','មេរៀនជីវិត','សុខភាព និងសម្រស់','កម្រងទេសភាព'))
);

function Generate_Featured_Image( $file, $post_id ){
    // Set variables for storage, fix file filename for query strings.
    preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
    
    if ( ! $matches ) {
         return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
    }

    $file_array = array();
    $file_array['name'] = basename( $matches[0] );
	
    // Download file to temp location.
    $file_array['tmp_name'] = download_url( $file );
    // print_r($file_array['tmp_name']);

    // If error storing temporarily, return the error.
    if ( is_wp_error( $file_array['tmp_name'] ) ) {

        return $file_array['tmp_name'];
    }


    // Do the validation and storage stuff.
    $id = media_handle_sideload( $file_array, $post_id);

    // If error storing permanently, unlink.
    if ( is_wp_error( $id ) ) {
        @unlink( $file_array['tmp_name'] );
        return $id;
    }

    
    return set_post_thumbnail($post_id,$id);

}
