<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @sub-subpackage Mapology
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://shabushabu.eu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
?>
<div class="activity">
	<?php if( mapo_routes_has_activity() ) : ?>
        
        <h3><?php _e( 'Route Activity', 'mapo' ) ?></h3> 
    
        <ul id="activity-list" class="activity-list item-list">
        
        <?php while ( bp_activities() ) : bp_the_activity(); ?>
    
            <li class="<?php bp_activity_css_class() ?>" id="activity-<?php bp_activity_id() ?>">
                <div class="activity-avatar">
                    <a href="<?php bp_activity_user_link() ?>">
                        <?php bp_activity_avatar( 'type=full&width=50&height=50' ) ?>
                    </a>
                </div>
    
                <div class="activity-content">
    
                    <div class="activity-header">
                        <?php bp_activity_action() ?>
                    </div>
    
                    <?php if ( bp_get_activity_content_body() ) : ?>
                        <div class="activity-inner">
                            <?php bp_activity_content_body() ?>
                        </div>
                    <?php endif; ?>
    
                    <?php do_action( 'bp_activity_entry_content' ) ?>
                    
                    <div class="activity-meta">
                        <?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>
                            <a href="<?php bp_activity_comment_link() ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id() ?>"><?php _e( 'Reply', 'buddypress' ) ?> (<span><?php bp_activity_comment_count() ?></span>)</a>
                        <?php endif; ?>
                    
                        <?php if ( is_user_logged_in() ) : ?>
                            <?php if ( !bp_get_activity_is_favorite() ) : ?>
                                <a href="<?php bp_activity_favorite_link() ?>" class="fav" title="<?php _e( 'Mark as Favorite', 'buddypress' ) ?>"><?php _e( 'Favorite', 'buddypress' ) ?></a>
                            <?php else : ?>
                                <a href="<?php bp_activity_unfavorite_link() ?>" class="unfav" title="<?php _e( 'Remove Favorite', 'buddypress' ) ?>"><?php _e( 'Remove Favorite', 'buddypress' ) ?></a>
                            <?php endif; ?>
                        <?php endif;?>
                    
                        <?php do_action( 'bp_activity_entry_meta' ) ?>
                    </div>
                    
                </div>
                
                <?php if ( bp_activity_can_comment() ) : ?>
                    <div class="activity-comments">
                        <?php bp_activity_comments() ?>
                
                        <?php if ( is_user_logged_in() ) : ?>
                        <form action="<?php bp_activity_comment_form_action() ?>" method="post" id="ac-form-<?php bp_activity_id() ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display() ?>>
                            <div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=25&height=25' ) ?></div>
                            <div class="ac-reply-content">
                                <div class="ac-textarea">
                                    <textarea id="ac-input-<?php bp_activity_id() ?>" class="ac-input" name="ac_input_<?php bp_activity_id() ?>"></textarea>
                                </div>
                                <input type="submit" name="ac_form_submit" value="<?php _e( 'Post', 'buddypress' ) ?> &rarr;" /> &nbsp; <?php _e( 'or press esc to cancel.', 'buddypress' ) ?>
                                <input type="hidden" name="comment_form_id" value="<?php bp_activity_id() ?>" />
                            </div>
                            <?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ) ?>
                        </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </li>
                
        <?php endwhile; ?>
        
        </ul>
    
    <?php else : ?>
        
        <div id="message" class="info">
            <?php if( is_user_logged_in() ) : ?>
            <p><?php _e( 'Be the first to comment on this route.', 'mapo' ) ?></p>
            <?php else : ?>
            <p><?php _e( 'Log in to comment on this route.', 'mapo' ) ?></p>
            <?php endif; ?>
        </div>
    
    <?php endif; ?>
    
    <?php if( is_user_logged_in() ) : ?>
    
        <h3 class="add-event-comm"><?php _e( 'Add your comment', 'mapo' ) ?></h3>
        
        <form id="route-activity-comment-form" name="route-activity-comment-form" class="standard-form" action="" method="post">
            
            <textarea id="comment_text" name="comment_text"></textarea>
        
            <div class="submit">
                <input type="submit" value="<?php _e( 'Send Comment', 'mapo' ) ?>" id="send_route_comment" name="send_route_comment" />
            </div>
        
            <input type="hidden" name="route_id" value="<?php mapo_routes_id() ?>" />
            <input type="hidden" name="privacy" value="<?php mapo_routes_public() ?>" />
            <?php wp_nonce_field( 'mapo_route_comment' ); ?>
        </form>
        
    <?php endif; ?>
</div>