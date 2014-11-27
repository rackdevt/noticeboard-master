<?php if(bp_has_members($members_query)) : ?>
<div class="avatar-block">
  <?php while (bp_members()) : bp_the_member(); ?>
  <?php if($list_type == 'list'){?>
  <a style="line-height:1.4em;" href="<?php bp_member_permalink() ?>">
  <?php bp_member_name() ?>
  </a><br />
  <?php }
  else{?>
  <a href="<?php bp_member_permalink() ?>" title="<?php bp_member_name() ?>">
  <?php bp_member_avatar('type=full&width=' . $av_width . '&height=' . $av_height) ?>
  </a>
  <?php } ?>
  <?php endwhile; ?>
</div>
<?php else: ?>
<div class="no_friends">
  <?php if ($default_friends == "User does not exist" && $bp_current_action =='')  _e( "<strong>Sorry, chosen user does not exist</strong>",'buddypress');
	else _e( "<strong>Sorry, no friends yet</strong>",'buddypress');?>
</div>
<?php endif; ?>