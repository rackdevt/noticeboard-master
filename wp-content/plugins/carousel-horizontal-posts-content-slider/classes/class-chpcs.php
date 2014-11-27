<?php
require('class-chpcs-widget.php');
class CHPC_Slider {

	//default settings
	private $defaults = array(
	'settings' => array(
		'display_image' => true,
		'word_limit' => '20',
		'read_more_text' => 'Read more',
		'number_of_posts_to_display' => '20',
		'posts_order' => 'desc',
		'posts_orderby' => 'id',
		'category' => '1',
		'display_pagination' => false,
		'display_excerpt' => true,
		'display_title' => true,
		'display_read_more' => true,
		'display_controls' => true,
		'no_of_items_to_scroll' => '1',
		'auto_scroll' => false,
		'circular' => false,
		'fx' => 'scroll',
		'deactivation_delete' => false,
		'loading_place' => 'header',
		'easing_effect' => 'linear'
	),
	'version' => '3.2.1'
);
	private $options = array();
	private $tabs = array();

	public function __construct() {

		register_activation_hook(__FILE__, array(&$this, 'wa_chpcs_activation'));
		register_deactivation_hook(__FILE__, array(&$this, 'wa_chpcs_deactivation'));

		$CHPCS_Widget = new CHPCS_widget();

		//Add admin option
		add_action('admin_menu', array(&$this, 'admin_menu_options'));
		add_action('admin_init', array(&$this, 'register_settings'));

		//add text domain for localization
		add_action('plugins_loaded', array(&$this, 'load_textdomain'));

		//load defaults
		add_action('plugins_loaded', array(&$this, 'load_defaults'));

		//update plugin version
		update_option('wa_chpcs_version', $this->defaults['version'], '', 'no');
		$this->options['settings'] = array_merge($this->defaults['settings'], (($array = get_option('wa_chpcs_settings')) === FALSE ? array() : $array));
		
		//insert js and css files
		add_action('wp_enqueue_scripts', array(&$this, 'include_scripts'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_include_scripts'));

		//settings link
		add_filter('plugin_action_links', array(&$this, 'wa_chpcs_settings_link'), 2, 2);

		//add shortcode
		add_shortcode( 'carousel-horizontal-posts-content-slider', array(&$this, 'wa_chpcs_display_slider'));
	}

	/* activation hook */
	public function wa_chpcs_activation(){

		add_option('wa_chpcs_settings', $this->defaults['settings'], '', 'no');
		add_option('wa_chpcs_version', $this->defaults['version'], '', 'no');

	}

	/*  deactivation hook */
	public function wa_chpcs_deactivation($multi = FALSE){
		$check = $this->options['settings']['deactivation_delete'];
		if($check === TRUE)
		{
			delete_option('wa_chpcs_settings');
			delete_option('wa_chpcs_version');
		}
	}

	/* settings link in management screen */
	public function wa_chpcs_settings_link($actions, $file){
		if(false !== strpos($file, 'carousel-horizontal-posts-content-slider'))
		 $actions['settings'] = '<a href="options-general.php?page=carousel-horizontal-posts-content-slider">Settings</a>';
		return $actions; 
	}

	/* display slider */
	public function wa_chpcs_display_slider(){
	
	global $wpdb, $post;
	$display_image = $this->options['settings']['display_image'];
	$word_limit = $this->options['settings']['word_limit'];
	$number_of_posts_to_display = $this->options['settings']['number_of_posts_to_display'];
	$posts_order= $this->options['settings']['posts_order'];
	$posts_orderby= $this->options['settings']['posts_orderby'];
	$category= $this->options['settings']['category'];
	$display_pagination= $this->options['settings']['display_pagination'];
	$display_excerpt= $this->options['settings']['display_excerpt'];
	$display_title= $this->options['settings']['display_title'];
	$display_read_more= $this->options['settings']['display_read_more'];
	$read_more_text= $this->options['settings']['read_more_text'];
	$display_controls= $this->options['settings']['display_controls'];
	$no_of_items_to_scroll= $this->options['settings']['no_of_items_to_scroll'];
	$auto_scroll= $this->options['settings']['auto_scroll'];
	$fx= $this->options['settings']['fx'];
	$slider_gallery = '';
	$slider_gallery.= '<div class="chpcs_image_carousel">';
	$slider_gallery.='<div id="wa_chpc_slider">';

	$args = array( 'numberposts' => $number_of_posts_to_display,  'category' => $category, 'order'=> $posts_order, 'orderby' => $posts_orderby, 'post_type' => 'any'  );
	$myposts = get_posts( $args );
	foreach( $myposts as $post ){

		$post_title = $post->post_title;
		$post_link =  get_permalink($post->ID);
		$post_content = strip_shortcodes($post->post_content);
		
		$slider_gallery.= '<div id="chpcs_foo_content">';

		if($display_image){
		
			$slider_gallery.= '<a href="'.$post_link.'">'.$this->get_post_image($post->ID).'</a>';
		}
		
		//Post title, Post Description, Post read more
		if($display_title){
		$slider_gallery.= '<br/><span class="chpcs_title"><a  style=" text-decoration:none;" href="'.$post_link.'">'.$post_title.'</a></span><br/>';
		}

		if($display_excerpt){
		$slider_gallery.= '<p><span class="chpcs_foo_con">'.$this->tchpcs_clean($post->post_excerpt, $word_limit).'</span></p>';
		}

		if($display_read_more){
		$slider_gallery.= '<br/><span class="chpcs_more"><a href="'.$post_link.'">'.$read_more_text.'</a></span>';
		}

		$slider_gallery.= '</div>';
	}

	$slider_gallery.='</div>';
	$slider_gallery.='<div class="chpcs_clearfix"></div>';
	if($display_controls){
	$slider_gallery.='<a class="chpcs_prev" id="wa_chpc_slider_prev" href="#"><span>prev</span></a>';
	$slider_gallery.='<a class="chpcs_next" id="wa_chpc_slider_next" href="#"><span>next</span></a>';	
	}
	if($display_pagination){
	$slider_gallery.='<div class="chpcs_pagination" id="wa_chpc_slider_pag"></div>'; }
	$slider_gallery.='</div>';

	return $slider_gallery;
	}


	/* Get image url */
	public	function get_post_image($post_image_id) {
  
  			if (has_post_thumbnail( $post_image_id ) ): 
			 $img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post_image_id ), 'single-post-thumbnail' ); $first_img = $img_arr[0];
			endif; 
	
	  if(empty($first_img)) {
			 $first_img = plugins_url('assets/images/default-image.jpg', dirname(__FILE__));
	  }
	  	$first_img = "<img src='". $first_img. "'/>";
	  	return $first_img;
	}


