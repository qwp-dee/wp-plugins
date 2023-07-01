<?php

/**
 * Video
 */


function post_formats_video_content( $content ) {

	if ( has_post_format( 'video' ) ) {

		$embed = get_post_meta( get_the_ID(), '_format_video_embed', true );

		if ( ! empty( $embed ) && strpos( $content, $embed ) === false ) {
			$content = $embed . "\n\n" . $content;
		}

	}
	return $content;
}

add_filter( 'the_content', 'post_formats_video_content' );
