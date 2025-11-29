(function ($) {
	"use strict";

	/* ..............................................
	   Loader
	   ................................................. */
	$(window).on('load', function () {
		$('.preloader').fadeOut();
		$('#preloader').delay(550).fadeOut('slow');
		$('body').delay(450).css({
			'overflow': 'visible'
		});
	});

	/* ..............................................
	   Fixed Menu
	   ................................................. */

	$(window).on('scroll', function () {
		if ($(window).scrollTop() > 50) {
			$('.main-header').addClass('fixed-menu');
		} else {
			$('.main-header').removeClass('fixed-menu');
		}
	});

	/* ..............................................
	   Gallery
	   ................................................. */

	// Prefer Superslides if the element uses slides-container markup; otherwise fallback to
	// Bootstrap Carousel when the markup matches bootstrap requirements.
	(function initSlider() {
		var $slidesEl = $('#slides-shop');
		var isMobile = window.matchMedia && window.matchMedia('(max-width: 991px)').matches;
		var hasSlidesContainerMarkup = $slidesEl.length && $slidesEl.find('.slides-container').length;

		// If markup matches Superslides and the plugin is available, prefer it.
		if (hasSlidesContainerMarkup && $.fn.superslides) {
			try {
				$slidesEl.superslides({
					inherit_width_from: '.cover-slides',
					inherit_height_from: '.cover-slides',
					play: 5000,
					animation: 'fade',
					// Allow the page to be scrollable on touch devices by enabling scrollable
					scrollable: true,
				});
			} catch (err) {
				console.warn('Superslides init failed:', err);
			}
		} else if (typeof bootstrap !== 'undefined' && bootstrap.Carousel) {
			try {
				// Bootstrap carousel requires a specific markup; initialize only if valid
				var carouselInner = $slidesEl.find('.carousel-inner');
				var carouselEl = $slidesEl.get(0);
				if (carouselEl && carouselInner.length) {
					bootstrap.Carousel.getOrCreateInstance(carouselEl, { interval: 5000, pause: 'hover', keyboard: true });
				}
			} catch (err) {
				// ignore
			}
		} else if ($slidesEl.length && $.fn.superslides) {
			// As a last resort, fallback to Superslides for either mobile/desktop
			try {
				$slidesEl.superslides({
					inherit_width_from: '.cover-slides',
					inherit_height_from: '.cover-slides',
					play: 5000,
					animation: 'fade',
					scrollable: true,
				});
			} catch (err) {
				console.warn('Fallback superslides init failed:', err);
			}
		}
	})();

	// Ensure that every slide has an overlay background, whether using UL/LIs (Superslides) or Bootstrap Carousel
	$(".cover-slides .slides-container li, .cover-slides .carousel-item").append("<div class='overlay-background'></div>");

	/* ..............................................
	   Map Full
	   ................................................. */

	$(document).ready(function () {
		$(window).on('scroll', function () {
			if ($(this).scrollTop() > 100) {
				$('#back-to-top').fadeIn();
			} else {
				$('#back-to-top').fadeOut();
			}
		});
		$('#back-to-top').click(function () {
			$("html, body").animate({
				scrollTop: 0
			}, 600);
			return false;
		});
	});

	/* ..............................................
	   Special Menu
	   ................................................. */

	var Container = $('.container');
	Container.imagesLoaded(function () {
		var portfolio = $('.special-menu');
		portfolio.on('click', 'button', function () {
			$(this).addClass('active').siblings().removeClass('active');
			var filterValue = $(this).attr('data-filter');
			$grid.isotope({
				filter: filterValue
			});
		});
		var $grid = $('.special-list').isotope({
			itemSelector: '.special-grid'
		});
	});

	/* ..............................................
	   BaguetteBox
	   ................................................. */

	baguetteBox.run('.tz-gallery', {
		animation: 'fadeIn',
		noScrollbars: true
	});

	/* ..............................................
	   Offer Box
	   ................................................. */

	// Initialize the offer ticker. Keep a safe re-init on document ready in case the
	// initial script runs before the DOM or when the file is hot-reloaded.

	function initOfferTicker() {
		try {
			if ($.fn.inewsticker && $('.offer-box').length) {
				$('.offer-box').inewsticker({
					speed: 3000,
					effect: 'slide',
					dir: 'ltr',
					font_size: 13,
					color: '#ffffff',
					font_family: 'Montserrat, sans-serif',
					delay_after: 1000
				});
			} else {
				// Fallback: implement a simple vertical ticker if plugin is not available
				$('.offer-box').each(function () {
					var $ticker = $(this);
					var speed = 3000;
					var fn = function () {
						var $items = $ticker.children();
						if ($items.length <= 1) return;
						var $first = $items.eq(0);
						var $second = $items.eq(1);
						$first.slideUp(function () { $first.appendTo($ticker).show(); });
					};
					setInterval(fn, speed);
				});
			}
		} catch (e) {
			console.warn('Offer ticker initialization error', e);
		}
	}

	// Run now in case earlier code didn't pick it up
	initOfferTicker();

	// Ensure initialization also occurs after the DOM is ready
	$(document).ready(function () {
		initOfferTicker();
	});

	/* ..............................................
	   Tooltip
	   ................................................. */

	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});

	/* ..............................................
	   Owl Carousel Instagram Feed
	   ................................................. */

	$('.main-instagram').owlCarousel({
		loop: true,
		margin: 0,
		dots: false,
		autoplay: true,
		autoplayTimeout: 3000,
		autoplayHoverPause: true,
		navText: ["<i class='fas fa-arrow-left'></i>", "<i class='fas fa-arrow-right'></i>"],
		responsive: {
			0: {
				items: 2,
				nav: true
			},
			600: {
				items: 3,
				nav: true
			},
			1000: {
				items: 5,
				nav: true,
				loop: true
			}
		}
	});

	/* ..............................................
	   Featured Products
	   ................................................. */

	$('.featured-products-box').owlCarousel({
		loop: true,
		margin: 15,
		dots: false,
		autoplay: true,
		autoplayTimeout: 3000,
		autoplayHoverPause: true,
		navText: ["<i class='fas fa-arrow-left'></i>", "<i class='fas fa-arrow-right'></i>"],
		responsive: {
			0: {
				items: 1,
				nav: true
			},
			600: {
				items: 3,
				nav: true
			},
			1000: {
				items: 4,
				nav: true,
				loop: true
			}
		}
	});

	/* ..............................................
	   Scroll
	   ................................................. */

	$(document).ready(function () {
		$(window).on('scroll', function () {
			if ($(this).scrollTop() > 100) {
				$('#back-to-top').fadeIn();
			} else {
				$('#back-to-top').fadeOut();
			}
		});
		$('#back-to-top').click(function () {
			$("html, body").animate({
				scrollTop: 0
			}, 600);
			return false;
		});
	});


	/* ..............................................
	   Slider Range
	   ................................................. */

	$(function () {
		$("#slider-range").slider({
			range: true,
			min: 0,
			max: 4000,
			values: [1000, 3000],
			slide: function (event, ui) {
				$("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
			}
		});
		$("#amount").val("$" + $("#slider-range").slider("values", 0) +
			" - $" + $("#slider-range").slider("values", 1));
	});

	/* ..............................................
	   NiceScroll
	   ................................................. */

	$(".brand-box").niceScroll({
		cursorcolor: "#9b9b9c",
	});


}(jQuery));