	/* insert css files js files */
	public function include_scripts() {	
	$args = apply_filters('chpcs_args', array(
		'auto_scroll' => $this->options['settings']['auto_scroll']? 'true' : 'false',
		'circular' => $this->options['settings']['circular']? 'true' : 'false',
		'easing_effect' => $this->options['settings']['easing_effect'],
		'no_of_items_to_scroll' => $this->options['settings']['no_of_items_to_scroll'],
		'fx' => $this->options['settings']['fx']));
	wp_register_style('wa_chpcs_style',plugins_url('assets/css/custom-style.css', dirname(__FILE__)));
	wp_enqueue_style('wa_chpcs_style');
	wp_register_script('wa_chpcs_caroufredsel',plugins_url('assets/js/jquery.carouFredSel-6.2.1-packed.js', dirname(__FILE__)),array('jquery'),'',($this->options['settings']['loading_place'] === 'header' ? false : true));
	wp_enqueue_script('wa_chpcs_caroufredsel');
	wp_register_script('wa_chpcs_touch_swipe',plugins_url('assets/js/jquery.touchSwipe.min.js', dirname(__FILE__)),array('jquery'),'',($this->options['settings']['loading_place'] === 'header' ? false : true));
	wp_enqueue_script('wa_chpcs_touch_swipe');
	wp_register_script('wa_chpcs_custom',plugins_url('assets/js/script.js', dirname(__FILE__)),array('jquery'),'',($this->options['settings']['loading_place'] === 'header' ? false : true));
	wp_enqueue_script('wa_chpcs_custom');
	wp_localize_script('wa_chpcs_custom','chpcsArgs',$args);
	}

