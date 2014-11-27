<?php

global $wpdb;

$current_user = wp_get_current_user();

$check_exit = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bp_activity WHERE user_id ='$current_user->ID' AND type = 'Member_review' AND usercheck='".bp_displayed_user_id()."'");

$tmp = $wpdb->get_results("SELECT id, content, star FROM {$wpdb->prefix}bp_activity WHERE user_id ='$current_user->ID' AND type = 'Member_review' AND usercheck='".bp_displayed_user_id()."'");
$check_content = array();
$check_content_ids = array();
foreach ($tmp as $row) {
    $check_content_ids[] = $row->id;
    $check_content[$row->id] = $row;
}

$check_content_loop = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bp_activity WHERE  type = 'Member_review' AND usercheck='".bp_displayed_user_id()."'");

$options = get_option('reviews_options');

if (is_user_logged_in()) {
    $check_total_set = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}bp_activity WHERE user_id ='$current_user->ID' AND type = 'Member_review' ");

    if ($options['limit'] == 0) {
        $show_check = true;
    } else {
        $show_check = false;
    }
    $check_show = (bp_displayed_user_id() == $current_user->ID and $options['Prevent'] == "Prevent");

    if ($check_show) {
        echo "<p style='padding-bottom:5px'>You can't review yourself</p>";
    } else {
        ?>
<form class="review-member-form whats-new-form-member" name="whats-new-form" id="whats-new-form " method="post" action="">
    <div id="whats-new-avatar">
        <a href="<?= get_bloginfo('home') ?>/members/<?= $current_user->user_login ?>/">
            <?= get_avatar($current_user->ID, 60, $default, $current_user->display_name) ?>
        </a>
    </div>
        <?php
        if (count($check_exit) != 0) {
            if ($options['limit'] == 0) {
                $maxss = 10000;
            } else {
                $maxss = $options['limit'];
            }
            $args = array(
                'user_id' => $check_exit[0]->user_id,
                'action'  => 'Member_review',
                'in'      => $check_content_ids,
                'sort'    => 'ASC',
                'max'     => $maxss
                );
            if (bp_has_activities($args)) {
                while (bp_activities()) {
                    bp_the_activity();

                    $demsss = bp_get_activity_id();
                    ?>
    <div class="already-rated">
        <h5><?php printf(__( "You rated %s "), bp_core_get_username(bp_displayed_user_id())); ?></h5>
        <style type="text/css">
            #activity-<?= $check_exit[0]->id ?> .hidencheck { display: none; }
        </style>
        <blockquote style="padding-bottom: 20px">
            <style type="text/css">.already-rated p .ratingtop { display: none; }</style>
            <p><?= $check_content[$demsss]->content ?></p>
            <div class="rest-stars">
                <span class="ratingtop" style="float:right">
                    <?php
                    for ($dem = 1; $dem < 6 ; $dem ++) {
                        if ($dem <= $check_content[$demsss]->star) {
                            echo '<img alt="1 star" src="'.DEPROURL.'/images/star.png">';
                        } else {
                            echo '<img alt="1 star" src="'.DEPROURL.'/images/star_off.png">';
                        }
                    }
                    ?>
                </span><br>
            <p style="float:right;margin-top:5px;"><?php bp_activity_delete_link(); ?></p>
            </div>
        </blockquote>
    </div>
                    <?php
                }
            }
        }

        if (count($check_exit) < $options['limit'] or $options['limit'] == 0) {
            ?>
    <h5>What are your thoughts on <?= bp_core_get_username(bp_displayed_user_id()) ?>, <?= $current_user->display_name ?>?</h5>

    <div id="whats-new-content">
        <div id="whats-new-textarea">
            <div>
                <textarea value="" id="whats-new" name="review_member_content" style="display: inline-block; height: 50px;"></textarea>
            </div>
        </div>

        <div id="review-rating" >
            Rate it: <img src="<?= DEPROURL ?>/images/star_off.png" class="star" id="star1">
                     <img src="<?= DEPROURL ?>/images/star_off.png" class="star" id="star2">
                     <img src="<?= DEPROURL ?>/images/star_off.png" class="star" id="star3">
                     <img src="<?= DEPROURL ?>/images/star_off.png" class="star" id="star4">
                     <img src="<?= DEPROURL ?>/images/star_off.png" class="star" id="star5">
        </div>
        <div id="whats-new-options" style="height: 40px;">
            <div id="whats-new-submit">
                <input type="hidden" value="0" id="rating" name="rating_member">
                <input type="hidden" value="<?= bp_displayed_user_id() ?>" id="rating_id" name="rating_member_id">
                <input type="hidden" value="<?= bp_core_get_username(bp_displayed_user_id()) ?>" id="rating_name" name="rating_member_name">
                <span class="ajax-loader"></span>
                <input type="submit" value="Post My Review" id="whats-new-submit" name="review_member_submit">
            </div>
            <?php
            if ($options['anonymous']) {
                ?>
            <div id="prorevs-anonymous">
                <label>Post as anonymous: <input type="checkbox" name="anonymous" value="1"></label>
            </div>
                <?php
            }
            ?>
        </div>
    </div>
            <?php
        }
        ?>
</form>
        <?php
    }
}

