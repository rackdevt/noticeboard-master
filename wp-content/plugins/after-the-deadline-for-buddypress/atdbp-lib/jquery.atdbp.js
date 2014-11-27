/* Override ATD defaults so we can add a nice button
 * Use BuddyPress .generic-button class
 */
jQuery.fn.addProofreader.defaults = {
	edit_text_content: '<span class="AtD_edit_button"><div id="atdbp-spellcheck" class="generic-button"><a href="#atd_bp_click">Edit</a></div></span>',
	proofread_content: '<span class="AtD_proofread_button"><div id="atdbp-spellcheck" class="generic-button"><a href="#atd_bp_click">Spellcheck</a></div></span>'
};
