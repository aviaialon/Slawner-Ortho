jQuery(function($) {
	/* highlight current menu group
	------------------------------------------------------------------------- */
	if (typeof current_group_id !== "undefined")
	{
		$('#menu-group li[id="group-' + current_group_id + '"]').addClass('current');
	}


	/* global ajax setup
	------------------------------------------------------------------------- */
	/*
	$.ajaxSetup({
		type: 'GET',
		datatype: 'json',
		timeout: 20000
	});
	$('#loading').ajaxStart(function() {
		$(this).show();
	});
	$('#loading').ajaxStop(function() {
		$(this).hide();
	});
	*/

	/* nested sortables
	------------------------------------------------------------------------- */
	var menu_serialized;
	function loadSortableMenu()
	{
		$('#easymm').NestedSortableDestroy();
		$('#easymm').NestedSortable({
			/*handle: ".ns-title",*/
			accept: 'sortable_menu_list',
			helperclass: 'ns-helper',
			noNestingClass: "no-nesting",	
			currentNestingClass: 'current-nesting',
			opacity: .6,
			autoScroll: true,
	        revert: true,
			fx:400,
			onStop: function() {
				CHANGE_PROMPT.addChangeFlag('The new menu positions have not yet been saved, are you sure you want to leave this page?', function() {
					$("div#tab" + current_group_id).show();																																  
				});
				if ($.browser.msie) 
				{
					//this is fix for ie
					loadSortableMenu();
				}
			},
			onChange: function(serialized) {
				menu_serialized = serialized[0].hash;
				$('#btn-save-menu').attr('disabled', false);
			}
		});
	}
	
	loadSortableMenu();
	
	/*
	 var loadSortableMenu = function()
		{
			$('ul#easymm').NestedSortableDestroy();
			$('ul#easymm').NestedSortable({
				accept: 'sortable_menu_list',
				helperclass: 'ns-helper',
				noNestingClass: "no-nesting",	
				currentNestingClass: 'current-nesting',
				opacity: .6,
				autoScroll: true,
		        revert: true,
				fx:400,
				onStop: function() {
					alert('fixing');
					fixSortable();
				},
				onChange: function(serialized) {
					menu_serialized = serialized[0].hash;
					$('#btn-save-menu').attr('disabled', false);
				}
			});
		}();
	 
		//loadSortableMenu();
	 */
	
	
	
	/* edit menu
	------------------------------------------------------------------------- */
	$('.edit-menu').unbind('click');
	$('.edit-menu').live('click', function() {
		var intMenuId = Number($(this).attr('menuId'));
		if (intMenuId) 
		{
			gbox.show({
				type: 'ajax',
				url: site_url('edit-menu/' + intMenuId)
			});
		}
		
		$("#EditMenuForm").validationEngine();
		
		return false;
	});
	
	$('div#gbox_content form#EditMenuForm').find('input[rel=submit]').unbind('click');
	$('div#gbox_content form#EditMenuForm').find('input[rel=submit]').live('click', function(event) {
		var blnError = false;
		
		event.preventDefault();
		$('#close').hide();
		$('#loader').show();
		$('div#gbox_content').find('.rowElem').fadeTo('fast', 0.3);
		
		//
		// Validation
		//
		if ($('#edit-menu-title').val() == "")
		{
			jAlert('Please enter a menu title.', 'Oops!');
			
			$('#close').show();
			$('#loader').hide();
			$('div#gbox_content').find('.rowElem').fadeTo('fast', 1);
			
			$('#edit-menu-title').focus();
			blnError = true;
		}
		
		if (false === blnError) 
		{
			$.ajax({
				type: 'POST',
				url: $('#gbox form').attr('action'),
				data: $('#gbox form').serialize(),
				success: function(data) {
					$('#close').show();
					$('#loader').hide();
					$('div#gbox_content').find('.rowElem').fadeTo('fast',1);
					
					
					
					//var menu_id = $(this).next().next().val();
					//var menu_div = $(this).parent().parent();
					
					
					switch (data.status) {
						case 1:
								CHANGE_PROMPT.removeFlag();
								var menu_div = $('#menu-' + data.menu.id + ' div.ns-row:eq(0)');
								menu_div.find('.ns-title').html(data.menu.title);
								menu_div.find('.ns-url').html(data.menu.url);
								menu_div.find('.ns-class').html(data.menu['class']);
								gbox.hide();
								$.jGrowl('This menu was saved successfully', { header: 'Menu Saved' });
							break;
						case 2:
								gbox.hide();
								$.jGrowl('An error has occued. Please try again later.', { header: 'An Error Occured.' });
							break;
					}
				}
			});
		}	
		
		return (! blnError);
	});
	
	
	
	$('div#gbox_content').find('input[rel=cancel], a[rel=cancel]').unbind('click');
	$('div#gbox_content').find('input[rel=cancel], a[rel=cancel]').live('click', function(event) {
		$('div#gbox_content form#EditMenuForm').find('input[rel=submit]').unbind('click');
		event.preventDefault();
		gbox.hide();
	});
	
	/* delete menu
	------------------------------------------------------------------------- */
	$('.delete-menu').unbind('click');
	$('.delete-menu').live('click', function() {
		var li = $(this).closest('li');
		var param = { menuId : Number($(this).attr('menuId')) }; /*$(this).next().val()*/
		var menu_title = $(this).parent().parent().children('.ns-title').text();
		jConfirm(
			'<h2>Delete Menu</h2>Are you sure you want to delete this menu?<br><b>' + menu_title + '</b><br /><br />', 
			'Delete: ' + menu_title + ' Menu Item?', function(blnConfirmedTrue) 
			{
				if (blnConfirmedTrue)
				{
					$.post(site_url('menu-delete/' + Number(param.menuId)), param, function(data) {
						if (data.success) {
							li.remove();
							$.jGrowl('This menu was removed', { header: 'Menu Deleted' });
						} else {
							$.jGrowl('Failed to delete this menu.', { header: 'Deleted Failed.' });
						}
					});
				}
			}
		);
		
		/*
		gbox.show({
			content: '<h2>Delete Menu</h2>Are you sure you want to delete this menu?<br><b>'
				+ menu_title +
				'</b><br><br>This will also delete all submenus under this menu.',
			buttons: {
				'Yes': function() {
					$.post(site_url('menu.delete'), param, function(data) {
						if (data.success) {
							gbox.hide();
							li.remove();
						} else {
							gbox.show({
								content: 'Failed to delete this menu.'
							});
						}
					});
				},
				'No': gbox.hide
			}
		});
		 */
		return false;
	});

	/* add menu
	------------------------------------------------------------------------- */
	$('a[rel=Add_Menu]').unbind('click');
	$('a[rel=Add_Menu]').click(function(event) {
		event.preventDefault();
		gbox.show({
			type: 'ajax',
			url: site_url('add-menu/group/' + $('input#menu-group-id').val())
		});
		return false;
	});
	
	$('div#gbox_content form#AddMenuForm').find('input[rel=submit]').unbind('click');
	$('div#gbox_content form#AddMenuForm').find('input[rel=submit]').live('click', function(event) {
		var blnError = false;
	
		event.preventDefault();
		$('#close').hide();
		$('#loader').show();
		$('div#gbox_content').find('.rowElem').fadeTo('fast', 0.3);
		
		//
		// Validation
		//
		if ($('#add-menu-title').val() == "")
		{
			jAlert('Please enter a menu title.', 'Oops!');
			
			$('#close').show();
			$('#loader').hide();
			$('div#gbox_content').find('.rowElem').fadeTo('fast', 1);
			
			$('#add-menu-title').focus();
			blnError = true;
		}
		
		if (false === blnError) 
		{												   
			$.ajax({
				type: 'POST',
				url: $('div#gbox_content').find('form').attr('action'),
				data: $('div#gbox_content').find('form').serialize(),
				error: function() {
					jAlert('Add menu error. Please try again.', 'Oops!');	
					$('#close').show();
					$('#loader').hide();
					$('div#gbox_content').find('.rowElem').fadeTo('fast',1);
				},
				success: function(data) {	
					switch (data.status) {
						case 1:
							CHANGE_PROMPT.removeFlag();
							/*$('#form-add-menu')[0].reset();*/
							$('#easymm')
								.prepend(data.li)
								.SortableAddItem($('#'+data.li_id)[0]);
								
							$.jGrowl('This menu was Added successfully', { header: 'Menu Saved' });
							// Redo the jqtransform for the new checkbox
							$('form').jqTransform({imgPath:'../images/forms'});
							break;
						case 2:
							/*gbox.show({
								content: data.msg,
								autohide: 1000
							});*/
							break;
						case 3:
							/*$('#menu-title').val('').focus();*/
							break;
					}
				
					$('#close').show();
					$('#loader').hide();
					$('div#gbox_content').find('.rowElem').fadeTo('fast',1);
					gbox.hide();
				}
			});
		}
	});
	
	
	$('div#gbox_content form#AddMenuForm').find('input[rel=cancel], a[rel=cancel]').unbind('click');
	$('div#gbox_content form#AddMenuForm').find('input[rel=cancel], a[rel=cancel]').live('click', function(event) {
		$('div#gbox_content form#AddMenuForm').find('input[rel=submit]').unbind('click');
		event.preventDefault();
		gbox.hide();
	});
	
	/* save menu position
	------------------------------------------------------------------------- */
	$('#form-menu').submit(function() {
		CHANGE_PROMPT.removeFlag();
		$(this).attr('action', $(this).attr('action'));
	});

	/* add menu group
	------------------------------------------------------------------------- */
	$('#add-group a').unbind('click');
	$('#add-group a').click(function() {
		gbox.show({
			type: 'ajax',
			url: $(this).attr('href'),
			buttons: {
				'Save': function() {
					var group_title = $('#menu-group-title').val();
					if (group_title == '') {
						$('#menu-group-title').focus();
					} else {
						//$('#gbox_ok').attr('disabled', true);
						$.ajax({
							type: 'POST',
							url: site_url('menu_group.add'),
							data: 'title=' + group_title,
							error: function() {
								//$('#gbox_ok').attr('disabled', false);
							},
							success: function(data) {
								//$('#gbox_ok').attr('disabled', false);
								switch (data.status) {
									case 1:
										gbox.hide();
										$('#menu-group').append('<li><a href="' + site_url('menu&group_id=' + data.id) + '">' + group_title + '</a></li>');
										break;
									case 2:
										$('<span class="error"></span>')
											.text(data.msg)
											.prependTo('#gbox_footer')
											.delay(1000)
											.fadeOut(500, function() {
												$(this).remove();
											});
										break;
									case 3:
										$('#menu-group-title').val('').focus();
										break;
								}
							}
						});
					}
				},
				'Cancel': gbox.hide
			}
		});
		return false;
	});

	/* update menu / save menu position
	------------------------------------------------------------------------- */
	$('#btn-save-menu').attr('disabled', true);
	$('#form-menu').submit(function() {
		$('#btn-save-menu').attr('disabled', true);
		$('ul#easymm').fadeTo('fast', 0.3);
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: menu_serialized + '&groupId=' + $('input#menu-group-id').val(),
			/*
			data: {
					'menu': menu_serialized, 
					'menuGroupId': $('input#menu-group-id').val()
			},
			*/
			error: function() 
			{
				$('#btn-save-menu').attr('disabled', false);
				$.jGrowl('Save menu error. Please try again.', { header: 'Error' });
				$('ul#easymm').fadeTo('fast', 1);
			},
			success: function(data) 
			{
				CHANGE_PROMPT.removeFlag();
				$.jGrowl('Menu position has been saved', { header: 'Success' });
				$('ul#easymm').fadeTo('fast', 1);
			}
		});
		return false;
	});

	/* edit group
	------------------------------------------------------------------------- */
	$('#edit-group').unbind('click');
	$('#edit-group').click(function() {
		var sgroup = $('#edit-group-input');
		var group_title = sgroup.text();
		sgroup.html('<input value="' + group_title + '">');
		var inputgroup = sgroup.find('input');
		inputgroup.focus().select().keydown(function(e) {
			if (e.which == 13) {
				var title = $(this).val();
				if (title == '') {
					return false;
				}
				$.ajax({
					type: 'POST',
					url: site_url('menu_group.edit'),
					data: 'id=' + current_group_id + '&title=' + title,
					success: function(data) {
						if (data.success) {
							sgroup.html(title);
							$('#group-' + current_group_id + ' a').text(title);
						}
					}
				});
			}
			if (e.which == 27) {
				sgroup.html(group_title);
			}
		});
		return false;
	});

	/* delete menu group
	------------------------------------------------------------------------- */
	$('#delete-group').unbind('click');
	$('#delete-group').click(function() {
		var group_title = $('#menu-group li.current a').text();
		var param = { id : current_group_id };
		gbox.show({
			content: '<h2>Delete Group</h2>Are you sure you want to delete this group?<br><b>'
				+ group_title +
				'</b><br><br>This will also delete all menus under this group.',
			buttons: {
				'Yes': function() {
					$.post(site_url('menu_group.delete'), param, function(data) {
						if (data.success) {
							window.location = site_url('menu');
						} else {
							gbox.show({
								content: 'Failed to delete this menu.'
							});
						}
					});
				},
				'No': gbox.hide
			}
		});
		return false;
	});

});