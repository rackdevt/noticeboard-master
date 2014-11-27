<?php

/**
 * This template, which powers the group Send Invites tab when IA is enabled, can be overridden
 * with a template file at groups/single/invite-anyone.php
 *
 * @package Invite Anyone
 * @since 0.8.5
 */

 $user_ID = get_current_user_id();

if ( function_exists( 'bp_post_get_permalink' ) ) { // ugly ugly ugly hack to check for pre-1.2 versions of BP

	add_action( 'wp_footer', 'invite_anyone_add_old_css' );
	?>

	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

		<?php do_action( 'bp_before_group_send_invites_content' ) ?>

			<?php if ( $event != 'create' ) : ?>
				<form action="<?php bp_group_send_invite_form_action() ?>" method="post" id="send-invite-form">
			<?php endif; ?>

				<div class="left-menu">

					<p><?php _e("Search for members to invite:", 'bp-invite-anyone') ?></span></p>

					<ul class="first acfb-holder">
						<li>
							<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
						</li>
					</ul>

					<p><?php _e( 'Select members from the directory:', 'bp-invite-anyone' ) ?> </p>

					<div id="invite-anyone-member-list">
						<ul>
							<?php bp_new_group_invite_member_list() ?>
						</ul>

						<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
					</div>

				</div>

				<div class="main-column">

					<div id="message" class="info">
						<p><?php _e('Select people to invite.', 'bp-invite-anyone'); ?></p>
					</div>

					<?php do_action( 'bp_before_group_send_invites_list' ) ?>

					<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
					<ul id="invite-anyone-invite-list" class="item-list">
					<?php if ( bp_group_has_invites() ) : ?>

						<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

							<li id="<?php bp_group_invite_item_id() ?>">
								<?php bp_group_invite_user_avatar() ?>

								<h4><?php bp_group_invite_user_link() ?></h4>
								<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

								<?php do_action( 'bp_group_send_invites_item' ) ?>

								<div class="action">
									<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'buddypress' ) ?></a>

									<?php do_action( 'bp_group_send_invites_item_action' ) ?>
								</div>
							</li>

						<?php endwhile; ?>
					<?php endif; ?>
					</ul>

					<?php do_action( 'bp_after_group_send_invites_list' ) ?>

				</div>

				<div class="clear"></div>

			<?php if ( $event != 'create' ) : ?>
				<p class="clear"><input type="submit" name="submit" id="submit" value="<?php _e( 'Send Invites', 'buddypress' ) ?>" /></p>
				<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites') ?>
			<?php endif; ?>

			<input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" />

			<?php if ( $event != 'create' ) : ?>
				</form>
			<?php endif; ?>

		<?php do_action( 'bp_before_group_send_invites_content' ); ?>
	<?php endwhile; endif;

} else { // Begin BP 1.2 code

	?>
<h3>Invite new people to join the GNB and the <?php bp_group_name() ?> group</h3>
	<?php do_action( 'bp_before_group_send_invites_content' ) ?>

	<?php if ( invite_anyone_access_test() && !bp_is_group_create() ) : ?>
<p>Want to invite someone to the group who is <b>not yet a member</b> of the site? </p>

<input type="submit" name="invite-anyone-expand-1" id="show-1" value="Invite friends to GNB" />


<!-- invite form - hard coded -->

<form id="invite-anyone-by-email" class="gnb-group-invite-hack" action="http://gnbdev.championsclubcommunity.com/members/nathanielashford/invite-anyone/sent-invites/send/" method="post">

	<h4>Please follow these steps to invite new members to the group:</h4>

	<ol id="invite-anyone-steps">

		<li>
			
			<div class="manual-email">
				<p>
					Enter email addresses below, one per line.									</p>

									<p class="description">You can invite a maximum of 10000 people at a time.</p>
									<textarea name="invite_anyone_email_addresses" class="invite-anyone-email-addresses" id="invite-anyone-email-addresses"></textarea>
			</div>

						
		</li>

		<li>
							<label for="invite-anyone-custom-subject">(optional) Customize the subject line of the invitation email.</label>
					<textarea name="invite_anyone_custom_subject" id="invite-anyone-custom-subject" rows="3" cols="10" >An invitation to join the <?php bp_group_name() ?> group on The Global Noticeboard.</textarea>
					</li>

		<li>
							<label for="invite-anyone-custom-message">(optional) Customize the text of the invitation.</label>
				<p class="description">The message will also contain a custom footer containing links to accept the invitation or opt out of further email invitations from this site.</p>
					<textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" cols="40" rows="10">You have been invited by <?php echo bp_core_get_user_displayname($user_ID); ?> to join the <?php bp_group_name() ?> on the Global Noticeboard.

Visit <?php echo bp_core_get_user_displayname($user_ID); ?>'s profile at <?php echo bp_core_get_user_domain($user_ID); ?></textarea>
			
		</li>
					
		
	</ol> 


<input type="hidden" name="invite_anyone_groups[]" id="invite_anyone_groups-192" value="<? echo bp_get_group_id(); ?>"  />


	<div class="submit">
		<input type="submit" name="invite-anyone-submit" id="invite-anyone-submit" value="Send Invites " />
	</div>


	</form>

	<!--end form -->



	<?php endif; ?>

	<?php if ( !bp_get_new_group_id() ) : ?>
		<form action="<?php invite_anyone_group_invite_form_action() ?>" method="post" id="send-invite-form">
	<?php endif; ?>
	<hr />
	<h3>Invite GNB Members to join the <?php bp_group_name() ?> group</h3>
	<p>Want to invite someone to the group who is <b>already</b> a member of the site? </p>

<input type="submit" name="invite-anyone-expand-2" id="show-2" value="Invite existing GNB members" />
<div class="gnb-exiting-members-invite" >
<h4>Please follow these steps to invite existing GNB members to the group:</h4>
	<div class="left-menu">
		<p><?php _e("Search for members to invite:", 'bp-invite-anyone') ?></p>

		<ul class="first acfb-holder">
			<li>
				<input type="text" name="send-to-input" class="send-to-input gnb-search-members" id="send-to-input" />
			</li>
		</ul>

		<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>

		<?php if ( ! invite_anyone_is_large_network( 'users' ) ) : ?>
			<p><?php _e( 'Select members from the directory:', 'bp-invite-anyone' ) ?></p>

			<div id="invite-anyone-member-list">
				<ul>
					<?php bp_new_group_invite_member_list() ?>
				</ul>
			</div>
		<?php endif ?>
	</div>

	<div class="main-column">

		<div id="message" class="info">
			<p><?php _e('Select people to invite from GNB Members directory', 'buddypress'); ?></p>
		</div>

		<?php do_action( 'bp_before_group_send_invites_list' ) ?>

		<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
		<ul id="invite-anyone-invite-list" class="item-list">
		<?php if ( bp_group_has_invites() ) : ?>

			<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

				<li id="<?php bp_group_invite_item_id() ?>">
					<?php bp_group_invite_user_avatar() ?>

					<h4><?php bp_group_invite_user_link() ?></h4>
					<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

					<?php do_action( 'bp_group_send_invites_item' ) ?>

					<div class="action">
						<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'buddypress' ) ?></a>

						<?php do_action( 'bp_group_send_invites_item_action' ) ?>
					</div>
				</li>

			<?php endwhile; ?>

		<?php endif; ?>
		</ul>

		<?php do_action( 'bp_after_group_send_invites_list' ) ?>

	</div>

	<div class="clear"></div>

	<?php if ( !bp_get_new_group_id() ) : ?>
	<div class="submit">
		<input type="submit" name="submit" id="submit" value="<?php _e( 'Send Invites', 'buddypress' ) ?>" />
	</div>
	<?php endif; ?>
</div>
	<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites') ?>

		<!-- Don't leave out this sweet field -->
	<?php
	if ( !bp_get_new_group_id() ) {
		?><input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" /><?php
	} else {
		?><input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" /><?php
	}
	?>

	<?php if ( !bp_get_new_group_id() ) : ?>
		</form>
	<?php endif; ?>

	<?php do_action( 'bp_after_group_send_invites_content' ) ?>



<?php
}
?>

<script type="text/javascript">
	
$( document ).ready(function() {	

$("#show-1").click(function(e){
  e.preventDefault();
  $(".gnb-group-invite-hack").toggle();
});

$("#show-2").click(function(e){
  e.preventDefault();
  $(".gnb-exiting-members-invite").toggle();
});

});

</script>	
