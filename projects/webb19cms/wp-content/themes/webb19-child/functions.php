<?php

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles()
{

	$parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get( 'Version' )
	);
}

add_action( 'widgets_init', 'my_register_sidebars' );

function my_register_sidebars() {
	/* Register the 'primary' sidebar. */
	register_sidebar(
		array(
			'id'            => 'primary',
			'name'          => __( 'Primary Sidebar' ),
			'description'   => __( 'A short description of the sidebar.' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
	/* Repeat register_sidebar() code for additional sidebars. */
}