<?php

define( 'WP_USE_THEMES', false ); 
require( '/var/www/khmernewslive/wp-load.php' );
require_once('/var/www/khmernewslive/wp-admin/includes/media.php');
require_once('/var/www/khmernewslive/wp-admin/includes/file.php');
require_once('/var/www/khmernewslive/wp-admin/includes/image.php');

require_once "vendor/autoload.php";


use Stichoza\GoogleTranslate\TranslateClient;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

$client = new Client();
$guzzleClient = new GuzzleClient(array(
	'timeout' => 250,
));
$client->setClient($guzzleClient);



$proxies = explode("\n", file_get_contents('/var/www/khmernewslive/webhook/proxies.txt'));
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
	array('id'=>4, 'keywords' => array('សុខភាព', 'ចែកចាយ','មេរៀនជីវិត','សុខភាព និងសម្រស់','កម្រងទេសភាព')),
	array('id'=>16, 'keywords' => array('កីឡា')),
	array('id'=>17, 'keywords' => array('បច្ចេកវិទ្យា'))
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
    // echo strpos($file, 'http');
	if (strpos($file, 'http') > -1){
		$file_array['tmp_name'] = download_url( $file );	
	}
	else{
		$file_array['tmp_name'] = download_url( 'http:'.$file );
	}

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



$GLOBALS['crawl_links'] =  array(
	array(
		'link' => 'http://www.topnews4khmer.com/author/sothea',
		'href_filter' => 'h3.entry-title > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.entry-content.mh-clearfix > p',
		'image_filter' => 'div.entry-content.mh-clearfix > p > img',
		'iframe_filter' => 'div.entry-content.mh-clearfix > p > iframe',
		'tag_input' => array('TopNews4Khmer'),
		'feature_image_filter' => 'div.entry-content.mh-clearfix > figure.entry-thumbnail > img'
	),
	array(
		'link' => 'http://www.topnews4khmer.com/author/soro',
		'href_filter' => 'h3.entry-title > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.entry-content.mh-clearfix > p',
		'image_filter' => 'div.entry-content.mh-clearfix > p > img',
		'iframe_filter' => 'div.entry-content.mh-clearfix > p > iframe',
		'tag_input' => array('TopNews4Khmer'),
		'feature_image_filter' => 'div.entry-content.mh-clearfix > figure.entry-thumbnail > img'
	),
	array(
		'link' => 'http://www.topnews4khmer.com/author/chanthorn',
		'href_filter' => 'h3.entry-title > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.entry-content.mh-clearfix > p',
		'image_filter' => 'div.entry-content.mh-clearfix > p > img',
		'iframe_filter' => 'div.entry-content.mh-clearfix > p > iframe',
		'tag_input' => array('TopNews4Khmer'),
		'feature_image_filter' => 'div.entry-content.mh-clearfix > figure.entry-thumbnail > img'
	),
	array(
		'link' => 'http://www.topnews4khmer.com/author/sreydy',
		'href_filter' => 'h3.entry-title > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.entry-content.mh-clearfix > p',
		'image_filter' => 'div.entry-content.mh-clearfix > p > img',
		'iframe_filter' => 'div.entry-content.mh-clearfix > p > iframe',
		'tag_input' => array('TopNews4Khmer'),
		'feature_image_filter' => 'div.entry-content.mh-clearfix > figure.entry-thumbnail > img'
	),
	array(
		'link' => 'http://cambopost.net/',
		'href_filter' => 'h2.post-box-title > a',
		'title_filter' => 'h1.name.post-title.entry-title > span',
		'post_category' => 3,
		'content_filter' => 'div.entry > p',
		'image_filter' => 'div.entry > p > img',
		'iframe_filter' => 'div.entry > p > iframe',
		'tag_input' => array('Cambopost'),
		'feature_image_filter' => 'div.entry > p > img'
	),
	array(
		'link' => 'https://camnews.asia/archives/category/news/local-news',
		'href_filter' => 'h2.post-title.entry-title > a',
		'title_filter' => 'h1.single-title.post-title.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.single-post-body > div.single-entry > p',
		'image_filter' => 'div.single-post-body > div.single-entry > p > img',
		'iframe_filter' => 'div.single-post-body > div.single-entry > div.fb-video',
		'tag_input' => array('Cambopost'),
		'feature_image_filter' => 'div.entry > p > img'
	),

);



