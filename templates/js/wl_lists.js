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

function buildEditWindow(data,line,id) {
	editWindow = $('<tr id="wlist-new" class="inline-edit-row quick-edit-row inline-editor" style=""><td colspan="6" class="colspanchange"><fieldset class="inline-edit-col-left"><div class="inline-edit-col"><h4></h4><label><span class="title">Title</span><span class="input-text-wrap"><input type="text" name="wlist_title" id="wlist-title" value=""></span></label><span class="title">Whitelisted pages</span><ul class="cat-checklist" id="pages-list"></ul></div></fieldset><fieldset class="inline-edit-col-right"><div class="inline-edit-col"><span class="title">Assigned to users</span><ul class="cat-checklist" id="users-list"></ul><span class="title">Assigned to roles</span><ul class="cat-checklist" id="roles-list"></ul></div></fieldset><input type="hidden" id="wlist-id" name="wlist-id" value=""><p class="submit inline-edit-save"><a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft" id="wlist-edit-cancel">Cancel</a><a accesskey="s" href="#inline-edit" id="wlist-edit-save" class="button-primary save alignright">Save</a><span class="error" style="display:none"></span><br class="clear"></p></td></tr>');
		
	usersList = editWindow.find("#users-list");
	$.each(data.users,function(key,user){
		var item = $('<li id="user-'+user.id+'"><label class="selectit"><input value="'+user.id+'" type="checkbox" name="users[]" id="user-id-'+user.id+'"> '+user.login+'</label></li>');
		if (user.assigned) {
			item.find('input').prop('checked',true);
		}		
		usersList.append(item);
	});
	
	rolesList = editWindow.find("#roles-list");
	$.each(data.roles,function(role,value){
		var item = $('<li id="role-'+role+'"><label class="selectit"><input value="'+role+'" type="checkbox" name="roles[]" id="role-'+role+'"> '+role+'</label></li>');
		if (value) {
			item.find('input').prop('checked',true);
		}
		rolesList.append(item);
	});
	
	pagesList = editWindow.find("#pages-list");
	$.each(data.pages, function(key,page){
		var item = $('<li id="page-'+page.id+'"><label class="selectit"><input value="'+page.id+'" type="checkbox" name="pages[]" id="page-id-'+page.id+'"> '+page.title+'</label></li>');
		if (page.assigned) {
			item.find("input").prop('checked',true);
		}
		pagesList.append(item);
	});
	var titleInput = editWindow.find("#wlist-title");
	var idInput = editWindow.find("#wlist-id");
	if (line===undefined) {
		editWindow.find("h4").text("Create New...");
		editWindow.appendTo("#wl-lists tbody");
		$("#wlist-edit-cancel").click(function(){
			editWindow.remove();
			editing=false;
		});
	} else {
		editWindow.find("h4").text("Edit...");
		titleInput.attr("value",data.name);
		idInput.attr("value",id);
		if (line.hasClass("alternate")) {
			editWindow.addClass("alternate");
		};
		line.replaceWith(editWindow);
		$("#wlist-edit-cancel").click(function(){
			editWindow.replaceWith(line);
			editing=false;
		});
		//replaces line
		//somehow take over the "alternate" class?
		//after saving, line will be "put back" with new content
	}
	$("#wlist-edit-save").click(function(){
		console.log($("#wlist-edit-form").serializeArray());
		var pagesArray = [];
		pagesList.find('input:checked').each(function(key,item){
			pagesArray.push(item.value);
		});
		var usersArray = [];
		usersList.find('input:checked').each(function(key,item){
			usersArray.push(item.value);
		}); 
		var rolesArray = [];
		rolesList.find('input:checked').each(function(key,item){
			rolesArray.push(item.value);
		});
		
		$.ajax({ 
				type: 'POST', 
				url: ajaxurl, 
				data: { 
					'action': 'wl_save',
					'name': titleInput.attr("value"),
					'id': idInput.attr("value"),
					'pages': pagesArray.join(),
					'users': usersArray.join(),
					'roles': rolesArray.join()
					}, 
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
		
	});	
	
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
	        buildEditWindow(response);
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
	        buildEditWindow(response, line, id);
			editing = false;
			//do nothing right now
	    }   
	});
});

