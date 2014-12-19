$ = jQuery;
editing = false;

function notice(message) {
	console.log("noticing");
	notice = $('<div id="message" class="updated below-h2"><p>'+message+'</p></div>');
	notice.hide();
	notice.insertAfter(".wrap h2");
	notice.fadeIn();
	//possibly a timeout and remove?
}

function EditWindow() {
	
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
	
	$.ajax({ 
	    type: 'POST', 
	    url: ajaxurl, 
	    data: { 
	    	'action': 'wl_load',
			}, 
	    dataType: 'json',
	    success: function(response) { 
	        console.log(response);
	        //add the editor as the last line of the table
	        //fill it out
	        //add ids to the buttons
	        //???
	        //PROFIT!
			editing = false;
	    }   
	});
	//get data from AJAX
	//build editor
	//
	
	return false;
});

$("span.edit a").click(function(){
	if (editing) {
		//already editing/creating
		//maybe ask if user wants to cancel edit and start editing this list?
		return false;
	} else {
		editing=true;
	}
		
	caller = $(this);
	id = caller.attr("id").replace("edit-wlist-","");
	line = caller.closest("tr");
	name = line.find(".list-name").text();
	
	$.ajax({ 
	    type: 'POST', 
	    url: ajaxurl, 
	    data: { 
	    	'action': 'wl_load',
			'id':id
			}, 
	    dataType: 'json',
	    success: function(response) { 
	        console.log(response);
			editing = false;
			//do nothing right now
	    }   
	});
});