for ($i = 0; $i < count($GLOBALS['crawl_links']); $i ++){

	// TopNews4Khmer.com 
	$crawler = $client->request('GET', $GLOBALS['crawl_links'][$i]['link']);

	$links = $crawler->filter($GLOBALS['crawl_links'][$i]['href_filter'])->each(function($node_link){

		$GLOBALS['my_post'] = array();
		$GLOBALS['my_post']['post_category'] = array();

		$post_link = $node_link->attr('href');

		$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
		$posts = get_posts($args);
		
		$GLOBALS['tmp_content'] = array();
		
		if (count($posts) < 1){
			
			$client = new Client();
			$crawler = $client->request('GET', $post_link);
			

			$crawler->filter($GLOBALS['crawl_links'][$i]['title_filter'])->each(function ($node) {
				
				$GLOBALS['my_post']['post_title'] = $node->text();
			});

			$crawler->filter('p.mh-meta.entry-meta > span.entry-meta-categories > a')->each(function ($node) {
				$post_cat = $node->text();
				foreach($GLOBALS['cats'] as $cat){
					foreach($cat['keywords'] as $keyword){
						if ($keyword == $post_cat){
							$GLOBALS['my_post']['post_category'][] = $cat['id'];
						}
					}
				}
			});

			if (count($GLOBALS['my_post']['post_category']) < 1){
				$GLOBALS['my_post']['post_category'][] = $GLOBALS['crawl_links'][$i]['post_category'];
			}

			$GLOBALS['post_content'] = array();
			$GLOBALS['_post_content'] = array();
			$crawler->filter($GLOBALS['crawl_links'][$i]['content_filter'])->each(function ($node) {
				$html = html_entity_decode($node->html());

				if (strpos($html, '</script>') < 1){
					
					if ($node->text() != ''){
						
						$text = remove_emoji($node->text());
						$GLOBALS['_post_content'][] = $text;	
						$GLOBALS['tmp_content'][] = $text;	

					}
					
				}
				
			});

			
			$tr_km_en = new TranslateClient('km', 'en', [
				'timeout'=>2000,
				'allow_redirects' => false,
				'proxy' => [
					'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
					'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
				],
				'headers' => [
					'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
				],
				'verify' => false
			]);

			$tr_en_km = new TranslateClient('en', 'km', [
				'timeout'=>2000,
				'allow_redirects' => false,
				'proxy' => [
					'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
					'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
				],
				'headers' => [
					'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
				],
				'verify' => false
			]);

			$post_content = implode($GLOBALS['tmp_content']);
			$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
			$post_content = $tr_km_en->translate($post_content);
			$post_content = $tr_en_km->translate($post_content);
			$GLOBALS['post_content'][] = $post_content;
			
			try{
				$image = $crawler->filter($GLOBALS['crawl_links'][$i]['image_filter'])->each(function($node){
					$image = $node->attr('src');
					$image = Generate_Image($image);
					if (is_string($image)){
						$GLOBALS['post_content'][] = $image;
						$GLOBALS['_post_content'][] = $image;
					}	
				});
				
			} catch (Exception $e){

			}



			try{
				$fb_video = $crawler->filter($GLOBALS['crawl_links'][$i]['iframe_filter'])->first();
				$fb_video = $fb_video->attr('src');

				$str = '<iframe src="'.$fb_video.'" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';

				$GLOBALS['post_content'][] = $str;
				$GLOBALS['_post_content'][] = $str;

			}catch(Exception $e){

			}

			$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
			$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
			
			$GLOBALS['my_post']['post_content'] = $post_content;

			$GLOBALS['my_post']['post_status'] = 'draft';
			$GLOBALS['my_post']['post_author'] = 1;
			$GLOBALS['my_post']['tags_input'] = array('TopNews4Khmer');
			
			remove_all_filters("content_save_pre");

			$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );
			// add_post_meta
			update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
			update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);

			try{
				$image = $crawler->filter($GLOBALS['crawl_links'][$i]['feature_image_filter'])->eq(0);
				$image = $image->attr('src');
				Generate_Featured_Image($image, $GLOBALS['post_id']);
			} catch (Exception $e){

			}
		}
	});
}




//cambopost.net
$crawler = $client->request('GET', 'http://cambopost.net/');

