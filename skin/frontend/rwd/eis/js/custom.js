// JavaScript Document
var $j = jQuery.noConflict();
$j(document).ready(function(){
	
	(function(){
		$j.fn.extend({
		accordion: function() {
		return this.each(function() {
		
		var cul = $j(this);
		
		if(cul.data('accordiated'))
		return false;
		
		$j.each(cul.find('ul, li>div'), function(){
			$j(this).data('accordiated', true);
			$j(this).hide();
		});
		
		$j.each(cul.find('span.head'), function(){
			$j(this).click(function(e){
			activate(this);
			return void(0);
			});
		});
		
		var active = (location.hash)?$j(this).find('a[href=' + location.hash + ']')[0]:"";
		
		if(active){
			activate(active, 'toggle');
			$j(active).parents().show();
		}
		
		function activate(el,effect){
			$j(el).parent('li').toggleClass('active').siblings().removeClass('active').children('ul, div').slideUp('fast');
			$j(el).siblings('ul, div')[(effect || 'slideToggle')]((!effect)?'fast':null);
		}
		
		});
		}
		});
	})($j);
	
	$j("ul.accordion li.parent").each(function(){
		$j(this).append('<span class="head"><a href="javascript:void(0)"></a></span>');
	});
	
	$j('ul.accordion').accordion();
	
	$j("ul.accordion li.active").each(function(){
		$j(this).children().next("ul").css('display', 'block');
	});
	
});

(function($j) {})(jQuery);

$j(document).ready(function () {
$j('.currency_pan').click(function(){  
	$j(".currency_detail").toggle();
});
$j('.select_lang').click(function(){  
	$j(".language_detail").toggle();
});
});
$j(document).ready(function(){
	$j(".leftbanner > li:gt(0)").hide();
	setInterval(function() { 
	  $j('.leftbanner > li:first')
		.fadeOut(1000)
		.next()
		.fadeIn(1000)
		.end()
		.appendTo('.leftbanner');
	},  3000);
});

    $j(document).ready(function() {
       var owl = $j('#owl-demo');
      owl.owlCarousel({
        margin: 0,
		autoplay:true,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
		animateOut: 'slideOutDown',
        animateIn: 'flipInX',
		transitionStyle : "backSlide",
        loop: true,
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 1
          },
          1000: {
            items: 1
          }
        }
      })
	  
    });

 $j(document).ready(function() {
       var owl = $j('#block-related');
      owl.owlCarousel({
        margin: 0,
		autoplay:true,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
		nav:true,
        loop: true,
        responsive: {
          0: {
            items: 2
          },
          600: {
            items: 3
          },
          1000: {
            items: 4
          }
        }
      })
	  
    });
	 $j(document).ready(function() {
       var owl = $j('#upsell-product-table');
      owl.owlCarousel({
        margin: 0,
		autoplay:true,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
		nav:true,		
        loop: true,
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 2
          },
          1000: {
            items: 4
          }
        }
      })
	  
    });
	$j(document).ready(function() {
       var owl = $j('#owl-demo3');
      owl.owlCarousel({
        margin: 0,
		autoplay:true,
        autoplayTimeout:5000,
		nav:true,
        autoplayHoverPause:true,
        loop: true,
        responsive: {
          0: {
            items: 2
          },
          600: {
            items: 3
          },
          1000: {
            items: 4
          }
        }
      })
	  
    });
	

    $j(document).ready(function() {      
	var owl = $j('#owl-demo2');
      owl.owlCarousel({
        margin: 5,
		autoPlay : true,
		nav:true,
		 autoplayTimeout:5000,
        loop: true,
        responsive: {
          0: {
            items: 3
          },
          600: {
            items: 3
          },
          1000: {
            items: 4
          }
        }
      })
});
   $j(document).ready(function() {      
	var owl = $j('#gallery-image');
      owl.owlCarousel({
        margin: 20,
		autoPlay : true,
        loop: true,
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 2
          },
          1000: {
            items: 4
          }
        }
      })
});

$j(document).ready(function() {      
	var owl = $j('#upsell-product-table');
      owl.owlCarousel({
        margin: 20,
		autoPlay : true,
        loop: true,
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 2
          },
          1000: {
            items: 4
          }
        }
      })
});

 
	
