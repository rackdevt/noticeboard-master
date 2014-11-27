<?php
/*
Plugin Name: FP News Ticker
Plugin URI: http://flourishpixel.com/
Description: This plugin will display news/post from a specific category in a widget position with ticker(reveal) effects or fadeIn/fadeOut effects. You can manage the options from backend.If the control buttons are not visible and the news title is not so long, then it will behave as Responsive view.
Author: Moshiur Rahman Mehedi
Version: 1.0.1
Author URI: http://www.flourishpixel.com/
*/

wp_enqueue_script('jquery');
wp_enqueue_script('ticker_script', plugins_url('/js/jquery.ticker.js',__FILE__), array( 'jquery' ));
wp_enqueue_style('ticker_css', plugins_url('/css/ticker-style.css',__FILE__) );

//Widget Code 

class NewstickerWidget extends WP_Widget
{
  function NewstickerWidget()
  {
    $widget_ops = array('classname' => 'NewstickerWidget', 'description' => 'Display posts as ticker/sliding' );
    $this->WP_Widget('NewstickerWidget', 'FP News Ticker', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'titleText'=>'Latest News:', 'speed' =>'0.100', 'pauseOnItems'=>'2000', 'controls' =>'true', 'direction'=>'ltr', 'fadeInSpeed' =>'600','fadeOutSpeed'=>'300', 'displayType'=>'reveal', 'debugMode'=>'false', 'category'=>'1','limit'=>'5') );
	$titleText = $instance['titleText'];
	$speed = $instance['speed'];
	$pauseOnItems = $instance['pauseOnItems'];
	$controls = $instance['controls'];
	$displayType = $instance['displayType'];
	$direction = $instance['direction'];
	$category = $instance['category'];
	$limit = $instance['limit'];
	$fadeInSpeed = $instance['fadeInSpeed'];
	$fadeOutSpeed = $instance['fadeOutSpeed'];
	$debugMode = $instance['debugMode'];
	
?>
<style type="text/css" media="screen">
p.fp_label input.custom {
	width:24%;
}
p.fp_label label{
	font-size:11px;
}
</style>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('titleText'); ?>">Title:
    <input class="widefat" id="<?php echo $this->get_field_id('titleText'); ?>" name="<?php echo $this->get_field_name('titleText'); ?>" type="text" value="<?php echo attribute_escape($titleText); ?>" />
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('category'); ?>">Category ID:
    <input class="custom" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo attribute_escape($category); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('limit'); ?>">Limit:
    <select name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>">
      <option value="2" <?php if(attribute_escape($limit) == '2'){echo 'selected';}?>>2</option>
      <option value="3" <?php if(attribute_escape($limit) == '3'){echo 'selected';}?>>3</option>
      <option value="4" <?php if(attribute_escape($limit) == '4'){echo 'selected';}?>>4</option>
      <option value="5" <?php if(attribute_escape($limit) == '5'){echo 'selected';}?>>5</option>
      <option value="6" <?php if(attribute_escape($limit) == '6'){echo 'selected';}?>>6</option>
      <option value="7" <?php if(attribute_escape($limit) == '7'){echo 'selected';}?>>7</option>
      <option value="8" <?php if(attribute_escape($limit) == '8'){echo 'selected';}?>>8</option>
      <option value="9" <?php if(attribute_escape($limit) == '9'){echo 'selected';}?>>9</option>
      <option value="10" <?php if(attribute_escape($limit) == '10'){echo 'selected';}?>>10</option>
      <option value="15" <?php if(attribute_escape($limit) == '15'){echo 'selected';}?>>15</option>
      <option value="20" <?php if(attribute_escape($limit) == '20'){echo 'selected';}?>>20</option>
    </select>
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('speed'); ?>">Speed:
    <input class="custom" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo attribute_escape($speed); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('pauseOnItems'); ?>">Delay Time:
    <input class="custom" id="<?php echo $this->get_field_id('pauseOnItems'); ?>" name="<?php echo $this->get_field_name('pauseOnItems'); ?>" type="text" value="<?php echo attribute_escape($pauseOnItems); ?>" />
  </label>
</p>
<p class="fp_label">
 <label for="<?php echo $this->get_field_id('direction'); ?>">Direction:
    <select name="<?php echo $this->get_field_name('direction'); ?>" id="<?php echo $this->get_field_id('direction'); ?>">
      <option value="ltr" <?php if(attribute_escape($direction) == 'ltr'){echo 'selected';}?>>Left to Right</option>
      <option value="rtl" <?php if(attribute_escape($direction) == 'rtl'){echo 'selected';}?>>Right to Left</option>
    </select>
  </label>
  </p>
  
  <p class="fp_label">
  <label for="<?php echo $this->get_field_id('displayType'); ?>">DIsplay Type:
    <select name="<?php echo $this->get_field_name('displayType'); ?>" id="<?php echo $this->get_field_id('displayType'); ?>">
      <option value="reveal" <?php if(attribute_escape($displayType) == 'reveal'){echo 'selected';}?>>Reveal</option>
      <option value="fade" <?php if(attribute_escape($displayType) == 'fade'){echo 'selected';}?>>Fade</option>
    </select>
  </label>
  </p>
  