$links = $crawler->filter('h2.post-box-title > a')->each(function($node_link){

	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();

	$post_link = $node_link->attr('href');

	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();
	
	if (count($posts) < 1){
		

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('h1.name.post-title.entry-title > span')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
		});

		$crawler->filter('p.post-meta > span.post-cats > a')->each(function ($node) {
			$post_cat = $node->text();
			foreach($GLOBALS['cats'] as $cat){
				foreach($cat['keywords'] as $keyword){
					if ($keyword == $post_cat){
						$GLOBALS['my_post']['post_category'][] = $cat['id'];
					}
				}
			}
		});

		if (count($GLOBALS['my_post']['post_category']) < 1){
			$GLOBALS['my_post']['post_category'][] = 3;
		}

		
		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.entry > p')->each(function ($node) {
			
			
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){
				
				if ($node->text() != ''){
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;	
				}
				
			}

			
		});


		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);


		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;

		try{
			$image = $crawler->filter('div.entry > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;
					$GLOBALS['_post_content'][] = $image;		
				}	
			});
			
		} catch (Exception $e){

		}

		try{
			$fb_video = $crawler->filter('div.entry > p > iframe')->first();
			$fb_video = $fb_video->attr('src');

			$str = '<iframe src="'.$fb_video.'" width="720" height="720" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}



		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		
		$GLOBALS['my_post']['post_content'] = $post_content;

		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('cambopost');
		remove_all_filters("content_save_pre");
		
		$GLOBALS['post_id'] = wp_insert_post( $GLOBALS['my_post'] );
		
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);

		try{
			$image = $crawler->filter('div.entry > p > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch(Exception $e) { 

		}


		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);
	}
});

echo '** DONE cambopost.net ** - ';


// camnews.asia
$crawler = $client->request('GET', 'https://camnews.asia/archives/category/news/local-news');

$links = $crawler->filter('h2.post-title.entry-title > a')->each(function($node_link){

	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();

	$post_link = $node_link->attr('href');


	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();
	
	if (count($posts) < 1){

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('h1.single-title.post-title.entry-title')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
			
		});



		$GLOBALS['my_post']['post_category'][] = 2;
		
		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.single-post-body > div.single-entry > p')->each(function ($node) {
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){
				

				if ($node->text() != ''){
					
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;
					
				}
				
			}
			
		});


		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;

		try{
			$image = $crawler->filter('div.single-post-body > div.single-entry > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;		
					$GLOBALS['_post_content'][] = $image;
				}	
			});
			
		} catch (Exception $e){

		}


		try{
			$fb_video = $crawler->filter('div.single-post-body > div.single-entry > div.fb-video')->first();
			$fb_video = $fb_video->attr('data-href');

			$str = '<iframe src="https://www.facebook.com/plugins/video.php?href='.urlencode($fb_video).'&width=720&show_text=false&appId=1905620589497116&height=720" width="720" height="720" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}
		
		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;
		
		
		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('camnews');
		remove_all_filters("content_save_pre");
		$GLOBALS['post_id'] = wp_insert_post( $GLOBALS['my_post'] );
		
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);
		try{
			$image = $crawler->filter('div.post-thumb > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch(Exception $e) { 

		}

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);
		
	}
});

echo '** DONE camnews.asia ** - ';

// kamsantoday.com/
$crawler = $client->request('GET', 'https://kamsantoday.com');

$links = $crawler->filter('h3.entry-title.td-module-title > a')->each(function($node_link){

	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();

	$post_link = $node_link->attr('href');


	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();
	
	if (count($posts) < 1){

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('h1.entry-title')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
			

		});



		$GLOBALS['my_post']['post_category'][] = 3;
		
		

		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.td-post-content.td-pb-padding-side > p')->each(function ($node) {
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){
				

				if ($node->text() != ''){
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;	
				}
				
			}
			
		});



		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;

		try{
			$image = $crawler->filter('div.td-post-content.td-pb-padding-side > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;	
					$GLOBALS['_post_content'][] = $image;	
				}	
			});
			
		} catch (Exception $e){

		}


		try{
			$fb_video = $crawler->filter('div.td-post-content.td-pb-padding-side > p > iframe')->first();
			$fb_video = $fb_video->attr('src');

			$str = '<iframe src="'.$fb_video.'" width="720" height="720" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}

		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;
		
		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('kamsantoday');
		remove_all_filters("content_save_pre");
		$GLOBALS['post_id'] = wp_insert_post( $GLOBALS['my_post'] );
		
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);
		try{
			$image = $crawler->filter('div.td-post-featured-image > a > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch(Exception $e) { 

		}

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);
		
	}
	// exit();
});


