<?php
global $bp;
$bp_current_action = $bp->current_action; 
$displayed_username = bp_get_displayed_user_fullname();

//Title when set to user
if($title == 'user' || $title == 'User'){
	if($current_member_mode && $displayed_username != '') $title = $displayed_username . "'s Friends";
	elseif (!$current_member_mode && is_user_logged_in()) $title = bp_get_loggedin_user_fullname() . "'s Friends";
	
	else $title = get_bloginfo('name') . " Friends";
}
else{
	if($current_member_mode && $displayed_username != '') $title = $displayed_username . "'s Friends";
	else $title = $title;
}

//Default Friends
if($default_friends == "") $default_friends = "";
elseif(!is_numeric($default_friends)){
	$user = get_userdatabylogin($default_friends);
	if($user) $default_friends = $user->ID;
	else $default_friends = "User does not exist";
}
else {
	$user = get_userdata($default_friends);
	if($user) /* Great */ ;
	else $default_friends = "User does not exist";
}
?>