	/* Post image attachment (sizes: thumbnail, medium, full) */
	public function attachment_image_filter($postid=0, $size='thumbnail', $attributes='') {
		if ($postid<1) $postid = get_the_ID();
		if ($images = get_children(array(
				'post_parent' => $postid,
				'post_type' => 'attachment',
				'numberposts' => 1,
				'post_mime_type' => 'image',)))
			foreach($images as $image)
			{
				$attachment=wp_get_attachment_image_src($image->ID, $size);

				  if(empty($attachment[0])) {
	    		$first_img = plugins_url()."/carousel-horizontal-posts-content-slider/images/default-image.jpg";
	    		return "<img  src='". $first_img . "' " . $attributes . " />";
	  			}else{

				return "<img  src='". $attachment[0] . "' " . $attributes . " />";

	  			}
			}
	}

	/* limit words (remove images, html tags and retrieve text only) */
	public function tchpcs_clean($excerpt, $substr) {
	
		$string = $excerpt;
		$string = strip_shortcodes(wp_trim_words( $string, (int)$substr ));
	
		return $string;
	}

	/* insert css files for admin area */
	public function admin_include_scripts(){
			wp_register_style('wa_chpcs_admin',plugins_url('assets/css/admin.css', dirname(__FILE__) ));
			wp_enqueue_style('wa_chpcs_admin');
	}

	public function admin_menu_options(){
		add_options_page(
			__('CHPC Slider', 'carousel-horizontal-posts-content-slider'),
			__('CHPC Slider', 'carousel-horizontal-posts-content-slider'),
			'manage_options',
			'carousel-horizontal-posts-content-slider',
			array(&$this, 'options_page')
		);
	}