echo '** DONE kamsantoday.com ** - ';

// www.poromean.com/
$crawler = $client->request('GET', 'https://www.poromean.com/archives/author/dara');

$links = $crawler->filter('div.td-module-thumb > a')->each(function($node_link){

	// print $node_link->html();

	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();


	$post_link = $node_link->attr('href');



	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);

	$GLOBALS['tmp_content'] = array();
	
	if (count($posts) < 1){

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('h1.entry-title')->each(function ($node) {
			$GLOBALS['my_post']['post_title'] = $node->text();
			
		});


		$GLOBALS['my_post']['post_category'][] = 2;
		
		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.td-post-content > p')->each(function ($node) {
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){
				
				if ($node->text() != ''){

					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;	

				}
			}
			
		});



		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;

		try{
			$image = $crawler->filter('div.td-post-content > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;		
					$GLOBALS['_post_content'][] = $image;
				}	
			});
			
		} catch (Exception $e){

		}

		try{
			$fb_video = $crawler->filter('div.td-post-content > div.fb-video')->first();
			$fb_video = $fb_video->attr('data-href');

			$str = '<iframe src="https://www.facebook.com/plugins/video.php?href='.urlencode($fb_video).'&width=720&show_text=false&appId=1905620589497116&height=720" width="720" height="720" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}



		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;
		

		
		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('poromean');
		remove_all_filters("content_save_pre");
		$GLOBALS['post_id'] = wp_insert_post( $GLOBALS['my_post'] );
		
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);
		try{
			$image = $crawler->filter('div.td-post-featured-image > a > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch(Exception $e) { 

		}

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);
		
	}
});

echo '** DONE poromean.com ** - ';


// kbn.news/
$crawler = $client->request('GET', 'https://kbn.news');

$links = $crawler->filter('div.td-block-span6 div.item-detail h3.entry-title.td-module-title > a')->each(function($node_link){

	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();


	$post_link = $node_link->attr('href');


	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();
	

	if (count($posts) < 1){

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('h1.entry-title')->each(function ($node) {
			$GLOBALS['my_post']['post_title'] = $node->text();
			
		});



		$GLOBALS['my_post']['post_category'][] = 3;
		
		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.td-post-content > p')->each(function ($node) {
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){


				if ($node->text() != ''){
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;	
				}
			}
			
		});

		

		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;


		try{
			$image = $crawler->filter('div.td-post-content > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['_post_content'][] = $image;
					$GLOBALS['post_content'][] = $image;		
				}	
			});
			
		} catch (Exception $e){

		}



		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		

		$GLOBALS['my_post']['post_content'] = $post_content;

		
		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('kbn.news');
		remove_all_filters("content_save_pre");
		$GLOBALS['post_id'] = wp_insert_post( $GLOBALS['my_post'] );
		
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);

		try{
			$image = $crawler->filter('div.td-post-featured-image > a > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch(Exception $e) { 

		}

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);
		
	}
});			

echo '** DONE kbn.news ** - ';


// http://all111.com/ 
$crawler = $client->request('GET', 'http://all111.com/');

$links = $crawler->filter('div.td_module_1.td_module_wrap.td-animation-stack >h3.entry-title.td-module-title > a')->each(function($node_link){


	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();

	$post_link = $node_link->attr('href');

	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();
	
	if (count($posts) < 1){
		

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('h1.entry-title')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
		});

		$crawler->filter('p.mh-meta.entry-meta > span.entry-meta-categories > a')->each(function ($node) {
			$post_cat = $node->text();
			foreach($GLOBALS['cats'] as $cat){
				foreach($cat['keywords'] as $keyword){
					if ($keyword == $post_cat){
						$GLOBALS['my_post']['post_category'][] = $cat['id'];
					}
				}
			}
		});

		if (count($GLOBALS['my_post']['post_category']) < 1){
			$GLOBALS['my_post']['post_category'][] = 2;
		}

		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.td-post-content > p')->each(function ($node) {
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){
				

				if ($node->text() != ''){
					
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;	
				}
				
			}

			
		});

		
		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;

		try{
			$image = $crawler->filter('div.td-post-content > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;	
					$GLOBALS['_post_content'][] = $image;	
				}	
			});
			
		} catch (Exception $e){

		}



		try{
			$yt_video = $crawler->filter('div.td-post-content > p > iframe')->first();
			$yt_video = $yt_video->attr('src');

			$str = '<iframe src="'.$yt_video.'" width="720" height="720" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;

		}catch(Exception $e){

		}

		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;

		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('all111');
		
		
		remove_all_filters("content_save_pre");

		$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);
		try{
			$image = $crawler->filter('div.td-post-featured-image > a > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch (Exception $e){

		}
		

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);
		
	}
	
});

