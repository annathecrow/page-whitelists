$ = jQuery;
editing = false;
spinner = $("#spinner");
spinner.tackRight = function(element) {
	this.appendTo(element);
	this.show();
};
spinner.tackLeft = function(element) {
	this.prependTo(element);
	this.show();
};
spinner.tearOff = function() {
	this.hide();
	this.detach();
};

function throwNotice(success,message) {
	$(document).scrollTop(0);
	var noticeClass = (success)?"updated":"error";
	if (typeof notice == 'undefined') {
		notice = $('<div id="message" class="below-h2 '+noticeClass+'"><p>'+message+'</p></div>');
		notice.hide();
		notice.insertAfter(".wrap h2");
		notice.fadeIn();	
	} else {
		notice.fadeOut('fast', function(){
			notice.find("p").text(message);
			notice.removeClass("updated").removeClass("error").addClass(noticeClass);
			notice.fadeIn();	
		});		
	}	
}

function buildEditWindow(data,line,id) {
	titleHtml = '<fieldset class="inline-edit-col"><div class="inline-edit-col"><h4></h4><label><span class="title">Title</span><span class="input-text-wrap"><input type="text" name="wlist_title" id="wlist-title" value=""></span></label></div></fieldset>';
	pagesHtml = '<fieldset class="inline-edit-col-left wl-col"><div class="inline-edit-col"><span class="title">Whitelisted pages</span><ul class="cat-checklist" id="pages-list"></ul></div></fieldset>';
	usersHtml = '<fieldset class="inline-edit-col-center wl-col"><div class="inline-edit-col"><span class="title">Assigned to users</span><ul class="cat-checklist" id="users-list"></ul></div></fieldset>';
	rolesHtml = '<fieldset class="inline-edit-col-right wl-col"><div class="inline-edit-col"><span class="title">Assigned to roles</span><ul class="cat-checklist" id="roles-list"></ul></div></fieldset>';
	bottomHtml = '<p class="submit inline-edit-save"><a accesskey="c" href="#" class="button-secondary cancel alignleft" id="wlist-edit-cancel">Cancel</a><a accesskey="s" href="#" id="wlist-edit-save" class="button-primary save alignright">Save</a><span class="error" style="display:none"></span><br class="clear"></p>';
	editWindow = $('<tr id="wlist-form" class="inline-edit-row quick-edit-row inline-editor" style=""><td colspan="5" class="colspanchange">'+titleHtml+pagesHtml+rolesHtml+usersHtml+bottomHtml+'</td></tr>');
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
		var item = $('<li id="page-'+page.id+'"><label class="selectit"><input value="'+page.id+'" type="checkbox" name="pages[]" id="page-id-'+page.id+'"> '+page.title+' ('+page.id+')</label></li>');
		if (page.assigned) {
			item.find("input").prop('checked',true);
		}
		pagesList.append(item);
	});
	var titleInput = editWindow.find("#wlist-title");
	var idInput = editWindow.find("#wlist-id");
	var editWindowTitle = editWindow.find("h4"); 
	if (line===undefined) {
		editWindowTitle.text("Create New...");
		editWindow.appendTo("#wl-lists tbody");
		$("#wlist-edit-cancel").click(function(){
			console.log("cancelling new");
			spinner.tackRight(editWindowTitle);
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
		line.after(editWindow);
		line.detach();
		
		$("#wlist-edit-cancel").click(function(){
			//maybe scroll up again?
			console.log("cancelling editing");
			spinner.tackRight(editWindowTitle);
			editWindow.replaceWith(line);
			editing=false;
		});
	}
	
	$("#wlist-edit-save").click(function(){
		spinner.tackRight(editWindowTitle);
		if (titleInput.attr("value")=='') {
			throwNotice(false,"cannot save a whitelist without a name.");
			//spinner.tearOff();
			//inform user somehow
			return false;
		}
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
					'id': data.id,
					'pages': pagesArray.join(),
					'users': usersArray.join(),
					'roles': rolesArray.join(),
					'nonce': data.nonce
					}, 
				success: function(response) {
					result = $.parseJSON(response);
					if (result.success) {
						console.log(result);
						if (line == undefined) {
							line = $('<tr id="wlist-'+result.id+'" class="whitelist-row"><th scope="row" class="id-column">'+result.id+'</th><td><span class="wlist-name"></span><div class="row-actions"><span class="edit"><a href="#" id="edit-wlist-'+result.id+'">Edit</a>|</span><span class="trash"><a href="#" id="delete-wlist-'+result.id+'">Delete</a></span></div></td><td class="wlist-pages"></td><td class="wlist-roles"></td><td class="wlist-users"></td></tr>');
							line.find("span.edit a").click(editWlist);
							line.find("span.trash a").click(deleteWlist).attr("href",result.deleteNonce);
							line.appendTo("#wl-lists tbody");
							
						} 
						line.find(".wlist-name").text(result.name);
						line.find(".wlist-users").text(result.users.join(", "));
						line.find(".wlist-roles").text(result.roles.join(", "));
						line.find(".wlist-pages").text(result.pages.join(", "));	
						editWindow.replaceWith(line);
						throwNotice(true,"Whitelist successfully " + result.message+".");
						editing = false;	
					} else {
						var message = "Error";
						throwNotice(false,result.message);
					}
					spinner.tearOff();
				}
		   
		});
		return false;
		
	});	
	
}