if (count($check_content_loop) > 0) {
    ?>
    <div class="pagination" id="pag-top" style="margin-bottom:20px">
        <div id="group-dir-count-top" class="pag-count">
            Viewing reviews 1 to <?= count($check_content_loop) ?>  (of <?= bp_core_get_username(bp_displayed_user_id()) ?> )
        </div>
        <div id="group-dir-pag-top" class="pagination-links">
        </div>
    </div>
    <?php
} else {
    ?>
    <div id="message" class="info">
        <p><?php printf(__("There are no reviews yet. Why not <a href=\"#\">be the first to write one</a>?", 'bpgr')); ?></p>
    </div>
    <?php
}
?>
<div class="activity show_important">
    <ul id="activity-stream" class="activity-list item-list ">
<?php
foreach ($check_content_loop as $check_content_loopss) {
    $depro_idcheck = $check_content_loopss->id;
    $user_info_id = $check_content_loopss->user_id;
    $user_info_lastcheck = get_userdata($user_info_id);

    $args = array(
        'in' => array($check_content_loopss->id),
        'user_id' => $check_content_loopss->user_id
        );

    if (bp_has_activities($args)) {
        while (bp_activities()) {
            bp_the_activity();
            ?>
        <li id="activity-<?= $depro_idcheck ?>" class="member review">
            <?php
            if ($check_content_loopss->anonymous) {
                ?>
            <div class="activity-avatar">
                <?= get_avatar(NULL, 48) ?>
            </div>
                <?php
            } else {
                ?>
            <div class="activity-avatar">
                <a href="<?= get_bloginfo('home') ?>/members/<?= $user_info_lastcheck->user_login ?>/">
                    <?= get_avatar($check_content_loopss->user_id, 48, $default, "") ?>
                </a>
            </div>
                <?php
            }
            ?>
            <div class="activity-content">
                <div class="activity-header">
                    <span class="ratingtop">
            <?php
            for ($dem = 1; $dem < 6; ++$dem) {
                if ($dem <= $check_content_loopss->star) {
                    echo '<img alt="1 star" src="'.DEPROURL.'/images/star.png">';
                } else {
                    echo '<img alt="1 star" src="'.DEPROURL.'/images/star_off.png">';
                }
            }
            ?>
                    </span> By
            <?php
            if ($check_content_loopss->anonymous) {
                ?>
                    Anonymous
                <?php
            } else {
                ?>
                    <a href="<?= get_bloginfo('home') ?>/members/<?= $user_info_lastcheck->user_login ?>/"><?= $user_info_lastcheck->user_login ?></a> <?= bp_core_time_since($check_content_loopss->date_recorded) ?>
                <?php
            }
            ?>
                    <span class="hidencheck"><span style='color:red'><!-- only Administrator see this button --></span><?php if (current_user_can('administrator')) { bp_activity_delete_link(); } ?></span>
                </div>
                <div class="activity-inner delete_star">
                    <p><?= $check_content_loopss->content ?></p>
                </div>
                <style type="text/css">.delete_star .ratingtop { display: none; }</style>
                <div class="activity-meta">
            <?php
            if (is_user_logged_in()) {
                if (bp_activity_can_comment()) {
                    ?>
                    <a href="<?php bp_activity_comment_link(); ?>" class="acomment-reply" id="acomment-comment-<?= $depro_idcheck ?>"><?php _e('Comment', 'buddypress'); ?> (<span><?php bp_activity_comment_count(); ?></span>)</a>
                    <?php
                }
                if ($options['flag']) {
                    ?>
                    <a href="#" class="prorevs-report-review" data-id="<?= $check_content_loopss->id ?>">Report</a>
                    <span class="prorevs-report-form" data-id="<?= $check_content_loopss->id ?>" style="display: none">
                        Reason: <input type="text" id="prorevs-reason-<?= $check_content_loopss->id ?>">
                        <a href="#" class="prorevs-report-send">send</a>
                        <a href="#" class="prorevs-report-cancel">cancel</a>
                    </span>
                    <?php
                }
            }
            do_action('bp_activity_entry_meta');
            ?>
                </div>
            </div>
            <div class="activity-comments">
            <?php
            bp_activity_comments();
            if (is_user_logged_in()) {
                ?>
                <form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?= $depro_idcheck ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
                    <div class="ac-reply-avatar"><?php bp_loggedin_user_avatar('width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT); ?></div>
                    <div class="ac-reply-content">
                        <div class="ac-textarea">
                            <textarea id="ac-input-<?= $depro_idcheck ?>" class="ac-input" name="ac_input_<?= $depro_idcheck ?>"></textarea>
                        </div>
                        <input type="submit" name="ac_form_submit" value="<?php _e('Post', 'buddypress'); ?>" /> &nbsp; <?php _e('or press esc to cancel.', 'buddypress'); ?>
                        <input type="hidden" name="comment_form_id" value="<?= $depro_idcheck ?>" />
                    </div>
                <?php
                do_action('bp_activity_entry_comments');
                wp_nonce_field('new_activity_comment', '_wpnonce_new_activity_comment');
                ?>
                </form>
                <?php
            }
            ?>
            </div>
        </li>
            <?php
        }
    }
}
?>
    </ul>
</div>
