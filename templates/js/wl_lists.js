$ = jQuery;

function notice(message) {
	console.log("noticing");
	notice = $('<div id="message" class="updated below-h2"><p>'+message+'</p></div>');
	notice.hide();
	notice.insertAfter(".wrap h2");
	notice.fadeIn();
	//possibly a timeout and remove?
}

/**
 * delete whitelist
 */
$("span.trash a").click(function(){
	caller = $(this);
	id = caller.attr("id").replace("delete-wlist-","");
	line = caller.closest("tr");
	name = line.find(".list-name").text();
	
	answer = confirm("Are you sure you want to delete whitelist '"+name+"'?");
	if (!answer) {return false;}
	
	var data = {
		'action': 'wl_delete',
		'id': id
	};
	
	$.post(ajaxurl, data, function(response) {
		if(response=='success') {
			//line.css("background-color","#dd3d36");
			line.fadeOut('fast',function(){					
				line.nextAll().toggleClass('alternate');
				line.remove();
				$(document).scrollTop(0);
				notice("Whitelist successfully deleted.");
				
			});
		}
	});//end of ajax
	
	return false;
});//end of delete


$("#create-wlist").click(function(){
	if (editing) {
		//already editing/creating
		return false;
	} else {
		editing=true;
	}
	//get data from AJAX
	//build editor
	//
	
	return false;
});

