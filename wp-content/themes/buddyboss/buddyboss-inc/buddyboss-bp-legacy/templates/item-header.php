
<div class="gnb-group-header" >

<?php 


$gnb_user = bp_displayed_user_username();

?>

<div id="item-header-avatar">


<?php bp_group_avatar(); ?>




</div><!-- #item-header-avatar -->
	


<?php 


if (empty($gnb_user)){

	global $bp;
	?>

			<h1 class="gnb-offered-group-title"><a href="/groups/<? echo $bp->groups->current_group->slug; ?>"><?php echo $bp->groups->current_group->name; ?></a></h1>


<div id="item-header-content" class="gnb-offered-group">


 <p><?php 


echo $bp->groups->current_group->description;

function time_ago( $date )
{
    if( empty( $date ) )
    {
        return "No date provided";
    }

    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

    $lengths = array("60","60","24","7","4.35","12","10");

    $now = time();

    $unix_date = strtotime( $date );

    // check validity of date

    if( empty( $unix_date ) )
    {
        return "Bad date";
    }

    // is it future date or past date

    if( $now > $unix_date )
    {
        $difference = $now - $unix_date;
        $tense = "ago";
    }
    else
    {
        $difference = $unix_date - $now;
        $tense = "from now";
    }

    for( $j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++ )
    {
        $difference /= $lengths[$j];
    }

    $difference = round( $difference );

    if( $difference != 1 )
    {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] {$tense}";

}


  ?></p>
  <span class="highlight"><?php echo $bp->groups->current_group->status; ?></span>
	<span class="activity">active <?php $active = $bp->groups->current_group->last_activity; echo time_ago($active); ?></span>

  </div>
	<div id="item-meta">
		

  
</div>
<?php
}
 else { ?>
  <h2 class="user-nicename">@<?php bp_displayed_user_username(); ?></h2>
  

  <span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

<?php } ?>

  <?php do_action( 'bp_before_member_header_meta' ); ?>

</div><!-- #item-header-content -->

<div id="item-buttons" class="profile">

  <?php do_action( 'bp_member_header_actions' ); ?>

</div><!-- #item-buttons -->

</div>