<?php
/**
* The Template for displaying all single classifieds posts.
* You can override this file in your active theme.
*
* @package Classifieds
* @subpackage UI Front
* @since Classifieds 2.0
*/

global $post, $wp_query;
$options = $this->get_options( 'general' );
$field_image = (empty($options['field_image_def'])) ? $this->plugin_url . 'ui-front/general/images/blank.gif' : $options['field_image_def'];

/**
* $content is already filled with the database html.
* This template just adds classifieds specfic code around it.
*/
?>

<script type="text/javascript" src="<?php echo $this->plugin_url . 'ui-front/js/ui-front.js'; ?>" >
</script>

<?php if ( isset( $_POST['_wpnonce'] ) ): ?>
<br clear="all" />
<div id="cf-message-error">
	<?php _e( "Send message failed: you didn't fill all required fields correctly in contact form!", $this->text_domain ); ?>
</div>
<br clear="all" />
<?php elseif ( isset( $_GET['sent'] ) && 1 == $_GET['sent'] ): ?>
<br clear="all" />
<div id="cf-message">
	<?php _e( 'Message is sent!', $this->text_domain ); ?>
</div>
<br clear="all" />
<?php endif; ?>
<div class="cf-post">

	<div class="cf-image">
		<?php
		if(has_post_thumbnail()){
			$thumbnail = get_the_post_thumbnail( $post->ID, array( 300, 300 ) );
		} else {
			$thumbnail = '<img width="300" height="300" title="no image" alt="no image" class="cf-no-image wp-post-image" src="' . $field_image . '">';
		}
		?>
		<a href="<?php the_permalink(); ?>" ><?php echo $thumbnail; ?></a>
	</div>
	<div class="clear"></div>
	<div class="cf-ad-info">
		<table>
			<tr>
				<th><?php _e( 'Listed by:', $this->text_domain ); ?></th>
				<td><?php the_author(); ?></td>
				
			</tr>
			<tr>
				<th><?php _e( 'Type', $this->text_domain ); ?></th>
				<td>
					<?php echo do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); ?>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Category', $this->text_domain ); ?></th>
				<td><?php echo do_shortcode('[ct id="ct_Item_Categ_text_9be6" property="value"]'); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Description', $this->text_domain ); ?></th>
				<td><?php echo do_shortcode('[ct id="ct_Item_Descr_textarea_b138"]'); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Location', $this->text_domain ); ?></th>
				<td>
					<?php echo do_shortcode('[ct id="ct_Location_C_text_3b50" property="value"]'); ?>,&nbsp;
					<?php echo do_shortcode('[ct id="ct_Item_Count_text_f58b" property="value"]'); ?>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Posted On', $this->text_domain ); ?></th>
				<!--<td><?php the_date(); ?></td>-->
				<td><?php echo do_shortcode('[ct id="ct_Post_Date_datepicker_2ddf" property="value"]'); ?></td>
			</tr>
			
			<tr>
				<th><?php _e( 'Status', $this->text_domain ); ?></th>
				<td>
					<?php echo do_shortcode('[ct id="ct_Item_Avail_text_6e7b" property="value"]'); ?> 
				</td>
			</tr>
		</table>
		<div class="clear"></div>
		<!--<div class="cf-custom-block">

			<?php
			//filter the duration field from the output
			echo do_shortcode('[custom_fields_block wrap="table"][ct_filter not="true"]_ct_selectbox_4cf582bd61fa4[/ct_filter][/custom_fields_block]');
			?>
		</div>-->
	</div>
	<div class="clear"></div>

<div id="respond">
<div style="margin-bottom:10px;color:red; font-size:21px; font-weight:500;">Respond to this item here...</div>
<?php 
if ( !is_user_logged_in() ) {
	 printf(__('You must be <a href="%s">logged in</a> to reply to a listed item.'), wp_login_url( get_permalink() ));
} elseif

    (
    is_user_logged_in() and // Force user validation... STUFF
    !empty($GLOBALS['post']) and // Check if a $post exists in globals
    !empty($GLOBALS['current_user']) and // Check if current_user exists in globals
    ($GLOBALS['current_user']->ID == $GLOBALS['post']->post_author) // Match User IDs
	) {
	echo 'You cannot respond to your own listed item';
} else { ?>

	<form method="post" action="#" class="contact-user-btn action-form" id="action-form">
		<input type="submit" name="contact_user" value="<?php _e('Contact Ad Lister', $this->text_domain ); ?>" onclick="classifieds.toggle_contact_form(); return false;" />
	</form>
	<div class="clear"></div>

 <?php
 }
?>	

</div>	

	<form method="post" action="#" class="standard-form base cf-contact-form" id="confirm-form">
		<?php
		global $current_user;

		$name   = ( isset( $current_user->display_name ) && '' != $current_user->display_name ) ? $current_user->display_name :
		( ( isset( $current_user->first_name ) && '' != $current_user->first_name ) ? $current_user->first_name : '' );
		$email  = ( isset( $current_user->user_email ) && '' != $current_user->user_email ) ? $current_user->user_email : '';
		?>
		<div class="editfield">
			<label for="name"><?php _e( 'Name', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
			<input type="text" id="name" name ="name" value="<?php echo ( isset( $_POST['name'] ) ) ? $_POST['name'] : $name; ?>" />
			<p class="description"><?php _e( 'Enter your full name here.', $this->text_domain ); ?></p>
		</div>
		<div class="editfield">
			<label for="email"><?php _e( 'Email', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
			<input type="text" id="email" name ="email" value="<?php echo ( isset( $_POST['email'] ) ) ? $_POST['email'] : $email; ?>" />
			<p class="description"><?php _e( 'Enter a valid email address here.', $this->text_domain ); ?></p>
		</div>
		<div class="editfield">
			<label for="subject"><?php _e( 'Subject', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
			<input type="text" id="subject" name ="subject" value="<?php echo ( isset( $_POST['subject'] ) ) ? $_POST['subject'] : ''; ?>" />
			<p class="description"><?php _e( 'Enter the subject of your enquiry here.', $this->text_domain ); ?></p>
		</div>
		<div class="editfield">
			<label for="message"><?php _e( 'Message', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
			<textarea id="message" name="message"><?php echo ( isset( $_POST['message'] ) ) ? $_POST['message'] : ''; ?></textarea>
			<p class="description"><?php _e( 'Enter the content of your enquiry here.', $this->text_domain ); ?></p>
		</div>

		<div class="editfield">
			<label for="cf_random_value"><?php _e( 'Security image', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
			<img class="captcha" src="<?php echo $this->plugin_url; ?>ui-front/general/cf-captcha-image.php" />
			<input type="text" id="cf_random_value" name ="cf_random_value" value="" size="8" />
			<p class="description"><?php _e( 'Enter the characters from the image.', $this->text_domain ); ?></p>
		</div>

		<div class="submit">
			<p>
				<?php wp_nonce_field( 'send_message' ); ?>
				<input type="submit" class="button confirm" value="<?php _e( 'Send', $this->text_domain ); ?>" name="contact_form_send" />
				<input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onclick="classifieds.cancel_contact_form(); return false;" />
			</p>
		</div>

	</form>

	<div class="clear"></div>
<!--
	<table class="cf-description">
		<thead>
			<tr>
				<th><?php _e( 'Description', $this->text_domain ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php
					//$content is already filled with the database text. This just add classified specfic code around it.
					echo $content;
					?>
				</td>
			</tr>
		</tbody>
	</table>
-->
</div>