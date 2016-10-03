$ = jQuery;
//jsi18n = i18n object containing all displayed strings to make them translateable (it's parsed in WL_Admin.php throught the gettext)  
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
	//console.log(data);
	var titleHtml = '<fieldset class="inline-edit-col" id="title-block"><h4></h4><div class="inline-edit-col"><label class="left"><span class="title">'+jsi18n.title+'</span><span class="input-text-wrap"><input type="text" name="wlist_title" id="wlist-title" value=""></span></label><label class="right"><span>'+jsi18n.allowNew+'</span><span><input type="checkbox" name="wlist_strict" id="wlist-strict" value=""></span></label></div></fieldset>';
	var pagesHtml = '<fieldset class="inline-edit-col-left wl-col"><div class="inline-edit-col"><span class="title">'+jsi18n.wlistedPages+'</span><ul class="cat-checklist" id="pages-list"></ul></div><div class="all-none"><a href="" class="select-all">'+jsi18n.selectAll+'</a>/<a href="" class="select-none">'+jsi18n.selectNone+'</a></div></fieldset>';
	var usersHtml = '<fieldset class="inline-edit-col-center wl-col"><div class="inline-edit-col"><span class="title">'+jsi18n.asToUsers+'</span><ul class="cat-checklist" id="users-list"></ul></div></fieldset>';
	var rolesHtml = '<fieldset class="inline-edit-col-right wl-col"><div class="inline-edit-col"><span class="title">'+jsi18n.asToRoles+'</span><ul class="cat-checklist" id="roles-list"></ul></div></fieldset>';
	var bottomHtml = '<p class="submit inline-edit-save"><a accesskey="c" href="#" class="button-secondary cancel alignleft" id="wlist-edit-cancel">'+jsi18n.cancel+'</a><a accesskey="s" href="#" id="wlist-edit-save" class="button-primary save alignright">'+jsi18n.save+'</a><span class="error" style="display:none"></span><br class="clear"></p>';
	editWindow = $('<tr id="wlist-form" class="inline-edit-row quick-edit-row inline-editor" style=""><td colspan="6" class="colspanchange">'+titleHtml+pagesHtml+rolesHtml+usersHtml+bottomHtml+'</td></tr>');
	if (data.strict) {
		editWindow.find("#wlist-strict").prop('checked',false);
	} else if (!data.strict) {
		editWindow.find("#wlist-strict").prop('checked',true);
	}
	var usersList = editWindow.find("#users-list");
	$.each(data.users,function(key,user){
		var item = $('<li id="user-'+user.id+'"><label class="selectit"><input value="'+user.id+'" type="checkbox" name="users[]" id="user-id-'+user.id+'"> '+user.login+'</label></li>');
		if (user.assigned) {
			item.find('input').prop('checked',true);
		}		
		usersList.append(item);
	});
	
	var rolesList = editWindow.find("#roles-list");
	$.each(data.roles,function(role,value){
		var item = $('<li id="role-'+role+'"><label class="selectit"><input value="'+role+'" type="checkbox" name="roles[]" id="role-'+role+'"> '+role+'</label></li>');
		if (value) {
			item.find('input').prop('checked',true);
		}
		rolesList.append(item);
	});
	
	
	var pagesList = editWindow.find("#pages-list");
	
	var jstreeData = [];
	
	$.each(data.pages, function(key,page){
		var item = {
			"id":page.id,
			"parent":page.parent,
			"text": page.title+' ('+page.id+')',
			"state":{
				"selected":page.assigned
			}
		};
		
		if (page.parent == 0) {
			item.parent = "#";
		} else if (page.parent == -1) {
			var emptyParent = {
				"id":page.parent+".x",
				"parent":"#",
				"text":"--("+jsi18n.missingParent+")--",
				"state":{
					"disabled":true
				}
			};
			jstreeData.push(emptyParent);
			item.parent = emptyParent.id;
		};
		jstreeData.push(item);
	});
		
	pagesList.jstree({
		'core': {
			'data':jstreeData,
			'themes':{
				'icons':false,
			},
			'strings' : {
	            'Loading ...' : jsi18n.jstreeLoading
	        }
		},
		'plugins':["checkbox"],
		'checkbox':{
			'keep_selected_style':false,
		},
	});
	
	editWindow.find(".all-none .select-all").click(function(e){
		//select none
		pagesList.jstree(true).select_all();
		e.preventDefault();
	});
	
	editWindow.find(".all-none .select-none").click(function(e){
		//select none
		pagesList.jstree(true).deselect_all();
		e.preventDefault();
	});
	
	
	var titleInput = editWindow.find("#wlist-title");
	var idInput = editWindow.find("#wlist-id");
	var editWindowTitle = editWindow.find("h4"); 
	if (line===undefined) {
		editWindowTitle.text(jsi18n.createNew);
		editWindow.appendTo("#wl-lists tbody");
		editWindow.get(0).scrollIntoView();
		$(document).scrollTop(editWindow.offset().top-$("#wpadminbar").height());
		$("#wlist-edit-cancel").click(function(){
			spinner.tackRight(editWindowTitle);
			editWindow.remove();
			editing=false;
		});
	} else {
		editWindow.find("h4").text(jsi18n.edit+'...');
		titleInput.attr("value",data.name);
		idInput.attr("value",id);
		if (line.hasClass("alternate")) {
			editWindow.addClass("alternate");
		};
		line.after(editWindow);
		line.detach();
		
		$("#wlist-edit-cancel").click(function(){
			//maybe scroll up again?
			spinner.tackRight(editWindowTitle);
			editWindow.replaceWith(line);
			window.location.hash = "";
			editing=false;
		});
	}
	
	$("#wlist-edit-save").click(function(e){
		spinner.tackRight(editWindowTitle);
		if (titleInput.attr("value")=='') {
			throwNotice(false,jsi18n.saveWNameErr);
			//spinner.tearOff();
			//inform user somehow
			return true;
		}
		var pagesArray = [];
		pagesArray = pagesList.jstree().get_selected();
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
					'strict': !$('#wlist-strict').prop('checked'),
					'nonce': data.nonce
				},
				error: function() {
						var message = "Server error";
						throwNotice(false,result.message);
						window.location.hash = "";
					},
				success: function(response) {
					result = $.parseJSON(response);
					if (result.success) {
						if (line == undefined) {
							line = $('<tr id="wlist-'+result.id+'" class="whitelist-row"><th scope="row" class="id-column">'+result.id+'</th><td><span class="wlist-name"></span><div class="row-actions"><span class="edit"><a href="#" id="edit-wlist-'+result.id+'">'+jsi18n.edit+'</a>|</span><span class="trash"><a href="#" id="delete-wlist-'+result.id+'">'+jsi18n.del+'</a></span></div></td><td class="wlist-pages"></td><td class="wlist-roles"></td><td class="wlist-users"></td><td class="wlist-strict"></td></tr>');
							line.find("span.trash a").attr("href",result.deleteNonce);
							line.appendTo("#wl-lists tbody");
							
						} 
						line.find(".wlist-name").text(result.name);
						line.find(".wlist-users").text(result.users.join(", "));
						line.find(".wlist-roles").text(result.roles.join(", "));
						
						//console.log(result.pages);
						var assignedPages = [];
						for(i = 0; i < result.pages.length; i++) {
							var page = result.pages[i];
							//console.log(page);
							var link = '<a href="'+page.url+'">'+page.title+'</a> ('+page.id+')';
							
							if (i == page.length - 1 && i < 5) {
	                            assignedPages.push('<span class="wlist-page">'+link+'</span>'); //no comma (last, visible)
	                        } else if (i == page.length - 1 && i >= 5) {
	                            assignedPages.push('<span class="wlist-page more">'+link+'</span>'); //no comma (last, hidden)                                                        
	                        } else if (i < 5) { //visible
	                            assignedPages.push('<span class="wlist-page">'+link+', </span>');
	                        } else { //hidden
	                            assignedPages.push('<span class="wlist-page more">'+link+', </span>');
	                        }													
						};
						var pagesHtml = assignedPages.join("");
						if (result.pages.length>5) {
							pagesHtml+='<span class="dots">...</span><a href="" class="more-link">('+jsi18n.moreLink+')</a>';	
						}
						
						line.find(".wlist-pages").html(pagesHtml);
						strictText = (result.strict)?jsi18n.no:jsi18n.yes;
						line.find(".wlist-strict").text(strictText);	
						editWindow.replaceWith(line);
						successNotice = (result.message=='created')?jsi18n.createdSuccess:jsi18n.editedSuccess;
						throwNotice(true,successNotice);
						editing = false;	
					} else {
						var message = jsi18n.err;
						throwNotice(false,result.message);
					}
					spinner.tearOff();
					window.location.hash = "";
				}
		   
		});
		e.preventDefault();
		
	});	
	
}


