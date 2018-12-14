<?php



define( 'WP_USE_THEMES', false ); 
require( '/var/www/khmernewslive/wp-load.php' );
require_once('/var/www/khmernewslive/wp-admin/includes/media.php');
require_once('/var/www/khmernewslive/wp-admin/includes/file.php');
require_once('/var/www/khmernewslive/wp-admin/includes/image.php');

require_once "vendor/autoload.php";

use \Iivannov\Branchio\Link;
use \Iivannov\Branchio\Client;
use \Iivannov\Branchio\UrlType;


$branch_key = 'key_live_leKpjCu7Ry6CklliGho9qnpdryljE4k5'; // your branch key.
$branch_secret = 'secret_live_7zwH7PiVZSV51CYU2MMdkq76SiNDk0fo';
// $ch = curl_init('https://api.branch.io/v1/url');


$args = array(
	"post_status" => array('publish'), 
	'posts_per_page'   => 200,
	'orderby'          => 'date',
	'order'            => 'DESC',
);



$custom_posts = get_posts($args);

foreach($custom_posts as $post) : setup_postdata($post);
	
	// $app_link = get_post_meta( $post->ID, 'app_link', true );

	$link = new Link();
	 
	$link->setChannel('Facebook')
		->setCampaign('Khmer News Live')
    	->setAlias($post->ID)
    	->setFeature('Share')
    	->setType(2);

    $url = get_permalink($query->post->ID, true);

   	$data = [
	     '$always_deeplink' => true,
	     '$desktop_url' => $url,
	     '$ios_url' => $url,
	     '$ipad_url' => $url,
	     '$android_url' => $url,
	     '$og_app_id' => '1905620589497116',
	     '$og_title' => $query->post->post_title,
	     '$og_description' => $query->post->post_excerpt,
	     '$og_image_url' => get_the_post_thumbnail_url($post->ID),
		 'photo_id' => get_post_thumbnail_id($post->ID),
		 '$marketing_title' => $query->post->post_title
	];
	 
	$link->setData($data);

	$client = new Client($branch_key, $branch_secret);

	$client->updateLink($url, $data, $type);
	
	$app_link = $client->createLink($link);
	update_post_meta( $post->ID, 'app_link',  $app_link);
	

endforeach;


// wp_reset_postdata();

// $loop = get_posts($query);

// $post_ids = array();

// while ( $query->have_posts() ) : $query->the_post();


// 	$payload = array(
// 	'branch_key' => $branch_key,
// 	'branch_secret' => $branch_secret,
//     'campaign' => 'Genesis',
//     "channel" => "facebook",
//     'data' => array(
//         '$desktop_url' => the_permalink($query->post->ID),
//         '$ios_url' => 'https://www.khmernewslive24.com/?p=33480',
//         '$ipad_url' => 'https://www.khmernewslive24.com/?p=33480',
//         '$android_url' => 'https://www.khmernewslive24.com/?p=33480',
//     	"$og_image_url" => "https://www.khmernewslive24.com/wp-content/uploads/2018/10/ice_screenshot_20181023-111449.png",
//     	"photo_id" => "111450",
//     	)
//     );

// 	// $payload = array(
// 	// "branch_key" => $branch_key,
//  //    "branch_secret" => $branch_secret,
//  //    'campaign' => 'Spread App',
//  //    "channel" => "Facebook",
//  //    'data' => array(
//  //        '$desktop_url' => the_permalink(),
//  //        '$ios_url' => the_permalink(),
//  //        '$ipad_url' => the_permalink(),
//  //        '$android_url' =>  the_permalink(),
//  //    	"$og_image_url" => get_the_post_thumbnail_url(),
//  //    	"$og_title" => get_the_title(),
//  //    	"photo_id" => get_post_thumbnail_id(),
//  //    	)
//  //    );

// 	print_r($payload);
// 	// curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload));
// 	// curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
// 	// # Return response instead of printing.
// 	// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
// 	// # Send request.
// 	// $result = curl_exec($ch);
	
// 	// $results = json_decode($result);
	
// 	// update_post_meta( get_the_ID(), 'app_link',  $results->url);


// endwhile;





