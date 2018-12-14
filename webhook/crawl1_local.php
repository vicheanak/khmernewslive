<?php

define( 'WP_USE_THEMES', false ); 
require( '/var/www/khmernewslocal/wp-load.php' );
require_once('/var/www/khmernewslocal/wp-admin/includes/media.php');
require_once('/var/www/khmernewslocal/wp-admin/includes/file.php');
require_once('/var/www/khmernewslocal/wp-admin/includes/image.php');

require_once "vendor/autoload.php";


use Stichoza\GoogleTranslate\TranslateClient;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

use Symfony\Component\DomCrawler\Crawler;





$client = new Client();

$GLOBALS['post_id'] = '';


function uploadRemoteImageAndAttach($image_url, $parent_id){

    $image = $image_url;

    $cookie = new WP_Http_Cookie('voanews');
	$cookie->name = 'SessionID';
	$cookie->value = '716181932.20480.0000';
	$cookie->expires = mktime( 0, 0, 0, date('m'), date('d') + 7, date('Y') ); // expires in 7 days
	$cookie->path = '/';
	$cookie->domain = 'gdb.voanews.com';

	$cookies[] = $cookie;

    $args = array(
	    'timeout'     => 2000,
	    'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0',
	    'cookies'	  => $cookies
	); 

    $get = wp_remote_get( $image, $args );

    $type = wp_remote_retrieve_header( $get, 'content-type' );
    
    
    if (!$type)
        return false;

    $mirror = wp_upload_bits( basename( $image ), '', wp_remote_retrieve_body( $get ) );


    $attachment = array(
        'post_title'=> basename( $image ),
        'post_mime_type' => $type
    );

    $attach_id = wp_insert_attachment( $attachment, $mirror['file'], $parent_id );
  

    $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );

    wp_update_attachment_metadata( $attach_id, $attach_data );

    
    return set_post_thumbnail($parent_id,$attach_id);

}

function Generate_Featured_Image( $file, $post_id ){
    // Set variables for storage, fix file filename for query strings.
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );

	if ( ! $matches ) {
		return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
	}

	$file_array = array();
	$file_array['name'] = basename( $matches[0] );
	
    // Download file to temp location.
    // echo strpos($file, 'http');
	if (strpos($file, 'http') > -1){
		$file_array['tmp_name'] = download_url( $file, $timeout = 2000  );	
	}
	else{
		$file_array['tmp_name'] = download_url( 'http:'.$file, $timeout = 2000  );
	}

	

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


function Generate_Image( $file ){
    // Set variables for storage, fix file filename for query strings.
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );

	if ( ! $matches ) {
		return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
	}

	$file_array = array();
	$file_array['name'] = basename( $matches[0] );
	
    // Download file to temp location.

	if (strpos($file, 'http') > -1){
		$file_array['tmp_name'] = download_url( $file );	
	}
	else{
		$file_array['tmp_name'] = download_url( 'http:'.$file );
	}


    // If error storing temporarily, return the error.
	if ( is_wp_error( $file_array['tmp_name'] ) ) {
		return $file_array['tmp_name'];
	}

	
    // Do the validation and storage stuff.
	$id = media_handle_sideload( $file_array, 0);

    // If error storing permanently, unlink.
	if ( is_wp_error( $id ) ) {
		@unlink( $file_array['tmp_name'] );
		return $id;
	}

	$image_attributes = wp_get_attachment_image($id, Array(640, 480) );



	if ( $image_attributes ) :
		return $image_attributes;
	endif;

	return false;



}
 

