<?php
/**
 * Template Name: Webb19 custom page
 * Template Post Type: album 
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

        <h1>This is a custom template page</h1>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
