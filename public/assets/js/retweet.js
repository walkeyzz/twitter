$(function(){
	$(document).on('click','.retweet', function(){
		var tweet_id    = $(this).data('tweet');
		var user_id     = $(this).data('user');
	    $counter        = $(this).find(".retweetsCount");
	    $count          = $counter.text();
	    $button         = $(this);


		$.post(siteUrl + 'ajax/retweet', {showPopup:tweet_id,user_id:user_id}, function(data){
			$('.popupTweet').html(data);
			$('.close-retweet-popup').click(function(){
				$('.retweet-popup').hide();
			})
		});
	});

	$(document).on('click', '.retweet-it', function(){
		var tweet_id   = $(this).data('tweet');
		var user_id    = $(this).data('user');
	    var comment    = $('.retweetMsg').val();


	    $.post(siteUrl + 'ajax/retweet', {retweet:tweet_id,user_id:user_id,comment:comment}, function(){
	    	$('.retweet-popup').hide();
	    	$count++;
	    	$counter.text($count);
	    	$button.removeClass('retweet').addClass('retweeted');
	    });

	});
});
