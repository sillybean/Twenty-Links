	</div><!-- #main -->

	<div id="footer" role="contentinfo">
		<div id="colophon">

<?php get_sidebar( 'footer' ); ?>

			<div id="site-info">
				<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div><!-- #site-info -->

			<div id="site-generator">
				<?php do_action( 'twentyten_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentyten' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentyten' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'twentyten' ), 'WordPress' ); ?></a>
				<?php printf( __(' and the <a class="twentylinks" href="%s">Twenty Links theme</a>'), 'http://sillybean.net/code/themes/twenty-links-a-delicious-inspired-child-theme-for-wordpress/' ); ?>
			</div><!-- #site-generator -->

		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>
</body>
</html>