var tagsChoices = {
	'en': new Array(),
	'fr': new Array()	
};
(function($) {
	$.fn.tagit = function(options) {

		var el = this;
		var maxInput = Number(options.maxValues);
		targetForm = options.targetForm;
		BACKSPACE		= 8;
		ENTER			= 13;
		SPACE			= 32;
		COMMA			= 44;

		// add the tagit CSS class.
		el.addClass("tagit");

		// create the input field.
		var html_input_field = "<li class=\"tagit-new\"><input class=\"tagit-input\" type=\"text\" /></li>\n";
		el.html (html_input_field);

		tag_input		= el.children(".tagit-new").children(".tagit-input");
		console.log(el);
		if (el.attr('data-rel-tags').length) {
			var startTags = el.attr('data-rel-tags').split(',');
			for (i=0; i <= startTags.length; i++) {
				if (typeof startTags[i] !== "undefined" && startTags[i].length > 0)
					create_choice(startTags[i], el.find('.tagit-input'), el.attr('data-rel-tag-lang'));	
			}
		}
		
		/*
		$('a.email_close').live('click', function(e) {
			e.stopPropagation()
			$(e.target).parent().parent().parent().parent().remove();
			removeElement($(e.target).parent().parent().parent().parent().attr('rel'));
		});
		*/
		
		// edit a tag/email by clicking it
		$('li.tagit-choice').live('click', function(e) {
			e.preventDefault();
			removeElement($(this).attr('rel'));
			$('input.tagit-input').val($(this).attr('rel'));
			$(this).remove();
			
			console.log('remove 2: ' + $(this).attr('rel')); 
		});
		
		$('input.tagit-input').live('blur', function(event) {
			event.preventDefault();
			var typed = $(this).val();
			typed = typed.replace(/,+$/,"");
			typed = typed.trim();
			
			if (typed != "") {
				if (is_new (typed)) {
					create_choice (typed);
				}
				// Cleaning the input.
				$(this).val("");
			}
		});

		tag_input.on('keypress', function(event) {
			if (event.which == BACKSPACE) {
				if (tag_input.val() == "") {
					// When backspace is pressed, the last tag is deleted.
					$(el).children(".tagit-choice:last").remove();
				}
			}
			// Comma/Space/Enter are all valid delimiters for new tags.
			else if (event.which == COMMA || event.which == SPACE || event.which == ENTER) {
				event.preventDefault();
				var typed = $(event.target).val();//tag_input.val();
				typed = typed.replace(/,+$/,"");
				typed = typed.trim();
				console.log('typed: ' + typed)
				if (typed != "") {
					if (is_new (typed)) {
						create_choice (typed, $(event.target));
						$(event.target).val("");
					} else {
						tag_input.val(typed);
					}
				}
			}
		});
		
		function removeElement(elem)
		{
			var newArrayElements = new Array();
			for (i = 0; i <= tagsChoices[activeLang].length; i++)
			{
				if (
					(typeof tagsChoices[activeLang][i] != "undefined") &&
					(tagsChoices[activeLang][i] != elem) 	
				) {
					newArrayElements.push(tagsChoices[activeLang][i]); 
				}
			}	
			tagsChoices[activeLang] = newArrayElements;
			
			// Remove Hidden Field
			this.targetForm.find('input[rel="' + elem + '"]').remove();
		}
		
		function is_new (value){
			var is_new = true;
			/*
			this.tag_input.parents("ul").children(".tagit-choice").each(function(i){
				n = $(this).children("input").val();
				console.log(n);
				if (value == n) {
					is_new = false;
				}
			})
			*/
			
			/*
			$('span.in_tag_container').each(function(i){
				n = $(this).find("span:eq(0)").html();
				if (value == n) {
					is_new = false;
				}
			})
			*/
			
			//for (i=0; i <= )
			$.each(tagsChoices[activeLang], function(index, tagValue) {
				is_new = (value == tagValue ? false : true);
			});
			
			return is_new;
		}

		function create_choice (value, objElement, __activeLang){
			var __lang = ((typeof __activeLang == "undefined") ? (typeof activeLang != "undefined" ? activeLang : 'en') : __activeLang);
			var blnAddTag = Boolean(((maxInput > 0) && (maxInput > tagsChoices[__lang].length)) || (Number(maxInput) <= 0));
			
			if (blnAddTag) 
			{
				var el = "";
				tagsChoices[__lang].push(value);
				/*
				el  = "<li class=\"tagit-choice\" rel=\"" + value + "\">\n";
				el += "<a href=\"#\" class=\"email_close\">x</a>\n";
				el += value + "\n";
				el += "<input type=\"hidden\" style=\"display:none;\" value=\""+value+"\" name=\"item[tags][]\">\n";
				el += "</li>\n";
				*/
				
				el  = "<li class=\"tagit-choice\" rel=\"" + value + "\">\n";
				el += "<span class='in_tag'><span class='in_tag_container'>\n";
				el += "	<span>" + value + "</span>&nbsp;"
				el += "	<div class='icon_container'><a href='javascript: void(0);' class='icon email_close'></a></div>";
				el += "</span></span></li>";
				// var li_search_tags = this.tag_input.parent();
				//$(el).insertBefore (li_search_tags);
				var li_search_tags = this.tag_input.parents('ul[data-rel-tag-lang="' + __lang + '"]');
				$(el).insertBefore ((typeof objElement != "undefined") ? objElement : li_search_tags.find('input.tagit-input'));
				this.tag_input.val("");
				
				// Add the hidden field
				$('<input/>').attr({
					type: 'hidden',
					name: 'tags[' + __lang  + '][]',
					rel: value		
				}).val(value).prependTo(this.targetForm);
			}
			
			if (maxInput <= tagsChoices[__lang].length)
			{
				$('.tagit-input').hide();
			}	
		}
	};

	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g,"");
	};

})(jQuery);	