	/* register setting for plugins page */
	public function register_settings()
	{
		register_setting('wa_chpcs_settings', 'wa_chpcs_settings', array(&$this, 'validate_options'));
		//general settings
		add_settings_section('wa_chpcs_settings', __('', 'wa-chpcs-txt'), '', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_display_image', __('Display featured image', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_display_image'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_display_controls', __('Display next and prev buttons', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_display_controls'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_display_read_more', __('Display read more text', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_display_read_more'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_display_title', __('Display title', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_display_title'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_display_excerpt', __('Display excerpt', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_display_excerpt'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_display_pagination', __('Display pagination', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_display_pagination'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_auto_scroll', __('Auto scroll', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_auto_scroll'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_circular', __('Circular', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_circular'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_word_limit', __('Excerpt limit', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_word_limit'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_read_more_text', __('Read more text', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_read_more_text'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_posts_category', __('Category', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_posts_category'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_number_of_posts_to_display', __('Number of posts', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_number_of_posts_to_display'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_no_of_items_to_scroll', __('Scroll', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_no_of_items_to_scroll'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_posts_order', __('Posts order', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_posts_order'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_posts_orderby', __('Posts orderby', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_posts_orderby'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_fx', __('Transition effect', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_fx'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_easing_effect', __('Easing effect', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_easing_effect'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_loading_place', __('Loading place', 'wa-chpcs'), array(&$this, 'wa_chpcs_loading_place'), 'wa_chpcs_settings', 'wa_chpcs_settings');
		add_settings_field('wa_chpcs_deactivation_delete', __('Deactivation', 'wa-chpcs-txt'), array(&$this, 'wa_chpcs_deactivation_delete'), 'wa_chpcs_settings', 'wa_chpcs_settings');
	
	}

	public function wa_chpcs_loading_place()
	{
		echo '
		<div id="wa_chpcs_loading_place" class="wplikebtns">';

		foreach($this->loading_places as $val => $trans)
		{
			$val = esc_attr($val);

			echo '
			<input id="rll-loading-place-'.$val.'" type="radio" name="wa_chpcs_settings[loading_place]" value="'.$val.'" '.checked($val, $this->options['settings']['loading_place'], false).' />
			<label for="rll-loading-place-'.$val.'">'.esc_html($trans).'</label>';
		}

		echo '
			<p class="description">'.__('Select where all the scripts should be placed.', 'wa-chpcs-txt').'</p>
		</div>';
	}

	public function wa_chpcs_display_image()
	{
		echo '
		<div id="wa_chpcs_display_image" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[display_image]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['display_image'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '</div>';
	}

	public function wa_chpcs_display_controls(){
		echo '
		<div id="wa_chpcs_display_controls" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[display_controls]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['display_controls'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '</div>';
	}

	/* display read more text */
	public function wa_chpcs_display_read_more(){
		echo '
		<div id="wa_chpcs_display_read_more" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[display_read_more]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['display_read_more'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '</div>';
	}

	/* display title */
	public function wa_chpcs_display_title(){
		echo '
		<div id="wa_chpcs_display_title" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[display_title]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['display_title'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '</div>';
	}

	/* display excerpt*/
	public function wa_chpcs_display_excerpt(){
		echo '
		<div id="wa_chpcs_display_excerpt" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[display_excerpt]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['display_excerpt'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '</div>';
	}

	/* display pagination */
	public function wa_chpcs_display_pagination(){
		echo '
		<div id="wa_chpcs_display_pagination" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[display_pagination]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['display_pagination'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}
		echo '</div>';
	}

	/* auto scroll slider */
	public function wa_chpcs_auto_scroll(){
		echo '
		<div id="wa_chpcs_auto_scroll" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[auto_scroll]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['auto_scroll'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '
			<p class="description">'.__('Determines whether the carousel should be auto scroll.', 'wa-chpcs-txt').'</p>
		</div>';
	}

	/* number of items to be slide per one transition */
	public function wa_chpcs_no_of_items_to_scroll(){
		echo '
		<div id="wa_chpcs_no_of_items_to_scroll">
			<input type="text" name="wa_chpcs_settings[no_of_items_to_scroll]" value="'.esc_attr($this->options['settings']['no_of_items_to_scroll']).'" />
		</div>';

					echo '
			<p class="description">'.__('Number of posts to be scroll per one transition', 'wa-chpcs-txt').'</p>
		</div>';
	}

	public function wa_chpcs_circular(){
		echo '
		<div id="wa_chpcs_circular" class="wplikebtns">';

		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="rll-galleries-'.$val.'" type="radio" name="wa_chpcs_settings[circular]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['circular'], FALSE).' />
			<label for="rll-galleries-'.$val.'">'.$trans.'</label>';
		}

		echo '
			<p class="description">'.__('Determines whether the carousel should be circular.', 'wa-chpcs-txt').'</p>
		</div>';
	}



	/* number of posts to display on the slider */
	public function wa_chpcs_number_of_posts_to_display(){
		echo '
		<div id="wa_chpcs_number_of_posts_to_display">
			<input type="text" name="wa_chpcs_settings[number_of_posts_to_display]" value="'.esc_attr($this->options['settings']['number_of_posts_to_display']).'" />
		</div>';

			echo '
			<p class="description">'.__('Number of posts to be displayed on the slider', 'wa-chpcs-txt').'</p>
		</div>';	
	}

	/* post order */
	public	function wa_chpcs_posts_order() {
	    $options = $this->options['settings']['posts_order'];
	     
    	$html = '<select id="wa_chpcs_posts_order" name="wa_chpcs_settings[posts_order]">';
        $html .= '<option value="asc"' . selected( esc_attr($this->options['settings']['posts_order']), 'asc', false) . '>Ascending </option>';
        $html .= '<option value="desc"' . selected( esc_attr($this->options['settings']['posts_order']), 'desc', false) . '>Descending</option>';
		$html .= '<option value="rand"' . selected( esc_attr($this->options['settings']['posts_order']), 'rand', false) . '>Random</option>';
    	$html .= '</select>';    
	    echo $html;
	} 

	/* posts order by */
	public function wa_chpcs_posts_orderby(){
		echo '
		<div id="wa_chpcs_posts_orderby">
			<input type="text" name="wa_chpcs_settings[posts_orderby]" value="'.esc_attr($this->options['settings']['posts_orderby']).'" />
		</div>';
	}

	/* category */
	public function wa_chpcs_posts_category(){
		echo '
		<div id="wa_chpcs_posts_category">
			<input type="text" name="wa_chpcs_settings[category]" value="'.esc_attr($this->options['settings']['category']).'" />
		</div>';

			echo '
			<p class="description">'.__('Category ids seperated by comma. e.g 1,2', 'wa-chpcs-txt').'</p>
		</div>';
	}

	/* read more text */
	public function wa_chpcs_read_more_text(){
		echo '
		<div id="wa_chpcs_read_more_text">
			<input type="text" name="wa_chpcs_settings[read_more_text]" value="'.esc_attr($this->options['settings']['read_more_text']).'" />
		</div>';
	}

	/* word limit */
	public function wa_chpcs_word_limit(){
		echo '
		<div id="wa_chpcs_word_limit">
			<input type="text" name="wa_chpcs_settings[word_limit]" value="'.esc_attr($this->options['settings']['word_limit']).'" />
		</div>';

		echo '
			<p class="description">'.__('Limit the no of words in excerpt', 'wa-chpcs-txt').'</p>
		</div>';
	}

	/* transition effects */
	public function wa_chpcs_fx(){
 		$options = $this->options['settings']['fx'];
	    $html = '<select id="wa_chpcs_fx" name="wa_chpcs_settings[fx]">';
        $html .= '<option value="none"' . selected( esc_attr($this->options['settings']['fx']), 'none', false) . '>none</option>';
        $html .= '<option value="scroll"' . selected( esc_attr($this->options['settings']['fx']), 'scroll', false) . '>scroll</option>';
        $html .= '<option value="directscroll"' . selected( esc_attr($this->options['settings']['fx']), 'directscroll', false) . '>directscroll</option>';
    	$html .= '<option value="fade"' . selected( esc_attr($this->options['settings']['fx']), 'fade', false) . '>fade</option>';
    	$html .= '<option value="crossfade"' . selected( esc_attr($this->options['settings']['fx']), 'crossfade', false) . '>crossfade</option>';
    	$html .= '<option value="cover"' . selected( esc_attr($this->options['settings']['fx']), 'cover', false) . '>cover</option>';
    	$html .= '<option value="cover-fade"' . selected( esc_attr($this->options['settings']['fx']), 'cover-fade', false) . '>cover-fade</option>';
    	$html .= '<option value="uncover"' . selected( esc_attr($this->options['settings']['fx']), 'uncover', false) . '>uncover</option>';
    	$html .= '<option value="uncover-fade"' . selected( esc_attr($this->options['settings']['fx']), 'uncover-fade', false) . '>uncover-fade</option>';
    	$html .= '</select>';    
	    echo $html;
	} 

	/* easing effects */
	public function wa_chpcs_easing_effect(){
		$options = $this->options['settings']['easing_effect'];
	    $html = '<select id="wa_chpcs_easing_effect" name="wa_chpcs_settings[easing_effect]">';
        $html .= '<option value="linear"' . selected( esc_attr($this->options['settings']['easing_effect']), 'linear', false) . '>linear</option>';
        $html .= '<option value="swing"' . selected( esc_attr($this->options['settings']['easing_effect']), 'swing', false) . '>swing</option>';
        $html .= '<option value="quadratic"' . selected( esc_attr($this->options['settings']['easing_effect']), 'quadratic', false) . '>quadratic</option>';
    	$html .= '<option value="cubic"' . selected( esc_attr($this->options['settings']['easing_effect']), 'cubic', false) . '>cubic</option>';
    	$html .= '<option value="elastic"' . selected( esc_attr($this->options['settings']['easing_effect']), 'elastic', false) . '>elastic</option>';
    	$html .= '</select>';    
	    echo $html;
	} 

	/* deactivation on delete */
	public function wa_chpcs_deactivation_delete(){
		echo '
		<div id="wa_chpcs_deactivation_delete" class="wplikebtns">';
		foreach($this->choices as $val => $trans)
		{
			echo '
			<input id="wa-chpcs-deactivation-delete-'.$val.'" type="radio" name="wa_chpcs_settings[deactivation_delete]" value="'.esc_attr($val).'" '.checked(($val === 'yes' ? TRUE : FALSE), $this->options['settings']['deactivation_delete'], FALSE).' />
			<label for="wa-chpcs-deactivation-delete-'.$val.'">'.$trans.'</label>';
		}
		echo '
			<p class="description">'.__('Delete settings on plugin deactivation.', 'wa-chpcs-txt').'</p>
		</div>';
	}

	public function options_page(){
		$tab_key = (isset($_GET['tab']) ? $_GET['tab'] : 'general-settings');
		echo '<div class="wrap">'.screen_icon().'
			<h2>'.__('Carousel horizontal posts content slider', 'wa-chpcs-txt').'</h2>
			<h2 class="nav-tab-wrapper">';

		foreach($this->tabs as $key => $name) {
			echo '
			<a class="nav-tab '.($tab_key == $key ? 'nav-tab-active' : '').'" href="'.esc_url(admin_url('options-general.php?page=carousel-horizontal-posts-content-slider&tab='.$key)).'">'.$name['name'].'</a>';
		}
		echo '</h2><div class="wa-chpcs-settings"><div class="wa-chpcs-credits"><h3 class="hndle">'.__('Carousel horizontal posts content slider', 'wa-chpcs-txt').'</h3>
					<div class="inside">
					<p class="inner">'.__('Configuration: ', 'wa-chpcs-txt').' <a href="http://wordpress.org/plugins/carousel-horizontal-posts-content-slider/installation/" target="_blank" title="'.__('Plugin URL', 'wa-chpcs-txt').'">'.__('Plugin URI', 'wa-chpcs-txt').'</a></p>
					</p><hr />
					<h4 class="inner">'.__('Do you like this plugin?', 'wa-chpcs-txt').'</h4>
					<p class="inner">'.__('Please, ', 'wa-chpcs-txt').'<a href="http://wordpress.org/support/view/plugin-reviews/carousel-horizontal-posts-content-slider" target="_blank" title="'.__('rate it', 'wa-chpcs-txt').'">'.__('rate it', 'wa-chpcs-txt').'</a> '.__('on WordPress.org', 'wa-chpcs-txt').'<br />          
					<hr />
					<div style="width:auto; margin:auto; text-align:center;"><a href="http://weaveapps.com/shop/wordpress-plugins/carousel-horizontal-posts-slider-wordpress-plugin/" target="_blank"><img width="270" height="70" src="'.plugins_url('assets/images/chpcs-pro.png',dirname(__FILE__)).'"/></a></div>
					</div>
				</div><form action="options.php" method="post">';
		wp_nonce_field('update-options');
		settings_fields($this->tabs[$tab_key]['key']);
		do_settings_sections($this->tabs[$tab_key]['key']);
		echo '<p class="submit">';
		submit_button('', 'primary', $this->tabs[$tab_key]['submit'], FALSE);
		echo ' ';
		echo submit_button(__('Reset to defaults', 'wa-chpcs-txt'), 'secondary', $this->tabs[$tab_key]['reset'], FALSE);
		echo '</p></form></div><div class="clear"></div></div>';
	}

	public function load_defaults(){
		
		$this->choices = array(
			'yes' => __('Enable', 'wa-chpcs-txt'),
			'no' => __('Disable', 'wa-chpcs-txt')
		);

		$this->loading_places = array(
			'header' => __('Header', 'wa-chpcs-txt'),
			'footer' => __('Footer', 'wa-chpcs-txt')
		);

		$this->tabs = array(
			'general-settings' => array(
				'name' => __('General settings', 'wa-chpcs-txt'),
				'key' => 'wa_chpcs_settings',
				'submit' => 'save_wa_chpcs_settings',
				'reset' => 'reset_wa_chpcs_settings',
			)
		);
	}

	/* load text domain for localization */
	public function load_textdomain(){
		load_plugin_textdomain('wa-chpcs-txt', FALSE, dirname(plugin_basename(__FILE__)).'/lang/');
	}

	/* validate options and register settings */
	public function validate_options($input){
		if(isset($_POST['save_wa_chpcs_settings']))
		{
			$input['loading_place'] = (isset($input['loading_place'], $this->loading_places[$input['loading_place']]) ? $input['loading_place'] : $this->defaults['settings']['loading_place']);
			$input['display_image'] = (isset($input['display_image'], $this->choices[$input['display_image']]) ? ($input['display_image'] === 'yes' ? true : false) : $this->defaults['settings']['display_image']);
			$input['display_pagination'] = (isset($input['display_pagination'], $this->choices[$input['display_pagination']]) ? ($input['display_pagination'] === 'yes' ? true : false) : $this->defaults['settings']['display_pagination']);
			$input['display_excerpt'] = (isset($input['display_excerpt'], $this->choices[$input['display_excerpt']]) ? ($input['display_excerpt'] === 'yes' ? true : false) : $this->defaults['settings']['display_excerpt']);
			$input['display_title'] = (isset($input['display_title'], $this->choices[$input['display_title']]) ? ($input['display_title'] === 'yes' ? true : false) : $this->defaults['settings']['display_title']);
			$input['display_read_more'] = (isset($input['display_read_more'], $this->choices[$input['display_read_more']]) ? ($input['display_read_more'] === 'yes' ? true : false) : $this->defaults['settings']['display_read_more']);
			$input['display_controls'] = (isset($input['display_controls'], $this->choices[$input['display_controls']]) ? ($input['display_controls'] === 'yes' ? true : false) : $this->defaults['settings']['display_controls']);
			$input['deactivation_delete'] = (isset($input['deactivation_delete'], $this->choices[$input['deactivation_delete']]) ? ($input['deactivation_delete'] === 'yes' ? true : false) : $this->defaults['settings']['deactivation_delete']);
			$input['auto_scroll'] = (isset($input['auto_scroll'], $this->choices[$input['auto_scroll']]) ? ($input['auto_scroll'] === 'yes' ? true : false) : $this->defaults['settings']['auto_scroll']);
			$input['circular'] = (isset($input['circular'], $this->choices[$input['circular']]) ? ($input['circular'] === 'yes' ? true : false) : $this->defaults['settings']['circular']);
			$input['fx'] = sanitize_text_field(isset($input['fx']) && $input['fx'] !== '' ? $input['fx'] : $this->defaults['settings']['fx']);			
			$input['word_limit'] = sanitize_text_field(isset($input['word_limit']) && $input['word_limit'] !== '' ? $input['word_limit'] : $this->defaults['settings']['word_limit']);
			$input['read_more_text'] = sanitize_text_field(isset($input['read_more_text']) && $input['read_more_text'] !== '' ? $input['read_more_text'] : $this->defaults['settings']['read_more_text']);
			$input['number_of_posts_to_display'] = sanitize_text_field(isset($input['number_of_posts_to_display']) && $input['number_of_posts_to_display'] !== '' ? $input['number_of_posts_to_display'] : $this->defaults['settings']['number_of_posts_to_display']);
			$input['posts_order'] = sanitize_text_field(isset($input['posts_order']) && $input['posts_order'] !== '' ? $input['posts_order'] : $this->defaults['settings']['posts_order']);
			$input['posts_orderby'] = sanitize_text_field(isset($input['posts_orderby']) && $input['posts_orderby'] !== '' ? $input['posts_orderby'] : $this->defaults['settings']['posts_orderby']);
			$input['category'] = sanitize_text_field(isset($input['category']) && $input['category'] !== '' ? $input['category'] : $this->defaults['settings']['category']);
			$input['no_of_items_to_scroll'] = sanitize_text_field(isset($input['no_of_items_to_scroll']) && $input['no_of_items_to_scroll'] !== '' ? $input['no_of_items_to_scroll'] : $this->defaults['settings']['no_of_items_to_scroll']);

		}elseif(isset($_POST['reset_wa_chpcs_settings']))
		{
			$input = $this->defaults['settings'];
			add_settings_error('reset_general_settings', 'general_reset', __('Settings restored to defaults.', 'wa-chpcs-txt'), 'updated');
		}

		return $input;
	}
}