echo '** DONE all111.com ** - ';




// http://khmerload.com/ 
$crawler = $client->request('GET', 'https://www.khmerload.com/category/social');

$links = $crawler->filter('div.homepage-zone-4 article.article-small > div.content > a')->each(function($node_link){


	$GLOBALS['my_post'] = array();
	$GLOBALS['my_post']['post_category'] = array();

	$post_link = 'https://www.khmerload.com'.$node_link->attr('href');

	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);
	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();

	if (count($posts) < 1){
		

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('div.article-header > h1')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
		});

		$crawler->filter('p.mh-meta.entry-meta > span.entry-meta-categories > a')->each(function ($node) {
			$post_cat = $node->text();
			foreach($GLOBALS['cats'] as $cat){
				foreach($cat['keywords'] as $keyword){
					if ($keyword == $post_cat){
						$GLOBALS['my_post']['post_category'][] = $cat['id'];
					}
				}
			}
		});

		if (count($GLOBALS['my_post']['post_category']) < 1){
			$GLOBALS['my_post']['post_category'][] = 1;
		}

		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.article-content > div > p')->each(function ($node) {
			$html = html_entity_decode($node->html());

			if (strpos($html, '</script>') < 1){
				

				if ($node->text() != ''){
					
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;
				}
				
			}

			
		});

		
		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;


		try{
			$image = $crawler->filter('div.article-content > div > figure > img')->each(function($node){
				$image = 'https:'.$node->attr('src');
				
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;	
					$GLOBALS['_post_content'][] = $image;	
				}
				
			});
			
		} catch (Exception $e){

		}



		try{
			$yt_video = $crawler->filter('div.article-content > div > figure > iframe')->first();
			$yt_video = $yt_video->attr('src');
			echo $yt_video;
			$str = '<iframe width="600" height="450" src="'.$yt_video.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>';
			
			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}

		

		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;

		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('Khmer Load');
		
		remove_all_filters("content_save_pre");

		$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );
		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);
		try{
			$image = $crawler->filter('div.article-content > div > figure > img')->eq(0);
			$image = 'https:'.$image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch (Exception $e){

		}
		

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);

	}
	
	
});

echo '** DONE khmerload.com ** - ';




$crawler = $client->request('GET', 'https://kohsantepheapdaily.com.kh/category/local');

$links = $crawler->filter('div.col-md-3.col-sm-12.col-xs-12 >article > h3 > a')->each(function($node_link){


	$GLOBALS['my_post'] = array();

	$GLOBALS['my_post']['post_category'] = array();

	$post_link = $node_link->attr('href');

	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);

	$posts = get_posts($args);
	
	$GLOBALS['tmp_content'] = array();
	
	
	if (count($posts) < 1){
		

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('.row > .col-lg-8.col-md-8 > h1')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
		});

		$GLOBALS['my_post']['post_category'][] = 2;
		

		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.col-lg-8.col-md-8 > .content-text > p')->each(function ($node) {
			$html = html_entity_decode($node->html());


			if (strpos($html, '</script>') < 1){

				if ($node->text() != ''){
					$text = remove_emoji($node->text());
					$GLOBALS['_post_content'][] = $text;	
					$GLOBALS['tmp_content'][] = $text;
				}
				
			}
			
		});

		$tr_km_en = new TranslateClient('km', 'en', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$tr_en_km = new TranslateClient('en', 'km', [
			'timeout'=>2000,
			'allow_redirects' => false,
			'proxy' => [
				'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
				'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
			],
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
			],
			'verify' => false
		]);

		$post_content = implode($GLOBALS['tmp_content']);
		$post_content = mb_substr($post_content, 0, 4700, "UTF-8");
		$post_content = $tr_km_en->translate($post_content);
		$post_content = $tr_en_km->translate($post_content);
		$GLOBALS['post_content'][] = $post_content;
		


		try{
			echo " Generating Images...";
			$image = $crawler->filter('div.col-lg-8.col-md-8 > .content-text > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;	
					$GLOBALS['_post_content'][] = $image;	
				}	
			});
			
		} catch (Exception $e){

		}



		try{
			$yt_video = $crawler->filter('div.col-lg-8.col-md-8 > .content-text > p > iframe')->first();
			$yt_video = $yt_video->attr('src');

			$str = '<iframe width="740" height="416" src="'.$yt_video.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}

		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;

		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('Kohsantepheap');
		
		remove_all_filters("content_save_pre");
		echo " Saving...";
		$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );

		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);


		try{
			$image = $crawler->filter('div.col-lg-8.col-md-8 > .content-text > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch (Exception $e){

		}
		
		

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);

	}
	
	
});



