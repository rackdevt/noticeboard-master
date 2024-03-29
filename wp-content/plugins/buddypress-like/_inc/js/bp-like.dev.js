jQuery(document).ready( function() {
	
	jQuery('.author-box').each(function(){
		var id = jQuery(this).attr('id');
		jQuery(this).append( jQuery(id+' .like-box') );
	});

	jQuery('.like, .unlike, .like_blogpost, .unlike_blogpost').live('click', function() {
		
		var type = jQuery(this).attr('class');
		var id = jQuery(this).attr('id');
		
		jQuery(this).addClass('loading');
		
		jQuery.post( ajaxurl, {
			action: 'activity_like',
			'cookie': encodeURIComponent(document.cookie),
			'type': type,
			'id': id
		},
		function(data) {
			/*the data
			$response = array(
				'success'		=>	'unlike',
				'liketext'		=>	bp_like_get_text( 'like' ),
				'unliketext'	=>	bp_like_get_text( 'unlike' ),
				'likecount'		=>	0,
				'unlikecount'	=>	0
			);
			*/
			
			var j_data = jQuery.parseJSON(data);
			var likecount = j_data.likecount -1;
			if(likecount<0){likecount = 0};
			var unlikecount = j_data.unlikecount -1;
			if(unlikecount<0){unlikecount = 0};
			/*
			if(j_data.success=='like'){
				var htmll = '<a class="bplike-prev-like" id="apl_'+id'"><span>'+j_data.liketext+'</span>: You+('+j_data.likecount+') other(s)';
				jQuery("#"+id).before(htmll);
				jQuery("#"+id).remove();
				
				/*remove the exiting unlike button or 'you unliked' text*/
				/*var unlID = id.replace("like", "unlike");
				jQuery("#" + unlID).remove();
				jQuery("#apul_"+unlID).remove();
				
				/*now append the unlike button after the prev-like text*/
				/*var uhtml = "<a href='#' class='unlike' id='"+unlID+"' title='unlike this item'>"+ j_data.unliketext+"</a>";
				jQuery('#apl_'+id).after(uhtml);
			}
			*/
			// Swap from like to unlike
			if (type == 'like') {
				/*remove the exiting unlike button or 'you unliked' text*/
				var unlID = id.replace("like", "unlike");
				
				jQuery("#" + unlID).remove();
				jQuery("#apul_"+id).remove();
				var uhtml = "<a href='#' class='unlike' id='"+unlID+"' title='" + bp_like_terms_unlike_message+"' >" + j_data.unliketext+"("+j_data.unlikecount+")</a>";
				
				var html = '<a class="bplike-prev-like" id="apl_' +id + '"><span>'+j_data.liketext+'</span>: You+('+likecount+') other(s)';
				
				jQuery("#"+id).before(html);
				jQuery("#"+id).before(uhtml);
				jQuery("#"+id).remove();
				
				
				/*now append the unlike button after the prev-like text*/
				//jQuery('#apl_'+id).after(uhtml);
			} else if (type == 'like_blogpost') {
				/*remove the exiting unlike button or 'you unliked' text*/
				var unlID = id.replace("like", "unlike");
				jQuery("#" + unlID).remove();
				jQuery("#apul_"+id).remove();
				
				var uhtml = "<a href='#' class='unlike_blogpost' id='"+unlID+"' title='"+bp_like_terms_unlike_message+"'>"+ j_data.unliketext+"("+j_data.unlikecount+")</a>";
				var html = '<a class="bplike-prev-like" id="apl_'+id + '"><span>'+j_data.liketext+'</span>: You+('+likecount+') other(s)';
				jQuery("#"+id).before(html);
				jQuery("#"+id).before(uhtml);
				jQuery("#"+id).remove();
				
			} else if (type == 'unlike_blogpost') {
				/*remove the exiting like button or 'you liked' text*/
				var unlID = id.replace("unlike", "like");
				jQuery("#" + unlID).remove();
				jQuery("#apl_"+unlID).remove();
				
				var uhtml = "<a href='#' class='like_blogpost' id='"+unlID+"' title='"+bp_like_terms_like_message+"'>"+ j_data.liketext+"("+j_data.likecount+")</a>";
				var html = '<a class="bplike-prev-like" id="apul_'+unlID+'"><span>'+j_data.liketext+'</span>: You+('+unlikecount+') other(s)';
				jQuery("#"+id).before(uhtml);
				jQuery("#"+id).before(html);
				jQuery("#"+id).remove();
				
			} else {
				/*remove the exiting like button or 'you liked' text*/
				
				var unlID = id.replace("unlike", "like");
				
				jQuery("#" + unlID).remove();
				jQuery("#apl_"+unlID).remove();
				
				var uhtml = "<a href='#' class='like' id='"+unlID+"' title='"+bp_like_terms_like_message+"'>"+ j_data.liketext+"("+j_data.likecount+")</a>";
				var html = '<a class="bplike-prev-unlike" id="apul_'+unlID+'"><span>'+j_data.unliketext+'</span>: You+('+unlikecount+') other(s)';
				jQuery("#"+id).before(uhtml);
				jQuery("#"+id).before(html);
				jQuery("#"+id).remove();
			}
			
			// Nobody else liked this, so remove the 'View Likes'
			if (data == bp_like_terms_like) {
				var pureID = id.replace("unlike-activity-", "");
				jQuery('.view-likes#view-likes-'+ pureID).remove();
				jQuery('.users-who-like#users-who-like-'+ pureID).remove();
			}
			
			// Show the 'View Likes' if user is first to like
			if ( data == bp_like_terms_unlike_1 ) {
				var pureID = id.replace("like-activity-", "");
				jQuery('li#activity-'+ pureID + ' .activity-meta').append('<a href="" class="view-likes" id="view-likes-' + pureID + '">' + bp_like_terms_view_likes + '</a><p class="users-who-like" id="users-who-like-' + pureID + '"></p>');
			}
			
		});
		
		
		return false;
	});

	jQuery('.view-likes').live('click', function() {
		
		var type = jQuery(this).attr('class');
		var id = jQuery(this).attr('id');
		var parentID = id.replace("view-likes", "users-who-like");
	
		if ( !jQuery(this).hasClass('open') ) {
			
			jQuery(this).addClass('loading');
			jQuery.post( ajaxurl, {
				action: 'activity_like',
				'cookie': encodeURIComponent(document.cookie),
				'type': type,
				'id': id
			},
			function(data) {
				jQuery('#' + id).html(bp_like_terms_hide_likes).removeClass('loading').addClass('open');
				jQuery('#' + parentID).html(data).slideDown('fast');
			});
			return false;

		} else {

			jQuery(this).html(bp_like_terms_view_likes).removeClass('loading, open');
			jQuery('#' + parentID).slideUp('fast');
			return false;

		};
	});
	
});