function createWlist(e) {
	if (editing) {
		//already editing/creating
		answer = confirm("You have unsaved changes. Do you want to continue?");
		if (!answer) {
			return false;
		} else {
			//fold down current edit window
			editWindow.find("#wlist-edit-cancel").click();
			editing=true;
		}
	} else {
		editing = true;
	}
	spinner.tackRight($(e.currentTarget));
	$.ajax({ 
	    type: 'POST', 
	    url: ajaxurl, 
	    data: { 
	    	'action': 'wl_load',
			}, 
	    dataType: 'json',
	    success: function(response) { 
	    	spinner.tearOff();
	        console.log(response);
	        buildEditWindow(response);			
	    }   
	});
	//get data from AJAX
	//build editor
	//
	
	return false;
}

function editWlist(e) {
	if (editing) {		
		answer = confirm("You have unsaved changes. Do you want to continue?");
		if (!answer) {
			return false;
		} else {
			//fold down current edit window
			editWindow.find("#wlist-edit-cancel").click();
			editing=true;
		}
	} else {
		editing = true;		
		
	}	
	var caller = $(e.currentTarget);
	line = caller.closest("tr");
	spinner.tackRight(line.find(".wlist-name"));
	id = caller.attr("id").replace("edit-wlist-","");
	
	
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
	        spinner.tearOff();
	        buildEditWindow(response, line, id);
			//do nothing right now
	    }   
	});
	return false;
}


function deleteWlist(e) {
	var caller = $(e.currentTarget);
	var id = caller.attr("id").replace("delete-wlist-","");
	console.log(caller);
	line = caller.closest("tr");
	console.log(line);
	var name = line.find(".wlist-name").text();
	
	answer = confirm("Are you sure you want to delete whitelist '"+name+"'?");
	
	if (!answer) {return false;}
	spinner.tackRight(line.find(".wlist-name"));
	var data = {
		'action': 'wl_delete',
		'nonce' : caller.attr("href"),
		'id': id
	};
	
	$.post(ajaxurl, data, function(response) {
		if(response=='success') {
			//line.css("background-color","#dd3d36");
			line.fadeOut('fast',function(){					
				line.nextAll().toggleClass('alternate');
				line.remove();
				throwNotice(true,"Whitelist successfully deleted.");		
			});
		} else {
			throwNotice(false, response);
			//throw notice
		}
	});
	return false;
}

$("#create-wlist").click(createWlist);
$("span.edit a").click(editWlist);
$("span.trash a").click(deleteWlist);
