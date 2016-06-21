var cssFix = function() {
    var u = navigator.userAgent.toLowerCase(),
    addClass = function(el,val){
        if(!el.className) {
            el.className = val;
        } else {
            var newCl = el.className;
            newCl+=(" "+val);
            el.className = newCl;
        }
    },
    is = function(t){
        return (u.indexOf(t)!=-1)
        };
    addClass(document.getElementsByTagName('html')[0],[
        (!(/opera|webtv/i.test(u))&&/msie (\d)/.test(u))?('ie ie'+RegExp.$1)
        :is('firefox/2')?'gecko ff2'
        :is('firefox/3')?'gecko ff3'
        :is('gecko/')?'gecko'
        :is('opera/9')?'opera opera9':/opera (\d)/.test(u)?'opera opera'+RegExp.$1
        :is('konqueror')?'konqueror'
        :is('safari/')?'webkit safari'
        :is('mozilla/')?'gecko':'',
        (is('x11')||is('linux'))?' linux'
        :is('mac')?' mac'
        :is('win')?' win':''
        ].join(" "));
}();

/* ---------------------------------------------------------------------- */
/*	Include touch device
/* ---------------------------------------------------------------------- */

	(function() {
		
		if(Modernizr.touch) {jQuery('body').addClass('touch');}
		
	})();

	/* end touch device */

/* ---------------------------------------------------------------------- */
/*	Navigation
/* ---------------------------------------------------------------------- */
	
	/* ---------------------------------------------------------------------- */
	/*	Main Navigation
	/* ---------------------------------------------------------------------- */
	
	(function() {
		
		var	arrowimages = {
			down: 'downarrowclass',
			right: 'rightarrowclass'
		};
		var $mainNav    = jQuery('#navigation').find('> ul'),
			optionsList = '<option value="" selected>Navigation</option>';
			  
			var $submenu = $mainNav.find("ul").parent();
			$submenu.each(function (i) {
				var $curobj = jQuery(this);
				 this.istopheader = $curobj.parents("ul").length == 1 ? true : false;
				$curobj.children("a").append('<span class="' + (this.istopheader ? arrowimages.down : arrowimages.right) +'"></span>');
			});
			
		$mainNav.on('mouseenter', 'li', function() {
			var $this    = jQuery(this),
				$subMenu = $this.children('ul');
			if($subMenu.length) $this.addClass('hover');
			$subMenu.hide().stop(true, true).fadeIn(200);
		}).on('mouseleave', 'li', function() {
			jQuery(this).removeClass('hover').children('ul').stop(true, true).fadeOut(50);
		});	
			
		// Navigation Responsive
		
		$mainNav.find('li').each(function() {
			var $this   = jQuery(this),
				$anchor = $this.children('a'),
				depth = $this.parents('ul').length - 1,
				dash  = '';
				
			if(depth) {
				while(depth > 0) {
					dash += '--';
					depth--;
				}
			}
			
			optionsList += '<option value="' + $anchor.attr('href') + '">' + dash + ' ' + $anchor.text() + '</option>';
			
		}).end()
		  .after('<select class="nav-responsive">' + optionsList + '</select>');

		jQuery('.nav-responsive').on('change', function() {
			window.location = jQuery(this).val();
		});
		
	})();

	/* end Main Navigation */

/* ---------------------------------------------------------------------- */
/*	Flex Slider
/* ---------------------------------------------------------------------- */

if (jQuery('#slider').length) {
	jQuery(window).load(function() {
		jQuery('#slider img').css('visibility','visible').fadeIn();
		jQuery('#slider').flexslider({
			directionNav: true,
			controlNav: false
		});
	});
}

/* end Flex Slider */

/* ---------------------------------------------------------------------- */
/*	Fit Videos
/* ---------------------------------------------------------------------- */

(function() {

	jQuery('.container').each(function(){
		var target  = [
			"iframe[src^='http://www.youtube.com']",
			"iframe[src^='http://player.vimeo.com']"
		],
			$allVideos = jQuery(this).find(target.join(','));

		$allVideos.each(function(){
			var $this = jQuery(this);
			if (this.tagName.toLowerCase() == 'embed' && $this.parent('object').length || $this.parent('.liquid-video-wrapper').length) {return;} 
			var height = this.tagName.toLowerCase() == 'object' ? $this.attr('height') : $this.height(),
			aspectRatio = height / $this.width();
			if(!$this.attr('id')){
				var $ID =  Math.floor(Math.random()*9999999);
				$this.attr('id', $ID);
			}
			$this.wrap('<div class="liquid-video-wrapper"></div>').parent('.liquid-video-wrapper').css('padding-top', (aspectRatio * 100)+"%");
			$this.removeAttr('height').removeAttr('width');
		});
	});
})();

