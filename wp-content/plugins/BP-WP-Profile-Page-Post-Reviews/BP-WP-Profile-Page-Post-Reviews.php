<?php
/*
Plugin Name: BP-WP Profile Reviews
Version: 1.3
Description: BP-WP Profile Reviews has two functions: (1) create a review section for member profiles in Buddypress (2) convert Wordpress comments on a post or page into reviews with star ratings.
Author: Spoonjab
Author URI: http://spoonjab.com
Plugin URI: http://spoonjab.com/bp-wp-profile-reviews/

Copyright (C) 2012 Spoonjab

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software") to use the Software. Permission is NOT granted to modify, merge, publish, distribute, sublicense, and/or sell copies of the Software. Persons to whom the Software is furnished, are subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

ob_start();

if (!defined('DEPROURL')) {
    define('DEPROURL', WP_PLUGIN_URL . '/BP-WP-Profile-Page-Post-Reviews/');
}

if (!defined('PROREVS_ROOT')) {
    define('PROREVS_ROOT', dirname(__FILE__));
}

add_action('wp_head', 'insert_js_depro', 1);

function insert_js_depro()
{
    ?>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
<script type="text/javascript">
    window.document.onkeydown = function (e) {
        if (!e)
            e = event;
        if (e.keyCode == 116) {
            return false;
        }
    }

    jQuery(document).ready(function() {
        var jq = jQuery;

        // Make the Read More on the already-rated box have a unique class
        var arm = jq('.already-rated .activity-read-more');
        jq(arm).removeClass('activity-read-more').addClass('already-rated-read-more');

        jq('.star').mouseover( function() {
            var num = jq(this).attr('id').substr( 4, jq(this).attr('id').length );
            for ( var i=1; i<=num; i++ )
                jq('#star' + i ).attr( 'src', "<?php echo DEPROURL?>images/star.png" );
        });

        jq('div#review-rating').mouseout( function() {
            for ( var i=1; i<=5; i++ )
                jq('#star' + i ).attr( 'src', "<?php echo DEPROURL?>images/star_off.png" );
        });

        jq('.star').click( function() {
            var num = jq(this).attr('id').substr( 4, jq(this).attr('id').length );
            for ( var i=1; i<=5; i++ )
                jq('#star' + i ).attr( 'src', "<?php echo DEPROURL?>images/star_off.png" );
            for ( var i=1; i<=num; i++ )
                jq('#star' + i ).attr( 'src', "<?php echo DEPROURL?>images/star.png" );

            jq('.star').unbind( 'mouseover' );
            jq('div#review-rating').unbind( 'mouseout' );

            jq('input#rating').attr( 'value', num );
        });

        jq('.already-rated-read-more a').live('click', function(event) {
            var target = jq(event.target);

            var link_id = target.parent().attr('id').split('-');
            var a_id = link_id[3];

            var a_inner = '.already-rated blockquote p';

            jq(target).addClass('loading');

            jq.post(
                ajaxurl,
                {
                    action: 'get_single_activity_content',
                    'activity_id': a_id
                },
                function(response) {
                    jq(a_inner).slideUp(300).html(response).slideDown(300);
            });

            return false;
        });

        jq('#whats-new-submit').click(function(){
            if(jq('input#rating').val() == 0){
                alert('Please choose a star rating!');
                return false;
            }
        });

        jq('#submit').click(function(){
            if(jq('input#rating').val() == 0){
                alert('Please Rate for This page/post !!!');
                return false;
            }
        });
    });
</script>
    <?php
}

//
// Implements the options page
//

add_action('admin_menu', 'prorevs_admin_menu');

function prorevs_admin_menu()
{
    add_menu_page('Reviews Settings', 'Profile Reviews', 'manage_options', 'profile-reviews', 'prorevs_options_page', DEPROURL.'/images/star.png');

    add_action( 'admin_init', 'prorevs_admin_init' );

    function prorevs_admin_init()
    {
        register_setting( 'reviews_options_page', 'reviews_options');
    }

    function prorevs_options_page()
    {
        ?>
<div class="wrap">
    <h2>BP-WP Profile Reviews - Options</h2>
    <div class="postbox" style="width: 850px;">
        <h3 style="padding: 10px;">About BP-WP Profile Reviews v.1.3</h3>
        <div style="padding:10px 10px; margin-top: -12px; background:#ffffff;">
            This plugin is an adaptation of <a href="http://wordpress.org/extend/plugins/bp-group-reviews/" target="_blank">BP Group Reviews</a>. This plugin has two functions that can be used together or separately:<br>
            <ol>
                <li><strong>Buddypress Member Profile Reviews</strong> - This option enables a Reviews tab and button for Buddypress Member Profiles. Members can leave text and star reviews on other members. The average star rating, number of reviews, and time is displayed. Reviews can only be deleted by the author or an administrator. The amount and average star ratings are displayed on the Member Directory. Reviews are fed into the activity stream. You can also reply and comment on reviews.</li>
                <li><strong>Wordpress/Buddypress Post and Page Reviews</strong> - Post and Page comments are globally transformed into reviews with star ratings. Only a name, comment text box, and star rating show (no other fields like website, email, etc...). If enabled, existing comments will show zero stars. This option can be used whether you have Buddypress installed or not.  The average star rating is shown at the top of the comments. This works for Buddpress and Wordpress posts and pages alike. You can enable or disable ratings at the bottom of the draft. Reviews are fed into the activity stream. Reviews can be deleted in the Comments section of the admin panel. Star rating is displayed in the admin Comments section.</li>
            </ol>
        </div>
        <div style="padding:10px; background:#eaf2fa;">
            Plugin Website: <a href="http://spoonjab.com/bp-wp-profile-reviews/?utm_source=BP%2BPlugin&utm_medium=admin%2Bpanel&utm_campaign=PluginBackend" target="_blank">http://spoonjab.com/bp-wp-profile-reviews/</a><br>
            Author Website: <a href="http://spoonjab.com/?utm_source=BP%2BPlugin&utm_medium=admin%2Bpanel&utm_campaign=PluginBackend" target="_blank">http://spoonjab.com</a><br>
            Support Email: <a href="mailto:spoon@spoonjab.com">spoon@spoonjab.com</a><br>
            Support Forums: <a href="http://support.spoonjab.com">support.spoonjab.com</a><br><br>
            <div style="color:#BE5409;font-weight:bold;">
                If you like this plugin, please donate. I pay to have it developed at my own expense; future requests and development can be accommodated through donations. If you want to make a request, email <a href="mailto:spoon@spoonjab.com">spoon@spoonjab.com</a>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_donations">
                    <input type="hidden" name="business" value="twistedmetal2002@yahoo.com">
                    <input type="hidden" name="lc" value="US">
                    <input type="hidden" name="item_name" value="Spoonjab">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
        </div>
    </div>
    <form action="options.php" method="post">
<?php
        settings_fields( 'reviews_options_page' );
        $options = get_option('reviews_options'); 
?>
        <style type="text/css">
            .options label{
                float:left;
            }
            .options input{
                float:right;
            }
        </style>
        <div class="options" style="width: 850px">
            <br>
            <label><strong>Enable Buddpress reviews on profile pages?</strong>&nbsp;<input name="reviews_options[profile]" <?= $options['profile'] == 'profile' ? 'checked' : '' ?> type="checkbox" id="textfield" value="profile" size="50" /></label><br>- This adds a Reviews tab and button to all member profiles<br><br>
            <div style="width: 750px; margin-left: 30px;">
                <label><strong>Number of multiple Buddypress Profile reviews per user:</strong>&nbsp;<input name="reviews_options[limit]" style="margin-top: -8px;"  type="text" id="text_check" value="<?= isset($options['limit']) ? $options['limit'] : '1' ?>" size="1" /></label><br>- Allows for multiple reviews from one user on same member. 0 = unlimited<br><br>
                <label><strong>Prevent members from rating their own Buddypress Profile?</strong>&nbsp;<input name="reviews_options[Prevent]" <?= $options['Prevent'] == 'Prevent' ? 'checked' : '' ?> type="checkbox" id="textfield" value="Prevent"  /></label><br><br>
                <label><strong>Allow members to publish their reviews as anonymous?</strong>&nbsp;<input name="reviews_options[anonymous]" <?= $options['anonymous'] == '1' ? 'checked' : '' ?> type="checkbox" id="textfield" value="1" /></label><br><br>
                <label><strong>Allow users to report (flag) profile reviews?</strong>&nbsp;<input name="reviews_options[flag]" <?= $options['flag'] == '1' ? 'checked' : '' ?> type="checkbox" value="1" /></label><br><br>
                <label><strong>&nbsp&nbsp&nbsp&nbsp&nbspUser reports on reviews/ratings will be sent to:</strong>&nbsp;<input name="reviews_options[admin_emails]" style="margin-top: -8px;"  type="text" value="<?= isset($options['admin_emails']) ? $options['admin_emails'] : get_settings('admin_email') ?>" size="50" /></label><br>&nbsp&nbsp&nbsp&nbsp&nbsp- Separate multiple email addresses using "," (comma) character<br><br>
                <label><strong>Shortcodes:</strong><br>- Show highest average profile review ratings: <strong>[prorevs_users_by_rating limit=10]</strong><br>- Show highest amount of profile review ratings: <strong>[prorevs_users_by_review_count limit=10]</strong><br><br>

            </div>
            <br><br>
            <hr align="left" style="width: 850px;"><br>
            <label><strong>Enable Wordpress reviews for pages/posts?</strong>&nbsp;<input name="reviews_options[postpage]" <?= $options['postpage'] == 'postpage' ? 'checked' : '' ?> type="checkbox" id="textfield" value="postpage" size="50" /></label><br>- This adds a star rating to all comment fields on posts and pages. Box must enabled on each post/page draft to be visible<br><br>
        </div>
        <input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
</div>
        <?php
    };
};

$options = get_option('reviews_options');

if ($options['postpage'] == 'postpage') {
    add_action('add_meta_boxes', 'depro_myplugin_add_custom_box');
    add_action('save_post', 'depro_myplugin_save_postdata');

    function depro_myplugin_add_custom_box()
    {
        add_meta_box(
            'depro_myplugin_sectionid',
            __('Reviews', 'deprohoang_setreviews'),
            'depro_myplugin_inner_custom_box',
            'post' 
        );
        add_meta_box(
            'depro_myplugin_sectionid',
            __('Reviews', 'deprohoang_setreviews'),
            'depro_myplugin_inner_custom_box',
            'page'
        );
    }

    function depro_myplugin_inner_custom_box($post)
    {
        global $post;
        $post_id = $post->ID;
        wp_nonce_field(plugin_basename(__FILE__), 'myplugin_noncename');
        echo '<label for="setreviews_onoroff">';
        _e("Show Review on this page ? ", 'deprohoang_setreviews');
        echo '</label> ';
        $checkmetakey = get_post_meta($post_id, '_reviews', true);
        if ($checkmetakey == "onoff") {
            $checked = "checked";
        } else {
            $checked = "";
        }
        echo '<input type="checkbox" id="setreviews_onoroff" name="setreviews_onoroff" value="onoff" '.$checked.' />';
    }

    function depro_myplugin_save_postdata($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!wp_verify_nonce($_POST['myplugin_noncename'], plugin_basename(__FILE__)))
            return;
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return;
        }

        $mydata = $_POST['setreviews_onoroff'];

        add_post_meta($post_id, '_reviews', $mydata, true);
        update_post_meta($post_id, '_reviews', $mydata);
    }

    add_action('comments_template', 'prorevs_template_comment');

    function prorevs_template_comment()
    {
        ?>
<script type="text/javascript">
    window.document.onkeydown = function(e)
    {
        if (!e)
            e = event;
        if (e.keyCode == 27) {
            var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

            if (!temp || !respond)
                return;
            t.I('comment_parent').value = '0';
            temp.parentNode.insertBefore(respond, temp);
            temp.parentNode.removeChild(temp);
            this.style.display = 'none';
            this.onclick = null;

            return false;
        }
    }

    jQuery(document).ready(function() {
        jQuery('.form-submit #submit').val('Post');
        var check = 0;
        jQuery('.comment-reply-link').click(function() {
            check++;
            jQuery('.form-submit #submit').val('Post');
            if(check ==1){
                jQuery("#submit").after(" <p id='hidenow' style='font-size:10px;color:#a1a1a1;position:relative;left:-50px;top:5px'>or press ESC to cancel !</p>");
            }
        })
    });
</script>
<style type="text/css">
    li.depth-1 .comment-meta, .comment-form-email, .comment-form-url {
        display: none !important;
    }

    ul.children .comment-meta {
        display: block !important;
    }

    ol.commentlist #review-rating {
        display: none !important;
    }

    #comments h3, #cancel-comment-reply-link {
        display: none !important;
    }

    #submit {
        float: right;
        margin-right: 80px;
        position: relative;
        z-index: 1000;
    }

    .commentlist #submit{
        float: left !important;
    }

    .commentlist #hidenow{
        display: block !important;
    }

    #respond #hidenow{
        display: none;
    }

    .comment-notes{
        display: none;
    }

    ol.commentlist li.comment{
        padding-top: 10px;
    }
</style>
<?php
        global $post;
        $post__id = $post->ID;
        global $wpdb;
        $rows = $wpdb->get_col("SELECT comment_ID FROM {$wpdb->prefix}comments WHERE comment_post_ID ='$post__id' and comment_parent = '0' AND comment_approved ='1'");
        $rowsss = 0;

        if(count($rows) > 0){
            $dem = -1;

            foreach ($rows as $commentsid) {
                $dem++;
                $commentsid = $rows[$dem];
                $rowss = $wpdb->get_col("SELECT meta_value FROM {$wpdb->prefix}commentmeta WHERE comment_id ='$commentsid' and meta_key = 'star_of_comment'");
                $rowsss += $rowss[0];
            }

            $check_show_star = $rowsss/count($rows);
        }else{
            $check_show_star = 0;
        }
?>
<div class="top-commentss">
    <span class="rating-top"><?php
        $demss = 0;
        for ($dem = 1; $dem < 6; ++$dem) {
            if ($dem <= $check_show_star ) {
                echo '<img alt="1 star" src="'.DEPROURL.'/images/star.png">';
            } else {
                $demss++;
                if (ceil($check_show_star) - $check_show_star > 0 and $demss == 1) {
                    echo '<img alt="1 star" src="'.DEPROURL.'/images/star_half.png">';
                }else{
                    echo '<img alt="1 star" src="'.DEPROURL.'/images/star_off.png">';
                }
            }
        }
    ?></span> (Based on  <?= count($rows) ?> Rating )
    <h3>Reviews</h3>
</div>
        <?php
    }

    add_filter('comment_text', 'edit_comment_depro');

    function edit_comment_depro($comment)
    {
        $check_star = get_comment_meta (get_comment_ID(),'star_of_comment',true);
        $comment_check = get_comment(get_comment_ID());
        if($comment_check->comment_parent == 0){
            echo '<div class="activity-header">
                <span class="rating"> ';
            for($dem = 1; $dem < 6 ; $dem ++){
                if($dem <= $check_star ){
                    echo '<img alt="1 star" src="'.DEPROURL.'/images/star.png">';
                }else{
                    echo '<img alt="1 star" src="'.DEPROURL.'/images/star_off.png">';
                }
            }
            $user_id = $comment_check->user_id;

            if($user_id == 0){
                $user_link = "#No-data-with-this-user";
            }else{
                $user_info = get_userdata($user_id);
                $user_link = get_bloginfo('home')."/members/".$user_info->user_login;
            }
            global $wpdb; 
            $rows = $wpdb->get_col("SELECT comment_ID FROM ".$wpdb->prefix."comments WHERE comment_parent ='".get_comment_ID()."' AND comment_approved='1'");
            ?>
            </span> By <a title="<?= $comment_check->comment_author ?>" href="<?= $user_link ?>"><?= $comment_check->comment_author ?></a> ( <?php printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?> )
            </div>
            <script type="text/javascript">
                jQuery(document).ready( function() {
                    jQuery('#<?php echo "comment-".get_comment_ID();?> .comment-reply-link').eq(0).html('comment (<?php echo count($rows); ?>)');
                });
            </script>
<?php
        }
        echo '<br>'.$comment;
    }

    add_filter( 'comments_open', 'remove_comments_template_on_pages' );

    function remove_comments_template_on_pages()
    {
        $options = get_option('reviews_options');
        global $post;
        $idpost = $post->ID;
        $metakey = get_post_meta($idpost, '_reviews', true);
        if($options['postpage'] != "postpage" or $metakey != "onoff" ){
?>
<style type="text/css">
    .top-commentss,#comments{display:none}
</style>
<?php
            return false;
        }
        else
            return true;
    }

    add_action('comment_form_field_comment','checkdepro');

    function checkdepro()
    {
        ?>
<style type="text/css">
    .form-allowed-tags{display:none}
</style>
<div id="whats-new-content">
    <div id="whats-new-textarea">
        <div>
            <textarea value="" id="whats-new" name="comment"></textarea>
        </div>
    </div>
    <div id="review-rating">
        <br>Rate it: <img src="<?php echo DEPROURL;?>/images/star_off.png" class="star" id="star1"><img src="<?php echo DEPROURL;?>/images/star_off.png" class="star" id="star2"><img src="<?php echo DEPROURL;?>/images/star_off.png" class="star" id="star3"><img src="<?php echo DEPROURL;?>/images/star_off.png" class="star" id="star4"><img src="<?php echo DEPROURL;?>/images/star_off.png" class="star" id="star5">
    </div><br>
</div>
<input type="hidden" value="0" id="rating" name="rating">
        <?php
    }

    add_action('comment_post','add_star');

    function add_star()
    {
        global $post;
        $post_id = $post->ID;
        $args = array(
            'number' => '1',
            'post_id' => $post_id,
            'order' => 'DESC',
        );
        $star_check = $_POST["rating"];
        $commentss = get_comments($args);
        foreach ($commentss as $comment) {
            $comment_id = $comment->comment_ID;
            $comment_parent = $comment->comment_parent;
        }
        if($comment_parent == 0){
            add_comment_meta( $comment_id, 'star_of_comment', $star_check, true );
        }
    }
}

function prorevs_load()
{
    require_once('includes/class_depro.php');
    require_once('includes/top_users.php');
}
add_action('plugins_loaded', 'prorevs_load');

function prorevs_scripts_and_styles()
{
    wp_register_script('prorevs-script-common', plugins_url('/js/common.js', __FILE__ ), array('jquery'), '20121225-2');
    wp_localize_script('prorevs-script-common', 'proRevs', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('prorevs-ajax-nonce'),
        'loadingImg' =>  plugins_url('/images/loading.gif', __FILE__ )
        ));
    wp_enqueue_script('prorevs-script-common');

    wp_register_style('prorevs-style-common', plugins_url('/css/style.css', __FILE__ ), array(), '20121225-2', 'all');
    wp_enqueue_style('prorevs-style-common');
}
add_action('wp_enqueue_scripts', 'prorevs_scripts_and_styles');

//
// Handle the ajax "Report a Review" action
//

if ($options['flag']) {
    function prorevs_report_review_action()
    {
        global $wpdb;
        $options = get_option('reviews_options');
        if (wp_verify_nonce($_POST['nonce'], 'prorevs-ajax-nonce')) {
            $rating = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bp_activity WHERE id = %d", (int)$_POST['id']));
            if ($rating !== null) {
                $current_user = wp_get_current_user();
                $rater = get_userdata($rating->user_id);
                $reviewed = get_userdata($rating->usercheck);
                wp_mail(
                    $options['admin_emails'],
                    'A review is flagged and needs your attention',
                    "URL: " . get_bloginfo('home') . '/members/' . $reviewed->user_login . "/reviews/\n".
                    "Reporter: {$current_user->user_login}\n".
                    "Reason: {$_POST['reason']}\n".
                    "Review by: {$rater->user_login}\n".
                    "Review content: ". strip_tags($rating->content)
                );
                echo '""';
            } else {
                echo '"Can\\\'t find the given review."';
            }
        } else {
            echo '"Please reload the page and try again."';
        }

        exit;
    }
    add_action('wp_ajax_report-review', 'prorevs_report_review_action');
}

// Allow shortcodes in widgets
add_filter('widget_text', 'do_shortcode');

// Hide ratings from activities
function prorevs_activity_filter($a, $activities)
{
    global $bp;

//    if (is_super_admin())
//        return $activities;

    if ($GLOBALS['bp']->current_component == "reviews")
        return $activities;

    foreach ($activities->activities as $key => $activity) {
        if ($activity->type == 'Member_review' && $activity->anonymous) {
            if (get_current_user_id() == $activities->activities[$key]->user_id) {
                $activities->activities[$key]->user_id = -1;
                $activities->activities[$key]->action
                    = preg_replace('#^<a.*?\>.*?</a>#', 'You (anonymously)', $activities->activities[$key]->action);
                $activities->activities[$key]->primary_link = '';
                $activities->activities[$key]->user_email = '';
                $activities->activities[$key]->user_nicename = '';
                $activities->activities[$key]->user_login = '';
                $activities->activities[$key]->display_name = '';
                $activities->activities[$key]->user_fullname = '';
            } else {
                --$activities->activity_count;
                unset($activities->activities[$key]);
            }
        }
    }

    $activities->activities = array_values($activities->activities);

    return $activities;
}
add_action('bp_has_activities', 'prorevs_activity_filter', 10, 2);