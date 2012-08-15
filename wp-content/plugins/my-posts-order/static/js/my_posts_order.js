 /**
	* The very first step to use this plugin is to create a section.
  */
	jQuery("#add_section").click(function() {
		get_loading_image('add_edit_section_row');
		jQuery.ajax({
			type: "post",url:"admin-ajax.php", data: { action: 'add_edit_section' },
			success: function(response){
				jQuery('#add_edit_section_row').html(response);
				allEvents(true);//function to load drag-drop functionality after the ajax response
			}
		});
	});

 /**
	* Next thing we can do is to edit section.
  */
	jQuery("#edit_section").click(function() {
		get_loading_image('add_edit_section_row');
		jQuery.ajax({
			type: "post",url:"admin-ajax.php", data: { action: 'edit_section' },
			success: function(response){
				jQuery('#add_edit_section_row').html(response);
			}
		});
	});

 /**
	* We have three main criteria to select post. (1) Posts (2) Category (3) Rss Feed
  */
	jQuery(".content_type").live ('click', function() {
		mpo_get_content_type();
	});

	function mpo_get_content_type() {
		var content_type = jQuery("input[name=content_type]:checked").val();
		var category_id = jQuery('#category_id').val();
		var post_ids = jQuery('#post_ids').val();
		var num_posts = jQuery('#num_posts').val();
		var post_type = jQuery('#post_type').val();
    var is_checked = jQuery('#is_checked').val();
		var feed_url = jQuery('#feed_url').val();
		var num_posts_xml_feed = jQuery('#num_posts_xml_feed').val();
		get_loading_image('content_desc');
		jQuery.ajax({
		  type: "post",url:"admin-ajax.php", data: { action: 'get_content_type', content_type: content_type, post_ids: post_ids, is_checked: is_checked, category_id: category_id, num_posts: num_posts, post_type: post_type, feed_url: feed_url, num_posts_xml_feed: num_posts_xml_feed },
		  success: function(response){
			jQuery('#content_desc').html(response);
			allEvents(true);//function to load drag-drop functionality after the ajax response
		  }
		});
	}


 /**
	* We can edit a section from two places. (1) by selecting edit section radio button (2) by clicking edit link of previously created sections
	* So this function will be called from second option
  */
	function edit_section(section_identifier) {
		jQuery('#edit_section').attr('checked', true)
		get_loading_image('add_edit_section_row');
		jQuery.ajax({
			type: "post",url:"admin-ajax.php", data: { action: 'edit_section', section_identifier: section_identifier },
			success: function(response){
				jQuery('#add_edit_section_row').html(response);
				edit_posts_section(section_identifier);
			}
		});
	}

	function edit_posts_section (val) {
		if (val == '' || typeof(val) == 'undefined')	{
			alert('Please select a valid criteria');
			return false;
		}
		get_loading_image('section_ajax_container');
		jQuery.ajax({
			type: "post",url: "admin-ajax.php",data: { action: 'add_edit_section', section_identifier: val },
			success: function(res){ //so, if data is retrieved, store it in html
				jQuery('#section_ajax_container').html(res);
				allEvents(true);//function to load drag-drop functionality after the ajax response
			}
			});
	}

 /**
	* This function is called when category radio button us clicked.
  */
  jQuery("#cat_reorder").live( 'click', function() {
    var is_checked = jQuery("#cat_reorder").is(':checked');
    if (is_checked) {
      jQuery('#num_posts_row').css({"visibility":"hidden"});;
      mpo_fetch_drag_drop_options();
    } else {
      jQuery('#num_posts_row').css({"visibility":"visible"});;
      jQuery('#show_hide_drag_drop').html('');
    }
	});


 /**
	* Fetches posts according to post type
  */
	jQuery(".post_type").live ('click', function() {
		var post_type = jQuery("input[name=post_type]:checked").val();
		get_loading_image('drag_drop_sel');
		jQuery.ajax({
		  type: "post",url:"admin-ajax.php", data: { action: 'drag_drop_criteria', post_type: post_type },
		  success: function(response){
			jQuery('#drag_drop_sel').html(response);
			allEvents(true);//function to load drag-drop functionality after the ajax response
		  }
		});
	});

 /**
	* Fetches categories list
  */
	jQuery("#categories_list").live ('change', function() {
    var is_checked = jQuery("#cat_reorder").is(':checked');
    if (is_checked) {
      mpo_fetch_drag_drop_options();
    }
	});

	function mpo_fetch_drag_drop_options() {
    var category_id = jQuery('#categories_list').val();
		var post_ids = jQuery('#post_ids').val();
		get_loading_image('show_hide_drag_drop');
		jQuery.ajax({
		  type: "post",url:"admin-ajax.php", data: { action: 'drag_drop_criteria',category_id: category_id, post_ids: post_ids },
		  success: function(response){
			jQuery('#show_hide_drag_drop').html(response);
			allEvents(true);//function to load drag-drop functionality after the ajax response
		  }
		});
  }

 /**
	* This is main function of plugin which validates a form and insert/edit values.
  */
	function validate_form() {
		var main_criteria = jQuery("input[name=main_criteria]:checked").val();
		var section_identifier;
		if (typeof(main_criteria) == 'undefined') {
			alert ('Please select your main criteria');
			return false;
		} else if (main_criteria == 'edit_section') {
			var section_identifier = jQuery('#all_sections').val();
			if (section_identifier == '') {
				alert('Please select a section name');
				return false;
			}
		}
		if (jQuery('#specific_content').is(':checked') == true) {
			if (fetch_selected_ids()) {
				var post_ids = jQuery('#selected_entries').val();
				var post_type = jQuery("input[name=post_type]:checked").val();
				if (typeof(post_type) == 'undefined') {
					alert ('Please select post type');
					return false;
				}
			} else {
				return false;
			}
		} else if (jQuery('#category_radio').is(':checked') == true) {
			if (check_textfield('categories_list', 'a category')) {
        var is_checked = jQuery("#cat_reorder").is(':checked');
        if (is_checked) {
          if (fetch_selected_ids()) {
            var post_ids = jQuery('#selected_entries').val();
          } else {
            return false;
          }
          is_checked = 1;
        } else {
          var length = jQuery('#no_posts_category').val();
        }
        var category_id = jQuery('#categories_list').val();
			} else {
				return false;
			}
		} else if (jQuery('#xml_feed').is(':checked') == true) {
			if (check_textfield('xml_feed_url', 'url of xml feed'))  {
				if (checkUrl('xml_feed_url') ) {
					var feed_url = jQuery('#xml_feed_url').val();
					var length = jQuery('#no_posts_xml_feed').val();
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			alert('Please select content type');
			return false;
		}
		if (check_textfield('section_name', 'Section name') ) {
			var section_name = jQuery('#section_name').val();
// 			var illegalChars = /^[A-Z0-9 _]*$/; // allow only letters AND numbers
// 			if (illegalChars.test(section_name)) {
// 				alert('Only letters or numbers are allowed');
// 				return false;
// 			}
		} else {
			return false;
		}
		var radio_val = jQuery("input[name=content_type]:checked").val();
		var nonce_field = jQuery('#nonce_field').val();
		get_loading_image('add_edit_section_row');
		jQuery.ajax({
			type: "post",url:"admin-ajax.php", data: { action: 'save_section_data', content_type: radio_val,
				category_id: category_id, post_ids:post_ids, post_type: post_type, is_checked: is_checked, length:length, feed_url:feed_url,
				_ajax_nonce: nonce_field, section_name: section_name, section_identifier: section_identifier },
			success: function(html){ //so, if data is retrieved, store it in html
				var res = html.split('~#$');
				jQuery('#add_edit_section_row').hide();
				alert(res[0]);
				window.location.href = 'admin.php?page=my-posts-order';
			}
		});
	}

 /**
	* Asks for confirmation before deletion
  */
	function confirm_delete() {
		var section_identifier = jQuery('#all_sections').val();
		section_delete(section_identifier);

	}

	function section_delete(section_identifier) {
		var confirm_box = confirm('Are you sure you want to delete');
	  if (confirm_box) {
			if (section_identifier == '' || typeof(section_identifier) == 'undefined') {
				alert('Please select a section name');
				return false;
			}
			get_loading_image('add_edit_section_row');
			jQuery.ajax({
				type: "post",url:"admin-ajax.php", data: { action: 'delete_section_data', section_identifier: section_identifier },
				success: function(res) {
					if (res) {
						jQuery('#add_edit_section_row').hide();
						alert(res);
						window.location.href = 'admin.php?page=my-posts-order';
					} else {
						alert(res);
					}
				}
			});
	  }
	}

 /**
	* Checks for empty field
  */
	function check_textfield (id, mess) {
		var text = jQuery.trim(jQuery('#' + id).val());
		if (text == '') {
			alert('Please enter ' + mess);
			return false;
		} else {
			return true;
		}
	}




	/**
	* Gives ordered posts' id
  */
	function fetch_selected_ids () {
		var entryIdString = '';
		jQuery('#tbody_selections > tr[id^="entry_"] > td[id^="action_entry_"]').each(
			function () {
				var entryId = this.id.split('_');
				entryIdString += entryId[(entryId.length - 1)] + ',';
			}
		);
		if (jQuery.trim(entryIdString) == '') {
			alert('Please make some selections.');
			return false;
		} else {
			jQuery('#selected_entries').val((entryIdString.substring(0, (entryIdString.length - 1))));
			return true;
		}
	}

	/**
	* Deprecated.
  */
	function getUrlVars() {
		var vars = [], hash;alert(window.location.href)
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	/**
	* Next thing we can do is to edit section.
  */
	function multiple_sel_box_val() {
		var selected_val_array = [];
		jQuery('#categories_list :selected').each(function(i, selected) {
		selected_val_array[i] = jQuery(selected).val();
		});
	}

	/**
	* Deprecated.
  */
	function checkUrl(theUrl){ return true;
		var url = jQuery('#'+ theUrl).val();///(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)/g
		///(https?://)?(www\.)?([a-zA-Z0-9_%]*)\b\.[a-z]{2,4}(\.[a-z]{2})?((/[a-zA-Z0-9_%]*)+)?(\.[a-z]*)?/g
		///^(?:(?P<scheme>\w+)://)?(?:(?P<login>\w+):(?P<pass>\w+)@)?(?P<host>(?:(?P<subdomain>[\w\.]+)\.)?(?P<domain>\w+\.(?P<extension>\w+)))(?::(?P<port>\d+))?(?P<path>[\w/%]*/(?P<file>\w+(?:\.\w+)?)?)?(?:\?(?P<arg>[\w=&]+))?(?:#(?P<anchor>\w+))?/g
		var pattern = '^((ht|f)tp(s?)\:\/\/|~/|/)?([\w]+:\w+@)?([a-zA-Z]{1}([\w\-]+\.)+([\w]{2,5}))(:[\d]{1,5})?((/?\w+/)+|/?)(\w+\.[\w]{3,4})?((\?\w+=\w+)?(&\w+=\w+)*)?';
		if(url.match(pattern) ){
			return true;
		} else {
	     alert("Please enter a valid url.");
			return false;
		}
	}


 /**
	* Removes default text on focus.
  */
	jQuery('#search_posts_text').live('focus', function() {
		if (jQuery(this).val() == 'Search Posts..' ) {
			jQuery(this).val('');
		}
	});

 /**
	* Displays default text on focus out.
  */
	jQuery('#search_posts_text').live ('blur', function() {
		var search_text = jQuery.trim(jQuery('#search_posts_text').val());
		if (search_text == '') {
			jQuery(this).val('Search Posts..');
		}
	});

	/**
	* Fires search query
  */
	jQuery('#search_posts_button').live('click', function() {
		var search_text = jQuery.trim(jQuery('#search_posts_text').val());
		if (search_text == '' || search_text == 'Search Posts..') {
			alert('Please enter search text');
			return false;
		} else {
			get_loading_image('specific_content_container');
			jQuery.ajax({
			type: "post",url: ajaxurl,data: { action: 'search_posts', search_str: search_text },
			success: function(res){ //so, if data is retrieved, store it in html
				jQuery('#specific_content_container').html(res);
				allEvents(true);//function to load drag-drop functionality after the ajax response
			}
			});
		}
	});


	/**
	* Gets loading image.
  */
	function get_loading_image(id) {
    jQuery('#' + id).html('<img  alt="loading.." style="padding-left:10%;" src="' + MPO_IMAGES_PATH + 'loadingAnimation.gif" />');
  }


 /**
	* Main section from where drag and drop functionality starts
  */

	jQuery(document).ready(
		function() {
			allEvents(false);
		}
	);

	function allEvents(unBind) {
		if (unBind) {
			jQuery('#tbody_entries > tr[id^="entry_"] > td[id^="action_entry_"]').unbind('click');
			jQuery('#tbody_selections > tr[id^="entry_"] > td[id^="action_entry_"]').unbind('click');
		}
		jQuery("#table_selections").tableDnD();

		try {
			jQuery('#tbody_entries > tr[id^="entry_"] > td[id^="action_entry_"]').click(
				function () {
					jQuery('#msg_selections').remove();
					jQuery('#no_content').remove();
					jQuery(this).html('Remove');
					jQuery(this).parent().clone().prependTo("#tbody_selections");
					jQuery(this).parent().remove();
					var entriesCount = 0;
					jQuery('#tbody_entries > tr[id^="entry_"]').each(
						function () {
							++entriesCount
						}
					);
					if (!entriesCount) {
						jQuery('#tbody_entries').html('<tr id="msg_entries"><td>No post found.</td></tr>');
					} else {
						jQuery('#msg_entries').remove();
					}
					allEvents(true);
				}
			);

			jQuery('#tbody_selections > tr[id^="entry_"] > td[id^="action_entry_"]').click(
				function () {
					jQuery('#msg_entries').remove();
					jQuery(this).html('Add');
					jQuery(this).parent().clone().prependTo("#tbody_entries");
					jQuery(this).parent().remove();
					var entriesCount = 0;
					jQuery('#tbody_selections > tr[id^="entry_"]').each(
						function () {
							++entriesCount
						}
					);
					if (!entriesCount) {
						jQuery('#tbody_selections').append('<tr id="msg_selections"><td>No selection made.</td></tr>');
					} else {
						jQuery('#msg_selections').remove();
					}
					allEvents(true);
				}
			);

			jQuery('td[id^="action_entry_"]').hover(
				function () {
					jQuery(this).css({cursor:"pointer"});
				},
				function () {
					jQuery(this).css({cursor:"auto"});
				}
			);


			if (jQuery.trim(jQuery('#tbody_entries').html()) == '') {
				jQuery('#tbody_entries').html('<tr id="msg_entries"><td>No post found.</td></tr>');
			}

			if (jQuery.trim(jQuery('#tbody_selections').html()) == '') {
				jQuery('#tbody_selections').html('<tr id="msg_selections"><td>No selection made.</td></tr>');
			}


			jQuery('span[id^="code_link_"]').hover(
				function () {
					jQuery(this).css({cursor:"pointer"});
				},
				function () {
					jQuery(this).css({cursor:"auto"});
				}
			);

		} catch (e) {
			alert(e);
		}
	}