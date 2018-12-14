<?php



define( 'WP_USE_THEMES', false ); 
require( '/var/www/khmernewslive/wp-load.php' );
require_once('/var/www/khmernewslive/wp-admin/includes/media.php');
require_once('/var/www/khmernewslive/wp-admin/includes/file.php');
require_once('/var/www/khmernewslive/wp-admin/includes/image.php');

require_once "vendor/autoload.php";



$args = array(
	"post_status" => array('publish'), 
	'posts_per_page'   => 1,
	'orderby'          => 'date',
	'order'            => 'DESC',
);



$custom_posts = get_posts($args);

foreach($custom_posts as $post) : setup_postdata($post);
	$GLOBALS['my_post'] = [];
	$GLOBALS['post_id'] = $post->ID;
	$GLOBALS['my_post']['post_title'] = $post->post_title;
	$post_content = $post->post_content;
	$post_content = mb_substr($post_content, 0, 70, "UTF-8")."</p>";
	$post_meta = get_post_meta($post->ID);
	$app_link = $post_meta['app_link'][0];

	$url = get_permalink($GLOBALS['post_id'], true);
	$android_url = 'https://play.google.com/store/apps/details?id=com.khmernewslive24.app';
	// print_r($GLOBALS['my_post']);
	

	// print_r($post);



	$branch_key = 'key_live_leKpjCu7Ry6CklliGho9qnpdryljE4k5'; // your branch key.
	$branch_secret = 'secret_live_7zwH7PiVZSV51CYU2MMdkq76SiNDk0fo';
	$ch = curl_init('https://api.branch.io/v1/url?url='.$url);

	

	$payload = [
		'branch_key' => $branch_key,
	    'branch_secret' => $branch_secret,
	    'campaign' => 'Khmer News Live',
	    'channel' => "Facebook",
	    'type' => 2,
	    // 'alias' => $GLOBALS['post_id'],
	    'data' => [
	    	'$og_description' => $post_content,
	        '$desktop_url' => $url,
	        '$ios_url' => $url,
	        '$ipad_url' => $url,
	        '$android_url' =>  $android_url,
	    	'$og_image_url' => get_the_post_thumbnail_url($GLOBALS['post_id']),
	    	'$og_title' => $GLOBALS['my_post']['post_title'],
	    	'photo_id' => get_post_thumbnail_id($GLOBALS['post_id']),
	    	'$og_app_id' => '583396836417491667',
	     	'$og_title' => $GLOBALS['my_post']['post_title'],
	     	'$marketing_title' => $GLOBALS['my_post']['post_title'],
	     	'$android_uri_scheme'=> 'khmernewslive://',
			'$android_package_name'=> "com.khmernewslive24.app",
			'$android_app_links_enabled'=> "1",
			'$uri_redirect_mode' => 2
	    	]
	    ];

    

	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload));
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	# Return response instead of printing.
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	# Send request.
	$result = curl_exec($ch);
	
	$app_link = json_decode($result);


	print_r($app_link);

endforeach;
curl_close($ch);




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





