<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">
				
				<?php if ($_GET['trashed']) { ?>
					<p class="notice">
						<?php $trashedpost = get_post($_GET['ids']); printf(__('<em>%s</em> has been moved to the <a href="%s">trash</a>.'), $trashedpost->post_title, get_option('site_url').'/wp-admin/edit.php?post_status=trash&post_type=post'); ?> 
					</p>
				<?php } ?>

				<div id="tag-trail">
					<h1 class="post-count">
						<?php echo $wp_query->found_posts .' <a href="feed/"><img src="'.get_stylesheet_directory_uri().'/rss-blue.png" alt="'.__('subscribe to an RSS feed of these tags', 'twentylinks').'" /></a>'; ?>
					</h1>
					<h1 class="page-title"><?php 
					$tag = get_query_var('tag');
					//var_dump($tag);
					$tags = explode('+', $tag);
					
					foreach ($tags as $atag) {
						$thetag = get_term_by('slug', $atag, 'post_tag', ARRAY_A);
						$taglink = '';
						$minusthistag = array_diff($tags, array($atag));
						if (empty($minusthistag)) $taglink = site_url() . '/';
						else $taglink = site_url() . '/tag/'.implode(‘+’, $minusthistag);
						$tagtitle .= '<span class="tag-name">'. $thetag['name'] . ' <a href="'.$taglink.'" class="remove-tag" title="remove '.$thetag['name'].'">&times;</a></span>';
					}
					
					echo $tagtitle;
					?></h1>
			
<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 get_template_part( 'loop', 'tag' );
?>
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>