$crawl_links = array(
	array(
		'link' => 'https://kohsantepheapdaily.com.kh/category/local',
		'href_filter' => 'div.col-md-3.col-sm-12.col-xs-12 >article > h3 > a',
		'title_filter' => '.row > .col-lg-8.col-md-8 > h1',
		'post_category' => 1,
		'content_filter' => 'div.col-lg-8.col-md-8 > .content-text > p',
		'image_filter' => 'div.col-lg-8.col-md-8 > .content-text > p > img',
		'iframe_filter' => 'div.col-lg-8.col-md-8 > .content-text > p > iframe',
		'tag_input' => array('កោះសន្តិភាព'),
		'feature_image_filter' => 'div.col-lg-8.col-md-8 > .content-text > img'
	),  
	array(
		'link' => 'https://www.dap-news.com/category/national/',
		'href_filter' => '.infinite-post > a',
		'title_filter' => '#post-header > h1',
		'post_category' => 35,
		'content_filter' => '#content-main > p',
		'image_filter' => '#content-main > figure img',
		'iframe_filter' => '#content-main > p > iframe',
		'tag_input' => array('ដើមអំពិល'),
		'feature_image_filter' => '#post-feat-img > img'
	),  
	array(
		'link' => 'http://www.rasmeinews.com/category/localnews/',
		'href_filter' => '.td-block-row .entry-title.td-module-title > h3 > a',
		'title_filter' => '.td-post-title > h1.entry-title',
		'post_category' => 36,
		'content_filter' => '.td-post-content > p',
		'image_filter' => '.td-post-content > p > img',
		'iframe_filter' => '.fb-video iframe',
		'tag_input' => array('រស្មីកម្ពុជា'),
		'feature_image_filter' => '.td-post-content > p > img'
	),  
	array(
		'link' => 'http://ppt-news.net/?cat=3',
		'href_filter' => 'h2.entry-title > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 37,
		'content_filter' => '.entry.entry-content > p',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('ភ្នំពេញថ្មី'),
		'feature_image_filter' => '.entry.entry-content > p img'
	),  
	// array(
	// 	'link' => 'http://kampucheathmey.com/2016/ព័ត៌មានជាតិ',
	// 	'href_filter' => '.image-post-title.feature_2col',
	// 	'title_filter' => 'h1.entry-title.single-post-title.heading_post_title',
	// 	'post_category' => 38,
	// 	'content_filter' => '.post_content > p',
	// 	'image_filter' => '',
	// 	'iframe_filter' => '',
	// 	'tag_input' => array('កម្ពុជាថ្មី'),
	// 	'feature_image_filter' => '.single_post_format_image > img'
	// ),  
	array(
		'link' => 'http://www.freshnewsasia.com/index.php/en/localnews.html',
		'href_filter' => '.list-title > a',
		'title_filter' => '.page-header > h2[itemprop="headline"]',
		'post_category' => 39,
		'content_filter' => 'div[itemprop="articleBody"] > p',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('Fresh News'),
		'feature_image_filter' => 'div[itemprop="articleBody"] > p > img'
	),  
	array(
		'link' => 'http://www.cen.com.kh/archives/category/national/',
		'href_filter' => '.header-standard > h2 > a',
		'title_filter' => 'h1.post-title.single-post-title',
		'post_category' => 40,
		'content_filter' => '.inner-post-entry > p',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('CEN'),
		'feature_image_filter' => '.inner-post-entry > p > img'
	),  
	array(
		'link' => 'https://www.postkhmer.com/ព័ត៌មានជាតិ',
		'href_filter' => '.stories-item > .article-image > a',
		'title_filter' => '.section.section-width-sidebar.single-article-header > h2',
		'post_category' => 41,
		'content_filter' => 'div[itemprop="articleBody"]',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('ភ្នំពេញប៉ុស្ត៍'),
		'feature_image_filter' => 'img[itemprop="contentURL"]'
	),  
	array(
		'link' => 'http://vayofm.com/index.php?option=list&id=nall',
		'href_filter' => '.media-heading.font-feature > a',
		'title_filter' => '.detail-title > h1',
		'post_category' => 42,
		'content_filter' => '.detail-text > p',
		'image_filter' => '',
		'iframe_filter' => '#whole_sound_news',
		'tag_input' => array('វាយោ'),
		'feature_image_filter' => '.detail-thumbnail > img'
	),  
	array(
		'link' => 'https://khmer.voanews.com/z/2277',
		'href_filter' => '.media-block.horizontal.with-date.has-img.size-3 > a',
		'title_filter' => '.col-title.col-xs-12 > h1',
		'post_category' => 43,
		'content_filter' => '#article-content p',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('VOA'),
		'feature_image_filter' => '.cover-media img'
	),  
	array(
		'link' => 'https://www.rfa.org/khmer',
		'href_filter' => '.single_column_teaser > h2 > a',
		'title_filter' => '#storypagemaincol > h1',
		'post_category' => 44,
		'content_filter' => '#storytext > p > span',
		'image_filter' => '',
		'iframe_filter' => 'audio.story_audio',
		'tag_input' => array('RFA អាសុីសេរី'),
		'feature_image_filter' => '#headerimg > img'
	),  
	array(
		'link' => 'http://km.rfi.fr/cambodia',
		'href_filter' => '#articleList > li[data-bo-type="article"] > a',
		'title_filter' => 'h1[itemprop="name"]',
		'post_category' => 45,
		'content_filter' => 'div[itemprop="articleBody"]',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('RFI បារាំង'),
		'feature_image_filter' => 'img[itemprop="image"]'
	),  
	array(
		'link' => 'https://www.cnc.com.kh/news',
		'href_filter' => '.col-sm-12.col-md-7.col-lg-7 > a',
		'title_filter' => '.n-text-primary.font-family-bayon.h4',
		'post_category' => 46,
		'content_filter' => '.contentText > p',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('CNC'),
		'feature_image_filter' => '.contentText img'
	),  
	array(
		'link' => 'https://thmeythmey.com/?page=location&id=9',
		'href_filter' => '.title_kh.lineheight26_kh.size18_kh.dark.left > a',
		'title_filter' => '.detail_dp_title_ctn.left > span',
		'post_category' => 47,
		'content_filter' => '.detail_desc.left > span > div',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('ថ្មីថ្មី'),
		'feature_image_filter' => '#myImg'
	), 
);





