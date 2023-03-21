$(function() {

	'use strict';

	var url = window.location + '';
	var path = url.replace(window.location.protocol + '//' + window.location.host + '/', '');
	var element = $('ul#sidebarnav a').filter(function() {
		return this.href === url || this.href === path;
	});

	element.parentsUntil('.sidebar-nav').each(function() {
		if ($(this).is('li') && $(this).children('a').length !== 0) {
			$(this).children('a').addClass('active');
			$(this).parent("ul#sidebarnav").length === 0
				? $(this).addClass('active')
				: $(this).addClass('selected');
		}
		else if (!$(this).is('ul') && $(this).children('a').length === 0) {
			$(this).addClass('selected');
		}
		else if ($(this).is('ul')) {
			$(this).addClass('in');
		}
	});

	element.addClass('active');
	$('#sidebarnav a').on('click', function() {
		if (!$(this).hasClass('active')) {
			// hide any open menus and remove all other classes
			$('ul', $(this).parents('ul:first')).removeClass('in');
			$('a', $(this).parents('ul:first')).removeClass('active');

			// open our new menu and add the open class
			$(this).next('ul').addClass('in');
			$(this).addClass('active');
		}
		else if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parents('ul:first').removeClass('active');
			$(this).next('ul').removeClass('in');
		}
	})

	$('#sidebarnav >li >a.has-arrow').on('click', function(e) {
		e.preventDefault();
	});

	var setsidebartype = function() {
		var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
		if ($('#left-sidebar').hasClass('gzo-display-sidebar')) {
			if (width < 1170) {
				$('#flex-wrap').attr('data-sidebartype', 'mini-sidebar');
				$('#flex-wrap').addClass('mini-sidebar');
				//$('#left-sidebar').removeClass('gzo-display-none')
			} else {
				$('#flex-wrap').attr('data-sidebartype', 'full');
				$('#flex-wrap').removeClass('mini-sidebar');
			}
		// } else if ($('#flex-wrap').hasClass('gzo-display-sidebar')) {
		// 	$('#left-sidebar').removeClass('gzo-display-none')
		} else {
			$('#flex-wrap').attr('data-sidebartype', 'gzo-wrap');
			$('#flex-wrap').addClass('gzo-wrap');
		}
	};
	$(window).ready(setsidebartype);
	$(window).on('resize', setsidebartype);

	$('.gzo-menu-toggle').click(function() {
		// var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
		// if (width > 1000) {
			$('#left-sidebar').toggleClass('gzo-display-none gzo-display-sidebar');
		// }
		// else {
			$('#flex-wrap').toggleClass('gzo-display-sidebar');
		// }
	});
});
