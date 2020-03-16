<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package webb19
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

            $lyrics = get_post_meta( get_the_ID(), 'lyrics', true );

            var_dump($lyrics);

            $composer_ids = get_post_meta( get_the_ID(), 'composer' );
            var_dump($composer_ids);

            $args = [
                'p'         => $composer_ids,
                'post_type' => 'composer'
            ];

            $composers = new WP_Query($args);

            if ( $composers->have_posts() ) {
                while ( $composers->have_posts() ) { 
                    $composers->the_post();
                    echo '<p class="composer">' . get_the_title() . '</p>';
                }
                wp_reset_postdata();
            }


			the_post_navigation();

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