foreach ($crawl_links as $crawl_link){
	$GLOBALS['crawl_link'] = $crawl_link;
	
	$crawler = $client->request('GET', $GLOBALS['crawl_link']['link']);

	
	

	$links = $crawler->filter($GLOBALS['crawl_link']['href_filter'])->each(function($node_link){
		
		

		$GLOBALS['my_post'] = array();
		$GLOBALS['my_post']['post_category'] = array();

		$post_link = $node_link->attr('href');

		if ($GLOBALS['crawl_link']['tag_input'][0] == 'ភ្នំពេញប៉ុស្ត៍'){
			$post_link = 'https://www.postkhmer.com'.$node_link->attr('href');
		}

		if ($GLOBALS['crawl_link']['tag_input'][0] == 'VOA'){
			$post_link = 'https://khmer.voanews.com'.$node_link->attr('href');
		}

		if ($GLOBALS['crawl_link']['tag_input'][0] == 'RFI បារាំង'){
			$post_link = 'http://km.rfi.fr'.$node_link->attr('href');
		}

		if ($GLOBALS['crawl_link']['tag_input'][0] == 'ថ្មីថ្មី'){
			$post_link = 'https://thmeythmey.com'.$node_link->attr('href');
		}
		
		if ($GLOBALS['crawl_link']['tag_input'][0] == 'Fresh News'){
			$post_link = 'http://www.freshnewsasia.com'.$node_link->attr('href');
		}
		
		
		

		$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
		$posts = get_posts($args);
		
		if (count($posts) < 1){
			
			$client = new Client();


			$crawler = $client->request('GET', $post_link);
			
			
			$crawler->filter($GLOBALS['crawl_link']['title_filter'])->each(function ($node) {
				
				$GLOBALS['my_post']['post_title'] = remove_emoji($node->text());

			});
			
			

			if (count($GLOBALS['my_post']['post_category']) < 1){
				$GLOBALS['my_post']['post_category'][] = $GLOBALS['crawl_link']['post_category'];
			}

			$GLOBALS['post_content'] = array();
			
			
		
			
			//iframe audio
			if ($GLOBALS['crawl_link']['tag_input'][0] == 'VOA'){
				$iframe_audio = $crawler->filter('meta[name="twitter:player"]');
				if ($iframe_audio->count()){
					$iframe_audio = $iframe_audio->eq(0)->attr('content');
					$iframe_audio = '<iframe src="'.$iframe_audio.'?type=audio" frameborder="0" scrolling="no" width="100%" height="144" allowfullscreen></iframe>';
					
					$GLOBALS['post_content'][] = $iframe_audio;	
					
				}
			}
			elseif($GLOBALS['crawl_link']['tag_input'][0] == 'វាយោ'){
				try{
					$audio = $crawler->filter($GLOBALS['crawl_link']['iframe_filter'])->each(function($node){
						$audio = $node->attr('src');

						$audio = '<audio id="whole_sound_news" src="'.$audio.'" style="" "="" controls=""></audio>';

						
						$GLOBALS['post_content'][] = $audio;	
						
						
					});
				}catch(Exception $e){

				}
			}elseif($GLOBALS['crawl_link']['tag_input'][0] == 'RFA អាសុីសេរី'){
				try{
					$audio = $crawler->filter($GLOBALS['crawl_link']['iframe_filter'])->each(function($node){
						$audio = $node->attr('src');

						// $audio = '<audio class="story_audio" type="audio/mpeg" preload="none" src="'.$audio.'"></audio>';
						$audio = '<audio id="whole_sound_news" src="'.$audio.'" style="" "="" controls=""></audio>';

						
						$GLOBALS['post_content'][] = $audio;	
						
						
					});
				}catch(Exception $e){

				}
			}




			$content = $crawler->filter($GLOBALS['crawl_link']['content_filter']);

			if ($content->count()){
				$content->each(function ($node) {
					
					$html = html_entity_decode($node->html());
					
					if (strpos($html, '</script>') < 1){
						if ($node->text() != '' && $node->text() != '។'){
							$text = remove_emoji($node->text());
							
							
							$GLOBALS['post_content'][] = $text;	
							

						}
					}
				
				});
			}
			else{
				if ($GLOBALS['crawl_link']['tag_input'][0] == 'VOA'){
					$crawler->filter('.intro.m-t-md')->each(function($node){
						$html = html_entity_decode($node->html());
						
						if (strpos($html, '</script>') < 1){
							if ($node->text() != ''){
								$text = remove_emoji($node->text());
								$GLOBALS['post_content'][] = $text;	
							}
						}
					});
				}

				if ($GLOBALS['crawl_link']['tag_input'][0] == 'RFI បារាំង'){
					$crawler->filter('.intro > p')->each(function($node){
						$html = html_entity_decode($node->html());
						
						if (strpos($html, '</script>') < 1){
							if ($node->text() != ''){
								$text = remove_emoji($node->text());
								$GLOBALS['post_content'][] = $text;	
							}
						}
					});
				}
				
			}


			if ($GLOBALS['crawl_link']['tag_input'][0] == 'RFA អាសុីសេរី'){
				$post_content = implode('', $GLOBALS['post_content']);
				$post_content = explode('។', $post_content);
				$post_content = "<p>".implode('។</p><p>', $GLOBALS['post_content'])."។</p>";
				
			}
			else{
				$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
			}
			
			$GLOBALS['my_post']['post_content'] = $post_content;

			$GLOBALS['my_post']['post_status'] = 'publish';
			$GLOBALS['my_post']['post_author'] = 1;
			
			$GLOBALS['my_post']['tags_input'] = $GLOBALS['crawl_link']['tag_input'];
			
			remove_all_filters("content_save_pre");

			$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );

			update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
			

			try{
				
				$image = $crawler->filter($GLOBALS['crawl_link']['feature_image_filter']);
				
				
				if ($image->count()){
					$image = $image->eq(0)->attr('src');
				}
				else{
					$image = $crawler->filter('.c-mmp__poster.js-poster > img')->eq(0);
					$image = $image->attr('src');
				}

				if ($GLOBALS['crawl_link']['tag_input'][0] == 'VOA'){
					$extension = explode(".", $image);
					$extension = $extension[count($extension) - 1];
					$image = explode("_", $image);
					$image = $image[0].'.'.$extension;
					
				}
				elseif($GLOBALS['crawl_link']['tag_input'][0] == 'RFA អាសុីសេរី'){
					
					$image = explode("/image", $image);
					$image = $image[0];
				}
				
				uploadRemoteImageAndAttach($image, $GLOBALS['post_id']);
				// Generate_Featured_Image($image, $GLOBALS['post_id']);
				
			} catch (Exception $e){

			}




			$branch_key = 'key_live_lkQmfrUnHRzhvvzi3I4rWnkfsBdMXBf6'; // your branch key.
			$branch_secret = 'secret_live_eBwbU2M81fS5gnIGGyt2X7uhfOF6KNqD';
			$ch = curl_init('https://api.branch.io/v1/url');

			$url = get_permalink($GLOBALS['post_id'], true);

			$payload = [
			'branch_key' => $branch_key,
		    'branch_secret' => $branch_secret,
		    'campaign' => 'Khmer News Local',
		    'channel' => "Facebook",
		    'type' => 2,
		    'alias' => $GLOBALS['post_id'],
		    'data' => [
		    	'$always_deeplink' => true,
		        '$desktop_url' => $url,
		        '$ios_url' => $url,
		        '$ipad_url' => $url,
		        '$android_url' =>  $url,
		    	'$og_image_url' => get_the_post_thumbnail_url($GLOBALS['post_id']),
		    	'$og_title' => $GLOBALS['my_post']['post_title'],
		    	'photo_id' => get_post_thumbnail_id($GLOBALS['post_id']),
		    	'$og_app_id' => '583396836417491667',
		     	'$og_title' => $GLOBALS['my_post']['post_title'],
		     	'$marketing_title' => $GLOBALS['my_post']['post_title']
		    	]
		    ];

		    

			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload));
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			# Return response instead of printing.
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			# Send request.
			$result = curl_exec($ch);

			$app_link = json_decode($result);

			update_post_meta($GLOBALS['post_id'], 'app_link',  $app_link->url);

			


		}
		
		
	});


	
}



