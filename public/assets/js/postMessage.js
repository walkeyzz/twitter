$(function(){
	$(document).on('click', '#send', function(){
		var message = $('#msg').val();
		var get_id   = $(this).data('user');
		$.post(siteUrl + 'ajax/messages', {sendMessage:message,get_id:get_id}, function(data){
			getMessages();
			$('#msg').val('');
		});
	});
});
