<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */?>

<?php if (!is_tag()) { ?>
	<?php if ($_GET['trashed']) { ?>
		<p class="notice">
			<?php $trashedpost = get_post($_GET['ids']); printf(__('<em>%s</em> has been moved to the <a href="%s">trash</a>.'), $trashedpost->post_title, get_option('site_url').'/wp-admin/edit.php?post_status=trash&post_type=post'); ?> 
		</p>
	<?php } ?>
	
	<div id="tag-trail">
<?php }

if (is_home()) { ?>
	<h1 class="post-count">
		<?php echo $wp_query->found_posts .' <a href="feed/"><img src="'.get_stylesheet_directory_uri().'/rss-blue.png" alt="'.__('subscribe to an RSS feed of these tags', 'twentylinks').'" /></a>'; ?>
	</h1>
<?php }

$tagbase = get_option('tag_base'); 
if (empty($tagbase)) $tagbase = 'tag'; 
$oldurl = home_url().'/'.$tagbase.'/'.get_query_var('tag'); 
?>

<form id="tag-filter" action="<?php echo site_url(); ?>/" method="POST">
<input type="text" name="tag" id="tag" autocomplete="off" onfocus="setSuggest('tag');" placeholder="<?php _e("enter a tag", 'twentylinks'); ?>" />	
<input type="hidden" name="oldtags" id="oldtags" value="<?php echo get_query_var('tag'); ?>" />	
<input type="hidden" name="oldurl" id="oldurl" value="<?php echo $oldurl; ?>" />	
</form>

</div><!-- # tag-trail -->

<?php $first = TRUE; ?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php
	/* Start the Loop.
	 *
	 * In Twenty Ten we use the same loop in multiple contexts.
	 * It is broken into three main parts: when we're displaying
	 * posts that are in the gallery category, when we're displaying
	 * posts in the asides category, and finally all other posts.
	 *
	 * Additionally, we sometimes check for whether we are on an
	 * archive page, a search page, etc., allowing for small differences
	 * in the loop on each template without actually duplicating
	 * the rest of the loop that is shared.
	 *
	 * Without further ado, the loop:
	 */ ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); global $post; ?>

<?php if (is_new_day() && !$first) echo "</div><!-- day -->"; ?>
<?php $first = FALSE; ?>
<?php the_date('j M y', '<div class="day"><h2 class="date">', '</h2>', true); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="entry-content">
				<?php the_content(); ?>
			</div><!-- .entry-content -->
			<div class="entry-tags">
				<?php
				$thesetags = array();
				$queriedtags = get_query_var('tag');
				if (!empty($queriedtags))
					$thesetags = explode('+', $queriedtags);
				$alltags = get_the_tags();
				//var_dump($thesetags);
				//print_r($alltags);
				
				foreach ($thesetags as $atag) {
					// print highlighted, without links
					echo '<span class="single-tag"><a class="this-tag" href="'.get_term_link($atag, 'post_tag').'" title="switch to '.esc_attr($atag).'">'.$atag.'</a></span>';
				}
				
				foreach ($alltags as $atag) {
					if (!in_array($atag->name, $thesetags)) {
						// print linked tag, then print + tag link
						$taglink = '<a href="'.get_term_link($atag).'" title="switch to '.esc_attr($atag->name).'">'.$atag->name.'</a>';
						$plus = '';
						if (!is_home()) $plus = '<a class="plus" href="'.$oldurl.'+'.$atag->slug.'" title="add '.esc_attr($atag->name).'">+</a>';
						echo '<span class="single-tag">'.$taglink.$plus.'</span>';
					}
				}
				?>
			</div>

			<div class="entry-utility">
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
				<?php if (current_user_can('edit_posts')) { ?> <span class="meta-sep">|</span> <a href="<?php echo get_delete_post_link( $post->ID, '', false ) ?>">Delete</a> <?php } ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

<?php endwhile; // End the loop. Whew. ?>
</div><!-- day -->
<?php endif; ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php
 if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<?php if (function_exists('wp_pagenavi')) : wp_pagenavi(); else : ?>
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
					<?php endif; ?>
				</div><!-- #nav-below -->
<?php endif; ?>