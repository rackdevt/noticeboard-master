<?php

/* Define pages we should load on */
function sfire_atdbp_active_page() {
global $bp;
	if (	bp_is_blog_page() ||
			bp_is_group_forum() || 
			bp_is_profile_edit() ||
			bp_is_messages_component() ||
			bp_is_group_forum_topic_edit() ||
			bp_is_user_activity() ||
			( bp_current_component() == 'forums' )
		)
		return true;
}

/* Load JS */
function sfire_atdbp_enqueue() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('atd-textarea',plugins_url('atd-jquery/scripts/jquery.atd.textarea.js',__FILE__), array('jquery'), '032410');
	wp_enqueue_script('csshttprequest',plugins_url('atd-jquery/scripts/csshttprequest.js',__FILE__), array('atd-textarea'), '032410');
	wp_enqueue_script('atdbuddypress',plugins_url('atdbp-lib/jquery.atdbp.js',__FILE__), array('atd-textarea'), '051711');
}

/* Load CSS */
function sfire_atdbp_css() {
	wp_enqueue_style( 'AtD_style', plugins_url('atd-jquery/css/atd.css',__FILE__), null, '1.0', 'screen' );
	wp_enqueue_style( 'AtD_BP_style', plugins_url('atdbp-lib/atdbp.css',__FILE__), null, '1.0', 'screen' );
}

/* Load Footer
 *
 * Load ATD proofreader
 * Insert our button after the textarea
 * Create an empty span so we can anchor our button click to it
 */
function sfire_atdbp_footer() { ?>
		<script type='text/javascript'>
			jQuery(function() {
				jQuery('textarea').addProofreader();
				jQuery('#AtD_0').insertAfter('textarea');
				jQuery('<span id="atd_bp_click"></span>').insertBefore('textarea');
			});
		</script>
<?php
	
}

/* If we're on an active page let's load everything */
function sfire_atbp_go(){
	if ( sfire_atdbp_active_page() ) {
		add_action('wp_enqueue_scripts','sfire_atdbp_enqueue');
		add_action('wp_print_styles', 'sfire_atdbp_css');
		add_action('wp_footer','sfire_atdbp_footer');
	}
}
add_action('init','sfire_atbp_go');


?>