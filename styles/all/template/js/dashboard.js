$(function() {

    'use strict';

    var url = window.location + '';
    var path = url.replace(window.location.protocol + '//' + window.location.host + '/', '');
    var element = $('ul#sidebarnav a').filter(function() {
        return this.href === url || this.href === path;// || url.href.indexOf(this.href) === 0;
    });

    element.parentsUntil('.sidebar-nav').each(function (index)
    {
        if($(this).is('li') && $(this).children('a').length !== 0)
        {
            $(this).children('a').addClass('active');
            $(this).parent("ul#sidebarnav").length === 0
                ? $(this).addClass('active')
                : $(this).addClass('selected');
        }
        else if(!$(this).is('ul') && $(this).children('a').length === 0)
        {
            $(this).addClass('selected');

        }
        else if($(this).is('ul')){
            $(this).addClass('in');
        }

    });

    element.addClass('active');
    $('#sidebarnav a').on('click', function (e) {

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

    $('#sidebarnav >li >a.has-arrow').on('click', function (e) {
        e.preventDefault();
    });

    function handlesidebarposition() {
        $('#sidebar-position').change(function () {
            if ($(this).is(":checked")) {
                $('#flex-wrap').attr("data-sidebar-position", 'fixed');
                $('.topbar .top-navbar .navbar-header').attr("data-navheader", 'fixed');
            } else {
                $('#flex-wrap').attr("data-sidebar-position", 'absolute');
                $('.topbar .top-navbar .navbar-header').attr("data-navheader", 'relative');
            }
        });
    };
    handlesidebarposition();

    function handleheaderposition() {
        $('#header-position').change(function () {
            if ($(this).is(":checked")) {
                $('#flex-wrap').attr("data-header-position", 'fixed');
            } else {
                $('#flex-wrap').attr("data-header-position", 'relative');
            }
        });
    };
    handleheaderposition();

    function handleboxedlayout() {
        $('#boxed-layout').change(function () {
            if ($(this).is(":checked")) {
                $('#flex-wrap').attr("data-boxed-layout", 'boxed');
            } else {
                $('#flex-wrap').attr("data-boxed-layout", 'full');
            }
        });

    };
    handleboxedlayout();

    var setsidebartype = function () {
        var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
        if (width < 1170) {
            $('#flex-wrap').attr('data-sidebartype', 'mini-sidebar');
            $('#flex-wrap').addClass('mini-sidebar');
        } else {
            $('#flex-wrap').attr('data-sidebartype', 'full');
            $('#flex-wrap').removeClass('mini-sidebar');
        }
    };
    $(window).ready(setsidebartype);
    $(window).on('resize', setsidebartype);

    $('.preloader').fadeOut();

	$('.gzo-menu-toggle').click(function () {
		$('#flex-wrap').toggleClass('gzo-display-sidebar');
        $('.nav-toggler i').toggleClass('ti-menu');
	});
});
