$(function(){
	$('#postComment').click(function(){
		var comment = $('#commentField').val();
		var tweet_id = $('#commentField').data('tweet');

		$.post(siteUrl + 'ajax/comment', {comment:comment,tweet_id:tweet_id}, function(data){
			$('#comments').html(data);
			$('#commentField').val('');
		});
	});
});