  <p class="fp_label">
  <label for="<?php echo $this->get_field_id('fadeInSpeed'); ?>">FadeIn:
    <input class="custom" id="<?php echo $this->get_field_id('fadeInSpeed'); ?>" name="<?php echo $this->get_field_name('fadeInSpeed'); ?>" type="text" value="<?php echo attribute_escape($fadeInSpeed); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('fadeOutSpeed'); ?>">FadeOut:
    <input class="custom" id="<?php echo $this->get_field_id('fadeOutSpeed'); ?>" name="<?php echo $this->get_field_name('fadeOutSpeed'); ?>" type="text" value="<?php echo attribute_escape($fadeOutSpeed); ?>" />
  </label>
</p>
  <p class="fp_label">
   <label for="<?php echo $this->get_field_id('controls'); ?>">Controls:
    <select name="<?php echo $this->get_field_name('controls'); ?>" id="<?php echo $this->get_field_id('controls'); ?>">
      <option value="true" <?php if(attribute_escape($controls) == 'true'){echo 'selected';}?>>True</option>
      <option value="false" <?php if(attribute_escape($controls) == 'false'){echo 'selected';}?>>False</option>
    </select>
  </label>
  <label for="<?php echo $this->get_field_id('debugMode'); ?>">Debug:
    <select name="<?php echo $this->get_field_name('debugMode'); ?>" id="<?php echo $this->get_field_id('debugMode'); ?>">
      <option value="true" <?php if(attribute_escape($debugMode) == 'true'){echo 'selected';}?>>true</option>
      <option value="false" <?php if(attribute_escape($debugMode) == 'false'){echo 'selected';}?>>false</option>
    </select>
  </label>
</p>

<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['titleText'] = $new_instance['titleText'];
	$instance['speed'] = $new_instance['speed'];
	$instance['pauseOnItems'] = $new_instance['pauseOnItems'];
	$instance['controls'] = $new_instance['controls'];
	$instance['displayType'] = $new_instance['displayType'];
	$instance['direction'] = $new_instance['direction'];
	$instance['fadeInSpeed'] = $new_instance['fadeInSpeed'];
	$instance['fadeOutSpeed'] = $new_instance['fadeOutSpeed'];
	$instance['debugMode'] = $new_instance['debugMode'];
	$instance['category'] = $new_instance['category'];
	$instance['limit'] = $new_instance['limit'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$titleText = empty($instance['titleText']) ? ' ' : apply_filters('widget_titleText', $instance['titleText']);
	$speed = empty($instance['speed']) ? ' ' : apply_filters('widget_speed', $instance['speed']);
	$pauseOnItems = empty($instance['pauseOnItems']) ? ' ' : apply_filters('widget_pauseOnItems', $instance['pauseOnItems']);
	$controls = empty($instance['controls']) ? ' ' : apply_filters('widget_controls', $instance['controls']);
	$displayType = empty($instance['displayType']) ? ' ' : apply_filters('widget_displayType', $instance['displayType']);
	$direction = empty($instance['direction']) ? ' ' : apply_filters('widget_direction', $instance['direction']);
	$fadeInSpeed = empty($instance['fadeInSpeed']) ? ' ' : apply_filters('widget_fadeInSpeed', $instance['fadeInSpeed']);
	$fadeOutSpeed = empty($instance['fadeOutSpeed']) ? ' ' : apply_filters('widget_fadeOutSpeed', $instance['fadeOutSpeed']);
	$debugMode = empty($instance['debugMode']) ? ' ' : apply_filters('widget_debugMode', $instance['debugMode']);
	$category = empty($instance['category']) ? ' ' : apply_filters('widget_category', $instance['category']);
	$limit = empty($instance['limit']) ? ' ' : apply_filters('widget_limit', $instance['limit']);


?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#js-news').ticker({
        speed: <?php echo $speed; ?>,           // The speed of the reveal
        ajaxFeed: false,       // Populate jQuery News Ticker via a feed
        feedUrl: false,        // The URL of the feed
        feedType: 'xml',       // Currently only XML
        htmlFeed: true,        // Populate jQuery News Ticker via HTML
        debugMode: <?php echo $debugMode; ?>,       // Show some helpful errors in the console or as alerts, SHOULD BE SET TO FALSE FOR PRODUCTION SITES!
        controls: <?php echo $controls; ?>,        // Whether or not to show the jQuery News Ticker controls
        titleText: '<?php echo $titleText; ?>',   // To remove the title set this to an empty String
        displayType: '<?php echo $displayType; ?>', // Animation type - current options are 'reveal' or 'fade'
        direction: '<?php echo $direction; ?>',       // Ticker direction - current options are 'ltr' or 'rtl'
        pauseOnItems: <?php echo $pauseOnItems; ?>,    // The pause on a news item before being replaced
        fadeInSpeed: <?php echo $fadeInSpeed; ?>,      // Speed of fade in animation
        fadeOutSpeed: <?php echo $fadeOutSpeed; ?>      // Speed of fade out animation
	});
});
</script>

<?php
	// WIDGET CODE GOES HERE
	query_posts('cat='.$category.'&showposts='.$limit);
	if (have_posts()) : 
		echo "<ul id='js-news' class='js-hidden'>";
		while (have_posts()) : the_post(); 
			echo "<li class='news-item'>";
			echo "<a href='".get_permalink()."'>".get_the_title()."</a>";
			echo "</li>";
		endwhile;
		echo "</ul>";
	endif; 
	wp_reset_query();
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("NewstickerWidget");') );

?>