//postkhnews
$crawler = $client->request('GET', 'http://postkhnews.com/archives/category/ពត៏មានសង្គម');


$links = $crawler->filter('div.summary > h4.news-title > a ')->each(function($node_link){
	

	$GLOBALS['my_post'] = array();

	$GLOBALS['my_post']['post_category'] = array();

	$post_link = $node_link->attr('href');

	$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);

	$posts = get_posts($args);
	
	
	
	if (count($posts) < 1){
		

		$client = new Client();
		$crawler = $client->request('GET', $post_link);
		

		$crawler->filter('.page-title > h1')->each(function ($node) {
			
			$GLOBALS['my_post']['post_title'] = $node->text();
		});

		$GLOBALS['my_post']['post_category'][] = 2;
		

		$GLOBALS['post_content'] = array();
		$GLOBALS['_post_content'] = array();
		$crawler->filter('div.post-content > article > p')->each(function ($node) {
			$html = html_entity_decode($node->html());


			if (strpos($html, '</script>') < 1){
				

				if ($node->text() != ''){
					echo " Translating...";
					
					$tr_km_en = new TranslateClient('km', 'en', [
						'timeout'=>2000,
						'allow_redirects' => false,
						'proxy' => [
							'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
							'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
						],
						'headers' => [
							'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
						],
						'verify' => false
					]);
					
					$tr_en_km = new TranslateClient('en', 'km', [
						'timeout'=>2000,
						'allow_redirects' => false,
						'proxy' => [
							'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
							'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
						],
						'headers' => [
							'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
						],
						'verify' => false
					]);
					
					$GLOBALS['_post_content'][] = remove_emoji($node->text());
					$text = $tr_km_en->translate(remove_emoji($node->text()));
					
					$text = $tr_en_km->translate($text);
					$GLOBALS['post_content'][] = remove_emoji($text);	
				}
				
			}
			
		});

		
		


		try{
			echo " Generating Images...";
			$image = $crawler->filter('div.post-content > article > p > img')->each(function($node){
				$image = $node->attr('src');
				$image = Generate_Image($image);
				if (is_string($image)){
					$GLOBALS['post_content'][] = $image;	
					$GLOBALS['_post_content'][] = $image;	
				}	
			});
			
		} catch (Exception $e){

		}



		try{
			$video = $crawler->filter('div.post-content > article > p > iframe')->first();
			$yt_video = $video->attr('src');

			$str = '<iframe width="740" height="416" src="'.$yt_video.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>';

			$GLOBALS['post_content'][] = $str;
			$GLOBALS['_post_content'][] = $str;
		}catch(Exception $e){

		}

		$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
		$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
		$GLOBALS['my_post']['post_content'] = $post_content;

		$GLOBALS['my_post']['post_status'] = 'draft';
		$GLOBALS['my_post']['post_author'] = 1;
		$GLOBALS['my_post']['tags_input'] = array('Postkhnews');
		
		remove_all_filters("content_save_pre");
		echo " Saving...";
		$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );

		update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
		update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);


		try{
			$image = $crawler->filter('div.post-content > figure.feature-image > img')->eq(0);
			$image = $image->attr('src');
			Generate_Featured_Image($image, $GLOBALS['post_id']);
		} catch (Exception $e){

		}
		
		

		// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);

	}
	
	
});


echo " Done Post Khmer News";

$sabbay_links = [
	'http://news.sabay.com.kh/index.php/article/tag/video-clip',
	'http://news.sabay.com.kh/index.php/topics/entertainment',
	'http://news.sabay.com.kh/index.php/topics/technology',
	'http://news.sabay.com.kh/index.php/topics/life',
	'http://news.sabay.com.kh/index.php/topics/sport'
];


