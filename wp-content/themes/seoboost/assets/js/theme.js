jQuery(function($) {
	
	'use strict';
	    
    
	/*	var m = new Masonry($('.masonry-wrap').get()[0], {
        itemSelector: ".masonry"
    });
		*/
	var $grid = $('.masonry-wrap').masonry({
  		itemSelector: '.masonry', // use a separate class for itemSelector, other than .col-
  		percentPosition: true
	});
	

// trigger after images loaded
$grid.imagesLoaded( function() {
  $grid.masonry();
});

// trigger on window load
$( window ).load( function() {
  $grid.masonry();
});


	
	var dd = $('.news-ticker').easyTicker({
		direction: 'up',
		easing: 'easeInOutBack',
		speed: 'slow',
		interval: 2000,
		height: 'auto',
		visible: 1,
		mousePause: 0,
		controls: {
			up: '.up',
			down: '.down',
			toggle: '.toggle',
			stopText: 'Stop !!!'
		}
	}).data('easyTicker');
	
	$("#search-icon").click(function () {
	  $('#search-popup').addClass('popup-box-on');
	  return false;
	});
	  
	$("#search-popup .close").click(function () {
		$('#search-popup').removeClass('popup-box-on');
	});
	
	$("#mobile-search").click(function () {
	  $('#mobile-search-popup').addClass('popup-box');
	  return false;
	});
	  
	$("#close-icon").click(function () {
		$('#mobile-search-popup').removeClass('popup-box');
	});
	
	if( $('.seoboost-block-post').length ){
		$( ".seoboost-block-post" ).each(function() {
			
			var banner_height = 1;
			var current = this;
			
			banner_height = $(current).find('.banner-grid-parent').height();
			if( banner_height == 0 ){ banner_height = 1; }
			if( $(current).find('.banner-grid-100x100').length ) banner_height = 500;
			var div_val = 1100 / banner_height;
					
			if( $( window ).width() >= 768 ) {
				seoboost_banner_resize(div_val, current);
			}else{
				$(current).find('.banner-grid-parent').css({height : 'auto' });
			}
			
			$( window ).resize(function() {
				if( $( window ).width() >= 768 ) {
					seoboost_banner_resize(div_val, current);	
				}else{
					$(current).find('.banner-grid-parent').css({height : 'auto' });
				}
			});
		});
	}
	
	$('.main-menu').clone().appendTo('.mobile-menu');
	$( ".menu-icon" ).on( "click", function() {
		$(".mobile-menu").slideToggle();
	});	
	
	$('.mobile-menu').find('.menu-item-has-children').append('<span class="zmm-dropdown-toggle fa fa-plus"></span>');
	$( ".mobile-menu .main-menu" ).find('.sub-menu').slideToggle();
	
	//dropdown toggle
	$( ".zmm-dropdown-toggle" ).on( "click", function() {
		var parent = $( this ).parent('li').children('.sub-menu');
		$( this ).parent('li').children('.sub-menu').slideToggle();
		$( this ).toggleClass('fa-minus');
		if( $( parent ).find('.sub-menu').length ){
			$( parent ).find('.sub-menu').slideUp();
			$( parent ).find('.zmm-dropdown-toggle').removeClass('fa-minus');
		}
	});
	
	$('.mobile-menu-wrap').find('.mobile-menu').append('<div class="menu-close"><i class="fa fa-times"></i></div>');
	$('.mobile-menu-wrap .main-menu').before($('.menu-close'));
	
	$('.mobile-menu-wrap .menu-close').on("click", function() { $(".mobile-menu").removeAttr("style") } );
	
	if ($(window).width() <= 1024) {
		
		//$( "body" ).addClass( "zmm-open" );
		if($('.menu-icon').on('click', function () {
			$( "body" ).addClass( "zmm-open" ); })
		);
		if($('.menu-close').on('click', function(){
			$( "body" ).removeClass( "zmm-open" ); })
		);
			
	}
	
	/* Banner image to background */
	$( ".banner-grid-item" ).each(function( index ) {
		var cur = $(this);
		var img_url = cur.attr("data-url");
		cur.css({ 'background-image' : 'url('+ img_url +')' });
	});
	
	/* Single post drop cap class */
	if( $( ".single-post").length ){
		$( ".single-post .entry-content p" ).first().addClass( "dropcap" );
	}
	
	function seoboost_banner_resize(div_val, current){
		var banner_width = $(current).width();
		banner_width = parseInt( parseInt( banner_width ) / div_val );
		$(current).find('.banner-grid-parent').css({height : banner_width + 'px' });
	}
		
});

