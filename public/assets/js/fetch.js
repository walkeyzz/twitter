$(function(){
	var win = $(window);
	var offset = 10;

	win.scroll(function(){
		if($(document).height() <= (win.height() + win.scrollTop())){
			offset +=10;
			$('#loader').show();
			$.post(siteUrl + 'ajax/fetchposts', {fetchPost:offset}, function(data){
				$('.tweets').html(data);
				$('#loader').hide();
			});
		}
	});
});
