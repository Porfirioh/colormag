<?php
/**
 * Theme Single Post Section for our theme.
 *
 * @package    ThemeGrill
 * @subpackage ColorMag
 * @since      ColorMag 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Hook: colormag_before_body_content.
 */
do_action( 'colormag_before_body_content' );
?>

	<div id="primary">
		<div id="content" class="clearfix">

			<?php
			/**
			 * Hook: colormag_before_single_post_page_loop.
			 */
			do_action( 'colormag_before_single_post_page_loop' );

			while ( have_posts() ) :
				the_post();

				get_template_part( 'content', 'single' );
			endwhile;

			/**
			 * Hook: colormag_after_singl_poste_page_loop.
			 */
			do_action( 'colormag_after_single_post_page_loop' );
			?>

		</div><!-- #content -->

		<?php
		if ( true === apply_filters( 'colormag_single_post_page_navigation_filter', true ) ) :
			get_template_part( 'navigation', 'single' );
		endif;

		/**
		 * Functions hooked into colormag_action_after_single_post_content action.
		 */
		do_action( 'colormag_action_after_single_post_content' );
		?>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
			<div class="author-box">
				<div class="author-img"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '100' ); ?></div>
				<h4 class="author-name"><?php the_author_meta( 'display_name' ); ?></h4>
				<p class="author-description"><?php the_author_meta( 'description' ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( get_theme_mod( 'colormag_related_posts_activate', 0 ) == 1 ) {
			get_template_part( 'inc/related-posts' );
		}
		?>

		<?php
		/**
		 * Hook: colormag_before_comments_template.
		 */
		do_action( 'colormag_before_comments_template' );

		/**
		 * Functions hooked into colormag_action_after_inner_content action.
		 *
		 * @hooked colormag_render_comments - 10
		 */
		do_action( 'colormag_action_comments' );

		/**
		 * Hook: colormag_after_comments_template.
		 */
		do_action( 'colormag_after_comments_template' );
		?>

	</div><!-- #primary -->

<?php
colormag_sidebar_select();

/**
 * Hook: colormag_after_body_content.
 */
do_action( 'colormag_after_body_content' );

get_footer();
