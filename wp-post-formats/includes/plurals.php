<?php

/* Filter the post format archive title. */
add_filter( 'single_term_title', 'my_post_format_single_term_title' );

/**
 * Filters the single post format title used on the term archive page. The purpose of this
 * function is to replace the singular name with a plural version.
 *
 */
function my_post_format_single_term_title( $title ) {

	if ( is_tax( 'post_format' ) ) {
		$term = get_queried_object();
		$plural = my_post_format_get_plural_string( $term->slug );
		$title = !empty( $plural ) ? $plural : $title;
	}
	return $title;
}

/**
 * Gets the plural version of a post format name.
 */
function my_post_format_get_plural_string( $slug ) {

	$strings = my_post_format_get_plural_strings();

	$slug = str_replace( 'post-format-', '', $slug );

	return isset( $strings[ $slug ] ) ? $strings[ $slug ] : '';
}

function my_post_format_get_plural_strings() {

	$strings = array(
	//	'standard' => __( 'Articles',       'my-textdomain' ), // Would this ever be used?
		'aside'    => __( 'Asides',         'my-textdomain' ),
		'audio'    => __( 'Audio',          'my-textdomain' ), // Leave as "Audio"?
		'chat'     => __( 'Chats',          'my-textdomain' ),
		'image'    => __( 'Images',         'my-textdomain' ),
		'gallery'  => __( 'Galleries',      'my-textdomain' ),
		'link'     => __( 'Links',          'my-textdomain' ),
		'quote'    => __( 'Quotes',         'my-textdomain' ), // Use "Quotations"?
		'status'   => __( 'Status Updates', 'my-textdomain' ),
		'video'    => __( 'Videos',         'my-textdomain' ),
	);

	return apply_filters( 'my_post_format_plural_strings', $strings );
}