/* end Fit Videos */

/* ---------------------------------------------------------------------- */
/*	Load Google Fonts
/* ---------------------------------------------------------------------- */
	
WebFontConfig = {
		google: {families: ['Adamina::latin', 'Alice::latin']}
	  };
	  (function() {
		var wf = document.createElement('script');
		wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
			'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		wf.type = 'text/javascript';
		wf.async = 'true';
		var s = document.getElementsByTagName('body')[0];
		s.appendChild(wf, s);
	  })();

/* end Google Fonts */

/************************************************************************/
/* DOM READY --> Begin													*/
/************************************************************************/

jQuery(document).ready(function(){

	// Autocolumnize script
	if (jQuery(".footer_row").length) {
		jQuery(".footer_row").autoColumn(20, ".widget-container");
	}

	/* ---------------------------------------------------- */
	/*	Min. Height
	/* ---------------------------------------------------- */

	(function() {

		jQuery('section.container').css( 'min-height', jQuery(window).outerHeight(true) - jQuery('#header').outerHeight(true) - jQuery('#footer').outerHeight(true));

	})();

	/* end Min. Height */

	/* ---------------------------------------------------- */
	/*	Content Toggle
	/* ---------------------------------------------------- */

	(function() {

		if(jQuery('.toggle-container').length) {	
			jQuery(".toggle-container").hide(); //Hide (Collapse) the toggle containers on load
			//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
			jQuery(".trigger").click(function(){
				jQuery(this).toggleClass("active").next().slideToggle("slow");
				return false; //Prevent the browser jump to the link anchor
			});
		}
	})();

	/* end Content Toggle */

	/* ---------------------------------------------------------------------- */
	/*	Accordion Content
	/* ---------------------------------------------------------------------- */

	(function() {

		if(jQuery('.acc-container').length) {

			var $container = jQuery('.acc-container'),
				$trigger   = jQuery('.acc-trigger');

			$container.hide();
			$trigger.first().addClass('active').next().show();

			var fullWidth = $container.outerWidth(true);
			$trigger.css('width', fullWidth);
			$container.css('width', fullWidth + 2);

			$trigger.on('click', function(e) {
				if( $(this).next().is(':hidden') ) {
					$trigger.removeClass('active').next().slideUp(300);
					$(this).toggleClass('active').next().slideDown(300);
				}
				e.preventDefault();
			});

			// Resize
			jQuery(window).on('resize', function() {
				fullWidth = $container.outerWidth(true)
				$trigger.css('width', $trigger.parent().width() );
				$container.css('width', $container.parent().width() + 2 );
			});
		}
	})();

	/* end Accordion Content */

	/* ---------------------------------------------------- */
	/*	Content Tabs
	/* ---------------------------------------------------- */

	(function() {

		if(jQuery('.content-tabs').length) {

			var $contentTabs  = jQuery('.content-tabs');

			$.fn.tabs = function($obj) {
					$tabsNavLis = $obj.find('.tabs-nav').children('li'),
					$tabContent = $obj.find('.tab-content');

				$tabContent.hide();	
				$tabsNavLis.first().addClass('active').show();
				$tabContent.first().show();

				$obj.find('ul.tabs-nav li').on('click', function(e) {
					var $this = jQuery(this);

						$obj.find('ul.tabs-nav li').removeClass('active');
						$this.addClass('active');
						$obj.find('.tab-content').hide(); //Hide all tab content
						$($this.find('a').attr('href')).fadeIn();

					e.preventDefault();
				});
			}

			$contentTabs.tabs($contentTabs);
		}

	})();

	/* end Content Tabs */

	/* ---------------------------------------------------- */
	/*	Back to Top
	/* ---------------------------------------------------- */

	(function() {

		var extend = {
				button      : '#back-top',
				text        : 'Back to Top',
				min         : 200,
				fadeIn      : 400,
				fadeOut     : 400,
				speed		: 800,
				easing		: 'easeOutQuint'
			},
			oldiOS     = false,
			oldAndroid = false;
			
		// Detect if older iOS device, which doesn't support fixed position
		if( /(iPhone|iPod|iPad)\sOS\s[0-4][_\d]+/i.test(navigator.userAgent) )
			oldiOS = true;

		// Detect if older Android device, which doesn't support fixed position
		if( /Android\s+([0-2][\.\d]+)/i.test(navigator.userAgent) )
			oldAndroid = true;

		jQuery('body').append('<a href="#" id="' + extend.button.substring(1) + '" title="' + extend.text + '">' + extend.text + '</a>');

		jQuery(window).scroll(function() {
			var pos = $(window).scrollTop();
			
			if( oldiOS || oldAndroid ) {
				jQuery( extend.button ).css({
					'position' : 'absolute',
					'top'      : position + $(window).height()
				});
			}
			
			if (pos > extend.min) {
				jQuery(extend.button).fadeIn(extend.fadeIn);
			}
				
			else {
				jQuery(extend.button).fadeOut (extend.fadeOut);
			}
				
		});

		jQuery(extend.button).click(function(e){
			jQuery('html, body').animate({scrollTop : 0}, extend.speed, extend.easing);
			e.preventDefault();
		});

	})();

	/* end Back to Top */

	/* ---------------------------------------------------- */
	/*	Fancybox
	/* ---------------------------------------------------- */

		if(jQuery('.single-image').length) {
			(function() {
				jQuery('.single-image').fancybox({
					'titlePosition' : 'over',
					'transitionIn'  : 'fade',
					'transitionOut' : 'fade'
				}).each(function() {
					jQuery(this).append('<span class="curtain">&nbsp;</span>');
				});		
			})();
		}

	/* end fancybox --> End */

	/* ---------------------------------------------------------------------- */
	/*	Testimonials
	/* ---------------------------------------------------------------------- */
	
	var $quotes = jQuery('ul.quotes');

	if($quotes.length) {

		// Run slider when all images are fully loaded
		$(window).load(function() {

			$quotes.each(function(i) {
				var $this = jQuery(this);

				$this.css('height', $this.find('li:first img').height())
					.cycle({
						before: function(curr, next, opts) {
							var $this = $(this);
							$this.parent().stop().animate({ height: $this.height() }, opts.speed);
						},
						containerResize : false,
						easing          : 'easeInOutExpo',
						fx              : 'fade',
						fit             : true,
						next            : '.next',
						pause           : true,
						prev            : '.prev',
						slideExpr       : 'li',
						slideResize     : true,
						speed           : 600,
						timeout         : 4000,
						width           : '100%'
					});
			});

		});
	}
		
	/* ------------------------------------------------------------------- */
	/*	Portfolio														   */
	/* ------------------------------------------------------------------- */
	
	(function() {

		var $cont = jQuery('#portfolio-items');
		
		
		if($cont.length) {

			var $itemsFilter = jQuery('#portfolio-filter'),
				mouseOver;
				
				
			// Copy categories to item classes
			jQuery('article', $cont).each(function(i) {
				var $this = jQuery(this);
				$this.addClass( $this.attr('data-categories') );
			});

			// Run Isotope when all images are fully loaded
			jQuery(window).on('load', function() {

				$cont.isotope({
					itemSelector : 'article',
					layoutMode   : 'fitRows'
				});

			});

			// Filter projects
			$itemsFilter.on('click', 'a', function(e) {
				var $this         = jQuery(this),
					currentOption = $this.attr('data-categories');

				$itemsFilter.find('a').removeClass('active');
				$this.addClass('active');

				if(currentOption) {
					if(currentOption !== '*') currentOption = currentOption.replace(currentOption, '.' + currentOption)

					$cont.isotope({filter : currentOption});
				}

				e.preventDefault();
			});

			$itemsFilter.find('a').first().addClass('active');
		}

	})();

	/* end Portfolio  */
	
	/* ---------------------------------------------------------------------- */
	/*	Image Gallery Slider
	/* ---------------------------------------------------------------------- */

	var $slider = jQuery('.image-post-slider ul');

	if($slider.length) {

		// Run slider when all images are fully loaded
		jQuery(window).load(function() {

			$slider.each(function(i) {
				var $this = jQuery(this);

				$this.css('height', $this.find('li:first img').height())
					.after('<div class="post-pager">&nbsp;</div>')
					.cycle({
						before: function(curr, next, opts) {
							var $this = jQuery(this);
							$this.parent().stop().animate({ height: $this.height() }, opts.speed);
						},
						containerResize : false,
						easing          : 'easeInOutExpo',
						fx              : 'scrollRight',
						fit             : true,
						next            : '.next',
						pause           : true,
						pager			: '.post-pager',
						prev            : '.prev',
						slideExpr       : 'li',
						slideResize     : true,
						speed           : 600,
						timeout         : 0,
						width           : '100%'
					});
			});

		});
	}

			// Include swipe touch
			if(Modernizr.touch) {
				
				function swipe(e, dir) {
				
					var $slider = jQuery(e.currentTarget);
				
					$slider.data('dir', '')
					
					if(dir === 'left') {
						$slider.cycle('next');
					}
					
					if(dir === 'right') {
						$slider.data('dir', 'prev')
						$slider.cycle('prev');
					}
					
				}
				
				$slider.swipe({
					swipeLeft       : swipe,
					swipeRight      : swipe,
					allowPageScroll : 'auto'
				});
				
			}
			
			
				
			
/************************************************************************/
});/* DOM READY --> End													*/
/************************************************************************/