$special_links = array(
	array(
		'link' => 'https://www.postkhmer.com/ព័ត៌មានជាតិ',
		'href_filter' => '.stories-item > .article-image > a',
		'title_filter' => '.single-article-header > h2',
		'post_category' => 41,
		'content_filter' => '#ArticleBody > p',
		'image_filter' => '',
		'iframe_filter' => '',
		'tag_input' => array('ភ្នំពេញប៉ុស្ត៍'),
		'feature_image_filter' => 'img[itemprop="contentURL"]'
	),  
);



foreach ($special_links as $crawl_link){
	$GLOBALS['crawl_link'] = $crawl_link;

	$crawler = $client->request('GET', $GLOBALS['crawl_link']['link']);

	
	if ($GLOBALS['crawl_link']['tag_input'][0] == 'ភ្នំពេញប៉ុស្ត៍'){
		$content = $client->getInternalResponse()->getContent();
	
		$regex = '~<div class=\"article-image\"\>(.*?)</div>~s';
		
		preg_match_all($regex, $content, $matches);

		$content = array();
		foreach($matches[0] as $m){
			$content[] = $m;
		}

		$urls = array();
		foreach($content as $con){
			$doc = new DOMDocument();
			$doc->resolveExternals = false;
			$doc->substituteEntities = false;
			$doc->loadHTML($con);
			foreach ($doc->getElementsByTagName('a') as $node)
			{
			  $urls[] = urldecode($node->getAttribute('href'));
			}
		}
	}


	foreach ($urls as $post_link){
		$GLOBALS['my_post'] = array();
		$GLOBALS['my_post']['post_category'] = array();

		// $post_link = $node_link->attr('href');

		if ($GLOBALS['crawl_link']['tag_input'][0] == 'ភ្នំពេញប៉ុស្ត៍'){
			$post_link = 'https://www.postkhmer.com'.$post_link;
		}

		

		$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
		$posts = get_posts($args);
		
		
		if (count($posts) < 1){
			
			$client = new Client();
			$crawler = $client->request('GET', $post_link);
			
			$crawler->filter($GLOBALS['crawl_link']['title_filter'])->each(function ($node) {
				$GLOBALS['my_post']['post_title'] = remove_emoji($node->text());

			});


			if (count($GLOBALS['my_post']['post_category']) < 1){
				$GLOBALS['my_post']['post_category'][] = $GLOBALS['crawl_link']['post_category'];
			}

			$GLOBALS['post_content'] = array();
			
		
			$crawler->filter($GLOBALS['crawl_link']['content_filter'])->each(function ($node) {
				$html = html_entity_decode($node->html());
				
				if (strpos($html, '</script>') < 1){
					if ($node->text() != ''){
						$text = remove_emoji($node->text());
						
						
						$GLOBALS['post_content'] = $text;
						
					}
				}
				
			});


		

			$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
			
			
			
			
			
			$GLOBALS['my_post']['post_content'] = $post_content;

			$GLOBALS['my_post']['post_status'] = 'publish';
			$GLOBALS['my_post']['post_author'] = 1;
			
			$GLOBALS['my_post']['tags_input'] = $GLOBALS['crawl_link']['tag_input'];
			
			remove_all_filters("content_save_pre");

			$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );
			// add_post_meta
			update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
			

			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['feature_image_filter'])->eq(0);
				$image = $image->attr('src');
				Generate_Featured_Image($image, $GLOBALS['post_id']);
			} catch (Exception $e){

			}




			$branch_key = 'key_live_lkQmfrUnHRzhvvzi3I4rWnkfsBdMXBf6'; // your branch key.
			$branch_secret = 'secret_live_eBwbU2M81fS5gnIGGyt2X7uhfOF6KNqD';
			$ch = curl_init('https://api.branch.io/v1/url');

			$url = get_permalink($GLOBALS['post_id'], true);

			$payload = [
			'branch_key' => $branch_key,
		    'branch_secret' => $branch_secret,
		    'campaign' => 'Khmer News Live',
		    'channel' => "Facebook",
		    'type' => '2',
		    'data' => [
		        '$desktop_url' => $url,
		        '$ios_url' => $url,
		        '$ipad_url' => $url,
		        '$android_url' =>  $url,
		    	'$og_image_url' => get_the_post_thumbnail_url($GLOBALS['post_id']),
		    	'$og_title' => $GLOBALS['my_post']['post_title'],
		    	'photo_id' => get_post_thumbnail_id($GLOBALS['post_id']),
		    	'$og_app_id' => '1905620589497116',
		     	'$og_title' => $GLOBALS['my_post']['post_title'],
		     	'$marketing_title' => $GLOBALS['my_post']['post_title']
		    	]
		    ];

		    

			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload));
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			# Return response instead of printing.
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			# Send request.
			$result = curl_exec($ch);

			$app_link = json_decode($result);

			update_post_meta($GLOBALS['post_id'], 'app_link',  $app_link->url);

			



		}
	}

	
}
curl_close($ch);





function test_print($item, $key)
{
    echo "$key holds $item\n";
}

function array_flatten($array) { 
  if (!is_array($array)) { 
    return false; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
}

function remove_emoji($text){
	return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
}