<?php

// local tag list

function tag_list_widget_setup() {
	register_widget('Tag_List_Widget');
}

add_action('widgets_init', 'tag_list_widget_setup');

class Tag_List_Widget extends WP_Widget {

	function Tag_List_Widget() {
			$widget_ops = array( 'description' => __('Lists all tags') );
			$control_ops = array( 'width' => 400, 'height' => 200 );
			$this->WP_Widget( 'tag_list', __('Tag List'), $widget_ops, $control_ops );
		}

	function widget($args, $instance) {
			extract( $args );
			echo $before_widget; 
			
			$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Top Tags' ) : $instance['title']);
			echo $before_title . $title . $after_title;
			?>
		    <ul id="tag-list-widget">
			<?php
			$tagbase = get_option('tag_base'); 
			if (empty($tagbase)) $tagbase = 'tag';
			
			$tags = get_terms('post_tag', array(
				'fields' => 'all', 
			    'orderby' => 'count',
				'order' => 'DESC',
				'number' => $instance['num']  ));
			foreach ($tags as $tag) {
				echo '<li><a href="/'.$tagbase.'/'.$tag->slug.'">'.$tag->name.'<span class="count">'.$tag->count.'</span></a></li>';
			}
			
			?>
		    </ul>
		   <?php
			echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['num'] = (int)$new_instance['num'];
			return $instance;
	}

	function form( $instance ) {
			//Defaults
				$instance = wp_parse_args( (array) $instance, array( 
						'title' => 'Top Tags',
						'num' => 45,
							));	
	?>  
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('Number of links to show:'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text" value="<?php echo $instance['num']; ?>" /></p>
		<?php
	}
}

// show clean URLs for multiple tag queries
// intercept tag $_POST and redirect to combined query
add_action('init', 'combine_tags_20links');
function combine_tags_20links() {
	if (isset($_POST['oldtags']) && !empty($_POST['oldtags'])) {
		wp_redirect( $_POST['oldurl'].'+'.$_POST['tag'] );
		exit;
	}
}

// redirect single posts to the linked url
// courtesy of Bill Erickson
// Find URL in post
function find_url_20links( $content ) {
	preg_match( '|href=["]([^\'^"]+)["]|mi', $content, $m );
	return $m[1];
}

// Redirect single posts to linked URL
function redirect_on_single_20links() {
	if ( is_single() ) {
		global $post;
		$url = find_url_20links( $post->post_content );
		if ( empty( $url ) ) return;
		wp_redirect( $url, '301' );
		exit;
	}
}
add_action( 'template_redirect', 'redirect_on_single_20links' );

// Press This style overrides
add_action("admin_head", 'press_this_css_20links');

function press_this_css_20links() {	?>
	<style type="text/css">
		body.press-this input#save, body.press-this div#submitdiv p, body.press-this div#categorydiv, body.press-this div#media-buttons { display: none; }
		body.press-this div#submitdiv p#publishing-actions { display: block; text-align: center; }
		body.press-this input#publish { float: none; }
	</style>
<?php 
}

// Mobile Layout
add_action('wp_head', 'device_width_20links');
function device_width_20links() {
	echo '<meta content="width = device-width, initial-scale = 0.8" name="viewport">';
}

// Add login link
add_action('wp_footer', 'login_link_20links');
function login_link_20links() {
	if (!is_user_logged_in()) { ?>
		<div id="login20links">
			<p><?php wp_loginout( site_url() ); ?></p>
		</div>
	<?php }
}

// jQuery tag autocompletion
add_action('wp_enqueue_scripts', 'add_jquery_20links');
add_action('wp_head', 'add_autosuggest_20links');

function add_jquery_20links() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'suggest' );
}

function add_autosuggest_20links() {
?>
    <script type="text/javascript">
    // Function to add tag auto suggest
    function setSuggest(id) {
        jQuery('#' + id).suggest("<?php echo admin_url('/admin-ajax.php?action=ajax-tag-search&tax=post_tag'); ?>", { delay: 500, minchars: 2, multiple: false });
    }
    </script>
<?php
}

// autocompletion for non-logged-in users
add_action('wp_ajax_nopriv_ajax-tag-search', 'add_autosuggest_20links_callback');

// cribbed from admin-ajax.php
function add_autosuggest_20links_callback() {
	global $wpdb;
	if ( isset( $_GET['tax'] ) ) {
		$taxonomy = sanitize_key( $_GET['tax'] );
		$tax = get_taxonomy( $taxonomy );
		if ( ! $tax )
			die( '0' );
	} else {
		die('0');
	}

	$s = stripslashes( $_GET['q'] );

	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = $s[count( $s ) - 1];
	}
	$s = trim( $s );
	if ( strlen( $s ) < 2 )
		die; // require 2 chars for matching

	$results = $wpdb->get_col( $wpdb->prepare( "SELECT t.name FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.name LIKE (%s)", $taxonomy, '%' . like_escape( $s ) . '%' ) );

	echo join( $results, "\n" );
}

// Translations can be filed in the /languages/ directory
load_theme_textdomain( 'twentylinks', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );
?>