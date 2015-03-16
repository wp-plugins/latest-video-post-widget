<?php
/*
Plugin Name: Latest Video
Plugin URI: http://webcodewrap.com
Description: A widget that shows the latest post that contains video embedded with iframe.
Author: WebCodeWrap
Author URI: http://webcodewrap.com
Version: 1.0.1
Text Domain: latest-video-post-widget
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class webcodewrap_latestvideopost extends WP_Widget {

	function webcodewrap_latestvideopost() {
	   	$widget_ops = array('classname' => 'webcodewrap_widget_latest_video_post', 'description' => __('Shows latest video post.', 'latest-video-post-widget'));
		$this->WP_Widget('webcodewrap-latest-video-post', __('Latest Video Post', 'latest-video-post-widget'), $widget_ops);
	}
	
	function widget($args, $instance) {
	   
        define('CSS_HOOK', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
		wp_enqueue_style('latest_video_post_css', CSS_HOOK.'css/style.css');
	   
		extract($args);
        
		$height = $instance['height'];
		$width = $instance['width'];
		$posttitlelink = $instance['posttitlelink'];
		$categorytitlelink = $instance['categorytitlelink'];
		
		if(!$height) { $height = '250'; }		
		if(!$width) { $width = '300'; }
				
		$video_id = '';
        $recent_video = new WP_Query('showposts=50');
        
		while ($recent_video->have_posts()) : $recent_video->the_post();
    			
    		$posturl = get_permalink( $post->ID );
    		$posttitle = get_the_title(); 
            
            global $post;
            ob_start();
            ob_end_clean();
            preg_match_all('/<iframe.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
             
            if($matches[1][0] != ''){
                $video_id = $matches[1][0];
            } else {
                $video_id = '';
            }		
    		           
            if($video_id != ''){
    	
        		echo $before_widget;
                
                $date = get_the_date('d M. Y');
                $post_id = get_the_ID();
                $category = get_the_category( $post_id );
                $category_link = get_category_link( $category[0]->cat_ID );  
                $category_name = $category[0]->cat_name;   
                 
               	echo ''.$before_title.'' . $instance['title'] . ''.$after_title.'';          		
                ?>
                <div class="textwidget">
                    <iframe width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="<?php echo $video_id; ?>" frameborder="0" allowfullscreen></iframe>
                </div>                	
        	<?php 
                if ( $posttitlelink == 1 ){
                    echo '<div class="vid-post-title"><a class="post-link" href="'.esc_url(get_permalink()).'">'.esc_attr(get_the_title()).'</a></div>';
                }
                if ( $categorytitlelink == 1 ){
                    echo '<div class="vid-post-cat">Published On <a class="cat-link" href="'.$category_link.'">'. $category_name. '</a></div>';
				}	
                break;
            } 
            
		endwhile; 
        
        wp_reset_postdata();		
		
		echo $after_widget;
	}
	   
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['width'] = $new_instance['width'];		
		$instance['height'] = $new_instance['height'];
        $instance['posttitlelink'] = !empty($new_instance['posttitlelink']) ? 1 : 0;
        $instance['categorytitlelink'] = !empty($new_instance['categorytitlelink']) ? 1 : 0;
		return $instance;
	}
	    
	function form($instance) {

		$instance = wp_parse_args((array) $instance, $defaults);
		$instance = wp_parse_args( (array) $instance, array('title' => 'Latest Video Post', 'category' => 'Videos', 'width' => '300', 'height' => '250'));
		$title = htmlspecialchars($instance['title']);
		$posttitlelink = isset( $instance['posttitlelink'] ) ? (bool) $instance['posttitlelink'] : false;
        $categorytitlelink = isset( $instance['categorytitlelink'] ) ? (bool) $instance['categorytitlelink'] : false;
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'latest-video-widget'); ?></label><br />
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title;?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'latest-video-widget'); ?></label>
			<input class="widefat" style="width: 80px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'latest-video-widget'); ?></label>
			<input class="widefat" style="width: 80px;" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('posttitlelink'); ?>" name="<?php echo $this->get_field_name('posttitlelink'); ?>"<?php checked( $posttitlelink ); ?> />
			<label for="<?php echo $this->get_field_id('posttitlelink'); ?>"><?php _e( 'Show post title', 'latest-video-widget' ); ?></label>
		</p>
        <p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('categorytitlelink'); ?>" name="<?php echo $this->get_field_name('categorytitlelink'); ?>"<?php checked( $categorytitlelink ); ?> />
			<label for="<?php echo $this->get_field_id('categorytitlelink'); ?>"><?php _e( 'Show post category', 'latest-video-widget' ); ?></label>
		</p>
		<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("webcodewrap_latestvideopost");'));

?>