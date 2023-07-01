<?php

/**
 * Aside
 */

add_filter( 'the_content', 'post_formats_aside_to_infinity_and_beyond', 9 ); // run before wpautop

function post_formats_aside_to_infinity_and_beyond( $content ) {

	/* Add the infinity symbol, linking to the post's permalink */
	if ( has_post_format( 'aside' ) && ! is_singular() ) {
		$content .= ' <a href="' . get_permalink() . '">&#8734;</a>';
	}

	return $content;
}
