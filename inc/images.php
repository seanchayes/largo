<?php

if ( !defined( 'LARGE_WIDTH') ) {
	define( 'LARGE_WIDTH', 771 );
}

if ( !defined( 'MEDIUM_WIDTH') ) {
	define( 'MEDIUM_WIDTH', 336 );
}

function largo_create_image_sizes() {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 140, 140, true ); // skybox thumbnail
    add_image_size( '60x60', 60, 60, true ); // in case you missed it thumbnail
    add_image_size( 'medium', MEDIUM_WIDTH, 9999 ); // medium width scaling
    add_image_size( 'large', LARGE_WIDTH, 9999 ); // large width scaling
    add_image_size( 'full', 1170, 9999 ); // large width scaling
}
add_action( 'after_setup_theme', 'largo_create_image_sizes' );

/**
 * Replace all the defaults in settings > media with our preferred settins
 */
function largo_set_media_options() {
	update_option('thumbnail_size_w', 140);
    update_option('thumbnail_size_h', 140);
    update_option('thumbnail_crop', 1);
    update_option('medium_size_w', 336);
    update_option('medium_size_h', 9999);
    update_option('large_size_w', 771);
    update_option('large_size_h', 9999);
	update_option('embed_autourls', 1);
	update_option('embed_size_w', 771);
	update_option('embed_size_h', 9999);
}
add_action( 'after_setup_theme', 'largo_set_media_options' );


/**
 * largo_get_image_tag(): Renders an <img /> tag for attachments, scaling it
 * to $size and guaranteeing that it's not wider than the content well.
 *
 * This is largely taken from get_image_tag() in wp-includes/media.php.
 */
function largo_get_image_tag( $html, $id, $alt, $title, $align, $size ) {
    // Never allow an image wider than the LARGE_WIDTH
    if ( $size == 'full' ) {
        list( $img_src, $width, $height ) = wp_get_attachment_image_src( $id, $size );
        if ( $width > LARGE_WIDTH ) {
            $size = 'large';
        }
    }

    list( $img_src, $width, $height ) = image_downsize( $id, $size );
    $hwstring = image_hwstring( $width, $height );

    // XXX: may not need all these classes
    $class = 'align' . esc_attr( $align ) .' size-' .  esc_attr( $size ) .
             ' wp-image-' . $id;
    $class = apply_filters( 'get_image_tag_class', $class, $id, $align,
                            $size );

    $html = '<img src="' . esc_attr( $img_src ) .
        '" alt="' . esc_attr( $alt ) .
        '" title="' . esc_attr( $title ) . '" ' .
        $hwstring . 'class="' . $class . '" />';

    return $html;
}
add_filter( 'get_image_tag', 'largo_get_image_tag', 10, 6 );

function attachment_image_link_remove_filter( $content ) {
    $content =
        preg_replace(
            array('{<a(.*?)(wp-att|wp-content\/uploads)[^>]*><img}',
                '{ wp-image-[0-9]*" /></a>}'),
            array('<img','" />'),
            $content
        );
    return $content;
}
add_filter( 'the_content', 'attachment_image_link_remove_filter' );

?>