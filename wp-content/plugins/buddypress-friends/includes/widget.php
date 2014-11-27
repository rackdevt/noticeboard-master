<?php echo $before_widget; ?>    
<?php if($title) echo $before_title . $title . $after_title; ?>       
<?php
if(is_user_logged_in()){ 
	$members_query = 
	'type=newest&max='. $max_friends .
	'&per_page=' . $max_friends .
	'&max=' . $max_friends;
	if($current_member_mode && $displayed_username != ''){}
	elseif($current_member_mode && $displayed_username == '') $members_query .= '&user_id=' . $default_friends;
	else $members_query .= '&user_id=' . bp_loggedin_user_id();
}
else{
	$members_query = 
	'type=newest&max='. $max_friends .
	'&per_page=' . $max_friends .
	'&max=' . $max_friends;
	if($current_member_mode && $displayed_username != ''){}
	else $members_query .= '&user_id=' . $default_friends;
}
require('members-loop.php');
?>
<?php echo $after_widget; ?>