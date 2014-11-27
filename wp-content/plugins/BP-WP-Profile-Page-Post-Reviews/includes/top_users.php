<?php

function prorevs_print_users($users) {
    $ret = '<div class="prorevs_user_list">';

    $count = 1;
    foreach ($users as $user) {
        $ret .= '
        <div class="prorevs_user_list_item">
            <div class="prorevs_user_list_number">
                ' . ($count++) . '.
            </div>
            ' . get_avatar($user->id, 50, $default, "") . '
            <div class="prorevs_user_list_info">
                <div>
                    <a href="' . get_bloginfo('home') . '/members/' . $user->name . '/"
                        class="prorevs_user_list_name">' . $user->name . '</a>
                </div>
                <div class="ratingtop">';
        for ($i = 1; $i < 6; ++$i) {
            if ($i <= $user->rating + 0.5) {
                $ret .= '<img alt="1 star" src="'.DEPROURL.'/images/star.png">';
            } else {
                $ret .= '<img alt="1 star" src="'.DEPROURL.'/images/star_off.png">';
            }
        }
        $ret .=
               '</div>
                <div class="prorevs_user_list_reviews">(' . $user->reviews . ' reviews)</div>
            </div>
        </div>';
    }

    $ret .= '</div>';

    return $ret;
}

function prorevs_users_by_rating($limit) {
    global $wpdb;

    $users = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                 a.ID as id, a.user_login AS name, SUM(star) / COUNT(star) AS rating, COUNT(star) AS reviews
             FROM wp_users AS a
             LEFT JOIN wp_bp_activity AS b ON a.ID = b.usercheck
             GROUP BY id
             ORDER BY rating DESC
             LIMIT %d",
             $limit
             )
        );

    return prorevs_print_users($users);
}

function prorevs_users_by_rating_shortcode($atts) {
    extract(shortcode_atts(array('limit' => 10), $atts));
    return prorevs_users_by_rating($limit);
}

function prorevs_users_by_review_count($limit) {
    global $wpdb;

    $users = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                 a.ID as id, a.user_login AS name, SUM(star) / COUNT(star) AS rating, COUNT(star) AS reviews
             FROM wp_users AS a
             LEFT JOIN wp_bp_activity AS b ON a.ID = b.usercheck
             GROUP BY id
             ORDER BY reviews DESC
             LIMIT %d",
             $limit
             )
        );

    return prorevs_print_users($users);
}

function prorevs_users_by_review_count_shortcode($atts) {
    extract(shortcode_atts(array('limit' => 10), $atts));
    return prorevs_users_by_review_count($limit);
}

add_shortcode('prorevs_users_by_rating', 'prorevs_users_by_rating_shortcode');
add_shortcode('prorevs_users_by_review_count', 'prorevs_users_by_review_count_shortcode');