for ($i = 0; $i < count($sabbay_links); $i ++){

	//postkhnews
	$crawler = $client->request('GET', $sabbay_links[$i]);

	$links = $crawler->filter('#posts_list a')->each(function($node_link){


		$GLOBALS['my_post'] = array();

		$GLOBALS['my_post']['post_category'] = array();

		$post_link = $node_link->attr('href');

		

		$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);

		$posts = get_posts($args);
		
		
		
		if (count($posts) < 1){
			

			$client = new Client();
			$crawler = $client->request('GET', $post_link);


			$crawler->filter('.title.detail > p')->each(function ($node) {
				$GLOBALS['my_post']['post_title'] = $node->text();
			});

			

			if ($i == 0 | $i == 1){
				$GLOBALS['my_post']['post_category'][] = 3;
			}
			elseif ($i == 2){
				$GLOBALS['my_post']['post_category'][] = 17;
			}
			elseif ($i == 3){
				$GLOBALS['my_post']['post_category'][] = 4;	
			}
			elseif ($i == 4){
				$GLOBALS['my_post']['post_category'][] = 16;	
			}

			

			$GLOBALS['post_content'] = array();
			$GLOBALS['_post_content'] = array();

			$crawler->filter('div.post_content > .detail > p')->each(function ($node) {
				$html = html_entity_decode($node->html());

				if (strpos($html, '</script>') < 1){
					
					if ($node->text() != ''){
						echo " Translating...";
						
						$tr_km_en = new TranslateClient('km', 'en', [
							'timeout'=>2000,
							'allow_redirects' => false,
							'proxy' => [
								'http'  => 'socks5://'.$GLOBALS['arr_proxies'][2],
								'https' => 'socks5://'.$GLOBALS['arr_proxies'][2]
							],
							'headers' => [
								'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
							],
							'verify' => false
						]);
						
						$tr_en_km = new TranslateClient('en', 'km', [
							'timeout'=>2000,
							'allow_redirects' => false,
							'proxy' => [
								'http'  => 'socks5://'.$GLOBALS['arr_proxies'][3],
								'https' => 'socks5://'.$GLOBALS['arr_proxies'][3]
							],
							'headers' => [
								'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
							],
							'verify' => false
						]);
						
						$GLOBALS['_post_content'][] = remove_emoji($node->text());
						// $GLOBALS['post_content'][] = remove_emoji($node->text());
						$text = $tr_km_en->translate(remove_emoji($node->text()));
						
						$text = $tr_en_km->translate($text);
						$GLOBALS['post_content'][] = remove_emoji($text);	
					}
					
				}
				
			});

			
			


			try{
				echo " Generating Images...";
				$image = $crawler->filter('div.post_content > .detail > .content-grp-img > img')->each(function($node){
					$image = $node->attr('src');
					echo $image . '---';
					$image = Generate_Image($image);
					if (is_string($image)){
						$GLOBALS['post_content'][] = $image;	
						$GLOBALS['_post_content'][] = $image;	
					}	
				});
				
			} catch (Exception $e){

			}



			try{
				$video = $crawler->filter('div.post_content > .detail > p > iframe')->each(function ($node) {

					$video = $node->attr('src');

					$str = '<iframe width="740" height="416" src="'.$video.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>';

					$GLOBALS['post_content'][] = $str;
					$GLOBALS['_post_content'][] = $str;
				});
				
			}catch(Exception $e){

			}

			$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
			$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
			$GLOBALS['my_post']['post_content'] = $post_content;

			$GLOBALS['my_post']['post_status'] = 'draft';
			$GLOBALS['my_post']['post_author'] = 1;
			$GLOBALS['my_post']['tags_input'] = array('Sabbay News');
			
			remove_all_filters("content_save_pre");
			
			$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );

			update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
			update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);

			try{
				$image = $crawler->filter('div.post_content > .detail > .content-grp-img > img')->eq(0);
				$image = $image->attr('src');
				Generate_Featured_Image($image, $GLOBALS['post_id']);
			} catch (Exception $e){

			}
			
			
			// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);

		}
		
		
	});
}


echo "Done Sabbay News";



$kanha_links = [
	'http://kanha.sabay.com.kh/topics/news',
	'http://kanha.sabay.com.kh/topics/health',
	'http://kanha.sabay.com.kh/topics/fashion-beauty',
	'http://kanha.sabay.com.kh/topics/love-wedding-family',
	'http://kanha.sabay.com.kh/topics/life-work'
];


