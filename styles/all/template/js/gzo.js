$(function() { // Avoid conflicts with other libraries

'use strict';

$('.c-tabs .c-tab').click(function() {
	var tab_id = $(this).attr('data-tab');

	$('.c-tabs .c-tab').removeClass('current');
	$('.c-tab-pane').removeClass('current');

	$(this).addClass('current');
	$('#' + tab_id).addClass('current');
});

}); // Avoid conflicts with other libraries
