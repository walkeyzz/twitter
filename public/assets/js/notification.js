notification = function(){
	$.get(siteUrl + "ajax/notification", {showNotification:true}, function(data){
		if(data){
			if(data.notification > 0){
				$('#notification').addClass('span-i');
				$('#notification').html(data.notification);
			}
			if(data.messages > 0){
				$('#messages').show();
 				$('#messages').addClass('span-i');
				$('#messages').html(data.messages);
			}
		}
	}, 'json');
}

setInterval(notification, 10000);