for ($i = 0; $i < count($kanha_links); $i ++){

	//postkhnews
	$crawler = $client->request('GET', $kanha_links[$i]);

	$links = $crawler->filter('#posts_list a')->each(function($node_link){


		$GLOBALS['my_post'] = array();

		$GLOBALS['my_post']['post_category'] = array();

		$post_link = $node_link->attr('href');

		

		$args = array("post_status" => array('publish', 'draft'), "meta_key" => "source_link", "meta_value" =>$post_link);

		$posts = get_posts($args);
		
		
		
		if (count($posts) < 1){
			

			$client = new Client();
			$crawler = $client->request('GET', $post_link);


			$crawler->filter('.title.detail > p')->each(function ($node) {
				$GLOBALS['my_post']['post_title'] = $node->text();
			});

			

			if ($i == 0 | $i == 2){
				$GLOBALS['my_post']['post_category'][] = 2;
			}
			elseif ($i == 1){
				$GLOBALS['my_post']['post_category'][] = 4;
			}
			elseif ($i == 3){
				$GLOBALS['my_post']['post_category'][] = 3;	
			}
			elseif ($i == 4){
				$GLOBALS['my_post']['post_category'][] = 4;	
			}

			

			$GLOBALS['post_content'] = array();
			$GLOBALS['_post_content'] = array();

			$crawler->filter('div.post_content > .detail > p')->each(function ($node) {
				$html = html_entity_decode($node->html());


				if (strpos($html, '</script>') < 1){
					

					if ($node->text() != ''){
						echo " Translating...";
						
						$tr_km_en = new TranslateClient('km', 'en', [
							'timeout'=>2000,
							'allow_redirects' => false,
							'proxy' => [
								'http'  => 'socks5://'.$GLOBALS['arr_proxies'][0],
								'https' => 'socks5://'.$GLOBALS['arr_proxies'][0]
							],
							'headers' => [
								'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
							],
							'verify' => false
						]);
						
						$tr_en_km = new TranslateClient('en', 'km', [
							'timeout'=>2000,
							'allow_redirects' => false,
							'proxy' => [
								'http'  => 'socks5://'.$GLOBALS['arr_proxies'][1],
								'https' => 'socks5://'.$GLOBALS['arr_proxies'][1]
							],
							'headers' => [
								'User-Agent' => 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0'
							],
							'verify' => false
						]);
						
						$GLOBALS['_post_content'][] = remove_emoji($node->text());
						// $GLOBALS['post_content'][] = remove_emoji($node->text());
						$text = $tr_km_en->translate(remove_emoji($node->text()));
						
						$text = $tr_en_km->translate($text);
						$GLOBALS['post_content'][] = remove_emoji($text);	
					}
					
				}
				
			});

			
			
			try{
				echo " Generating Images...";
				$image = $crawler->filter('div.post_content > .detail > .content-grp-img > img')->each(function($node){
					$image = $node->attr('src');
					
					$image = Generate_Image($image);
					if (is_string($image)){
						$GLOBALS['post_content'][] = $image;	
						$GLOBALS['_post_content'][] = $image;	
					}	
				});
				
			} catch (Exception $e){

			}



			try{
				$video = $crawler->filter('div.post_content > .detail > p > iframe')->each(function ($node) {

					$video = $node->attr('src');

					$str = '<iframe width="740" height="416" src="'.$video.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>';

					$GLOBALS['post_content'][] = $str;
					$GLOBALS['_post_content'][] = $str;
				});
				
			}catch(Exception $e){

			}

			$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
			$_post_content = "<p>".implode('</p><p>', $GLOBALS['_post_content'])."</p>";
			$GLOBALS['my_post']['post_content'] = $post_content;

			$GLOBALS['my_post']['post_status'] = 'draft';
			$GLOBALS['my_post']['post_author'] = 1;
			$GLOBALS['my_post']['tags_input'] = array('Sabbay News');
			
			remove_all_filters("content_save_pre");
			echo " Saving...";
			$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );

			update_post_meta($GLOBALS['post_id'],'source_link',$post_link);
			update_post_meta($GLOBALS['post_id'],'original_content',$_post_content);

			try{
				$image = $crawler->filter('div.post_content > .detail > .content-grp-img > img')->eq(0);
				$image = $image->attr('src');
				Generate_Featured_Image($image, $GLOBALS['post_id']);
			} catch (Exception $e){

			}
			

			
			// post_to_facebook($GLOBALS['post_id'], $GLOBALS['my_post']['post_title']);

		}
		
		
	});
}


echo "Done Kanha News";

function remove_emoji($text){
	return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
}