function createWlist() {
	if (editing) {
		//already editing/creating
		answer = confirm(jsi18n.confirmLeave);
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
	spinner.tackRight($("#create-wlist"));
	$.ajax({ 
	    type: 'POST', 
	    url: ajaxurl, 
	    data: { 
	    	'action': 'wl_load',
			}, 
	    dataType: 'json',
	    success: function(response) { 
	    	spinner.tearOff();
	        buildEditWindow(response);
	        window.location.hash = "#new";			
	    }   
	});
	//get data from AJAX
	//build editor
	//
}

function editWlist(clicked) {
	if (editing) {		
		answer = confirm(jsi18n.confirmLeave);
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
	var caller = $(clicked);
	line = caller.closest("tr");
	spinner.tackRight(line.find(".wlist-name"));
	id = caller.attr("id").replace("edit-wlist-","");
	window.location.hash = "#edit="+id;
	
	$.ajax({ 
	    type: 'POST', 
	    url: ajaxurl, 
	    data: { 
	    	'action': 'wl_load',
			'id':id
			}, 
	    dataType: 'json',
	    success: function(response) { 
	        spinner.tearOff();
	        buildEditWindow(response, line, id);
			//do nothing right now
	    }   
	});
}


function deleteWlist(clicked) {
	var caller = $(clicked);
	var id = caller.attr("id").replace("delete-wlist-","");
	line = caller.closest("tr");
	var name = line.find(".wlist-name").text();
	
	answer = confirm(jsi18n.confirmDelete.replace('{listName}',name));
	
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
				throwNotice(true,jsi18n.deletedSuccess);		
			});
		} else {
			throwNotice(false, response);
			spinner.tearOff();
			//throw notice
		}
	});
}

$(document).on('click','#create-wlist',function(e) {
	e.preventDefault();
	createWlist();	
});
$(document).on('click','span.edit a',function(e) {
	e.preventDefault();
	editWlist(e.target);
});
$(document).on('click','span.trash a',function(e) {
	e.preventDefault();
	deleteWlist(e.target);
});

$(document).on('click','a.more-link',function(e) {
	e.preventDefault();
	var moreLink = $(this);
	wlistPages = moreLink.parent();
	wlistPages.find("span.wlist-page.more").toggle();
	wlistPages.find(".dots").toggle();
	var text = moreLink.text();
	moreLink.text(text == "(more)" ? "(less)" : "(more)");	
});

var hash = window.location.hash.substr(1);

if (hash == 'new') {
	createWlist();
} else if (hash.indexOf('edit')==0) {
	toEdit = hash.split("=")[1];
	editWlist($("#edit-wlist-"+toEdit));
	//split on = and open wlist with the id
}

