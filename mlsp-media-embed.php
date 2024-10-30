<?php
/*
Plugin Name: MLSP Media Embed
Plugin URI: https://blog.myleadsystempro.com/mlsp-media-embed-for-wordpress
Description: Enables functionality for embedding media from MyLeadSystemPRO.com. Also allows you to activate a script that will allow all media embeds to resize dynamically (VERY useful for responsive themes!).
Version: 2.2
License: GPLv2
Author: Jeff Hoffman & Jim Fanale
Author URI: http://www.mlsp.com
*/

//
// ------------------------------------------------------------------
// THE FOLLOWING WILL ADD THE OPTION TO ACTIVATE RESPONSIVE MEDIA
// ------------------------------------------------------------------
//

function responsive_functionality_init() {
	// Add the section to media settings so we can add our
	add_settings_section( 'responsive_functionality_section',
		'<p>&nbsp;</p>Add Responsive Media Functionality',
		'responsive_functionality_section_callback_function',
		'media' );

	// Add the field 
	add_settings_field( 'responsive_functionality_name',
		'Add Responsive Media Functionality',
		'responsive_functionality_callback_function',
		'media',
		'responsive_functionality_section' );

	// Register our setting
	register_setting( 'media', 'responsive_functionality_name' );
}

add_action( 'admin_init', 'responsive_functionality_init' );


// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
// This function is needed if we added a new section. 
//

function responsive_functionality_section_callback_function() {
	echo '<p>This will activate a script that will resize your MLSP media on the fly based the the user\'s browser size.<br />
This is not needed for most modern themes, however if your MLSP videos are not responsive, this will help.
</p>';
}

// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
// creates a checkbox true/false option. Other types are surely possible
//

function responsive_functionality_callback_function() {
	echo '<input name="responsive_functionality_name" id="responsive_functionality" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'responsive_functionality_name' ), false ) . ' /> Activate';
}

// MAKE THE MAGIC HAPPEN
if ( get_option( 'responsive_functionality_name' ) ) {
	add_filter( 'embed_oembed_html', 'mlsp_video_wrapper', 10, 3 );
	add_action( 'wp_head', 'build_embed_stylesheet' );
}
if ( !function_exists( 'mlsp_video_wrapper' ) ):
	function mlsp_video_wrapper( $html, $url, $attr ) {
		if ( strpos( $html, 'myleadsystempro' ) !== false ) {
		// GET HEIGHT AND WIDTH OF EMBED
		$height = preg_match( '/height=\"([0-9]*)\"/', $html, $matches ) ? $matches[ 1 ] : 0;
		$width = preg_match( '/width=\"([0-9]*)\"/', $html, $matches ) ? $matches[ 1 ] : 0;
		// DO THE MATH TO GET THE CORRECT PADDING AMOUNT
		$padding = '';
		if ( $height > 0 ) {
			$padding = ' style="padding-bottom:' . round( ( $height / $width ) * 100, 2 ) . '%;"';
		} else {
            $padding = ' style="padding-bottom: 0.5625%;"';
        }
		// RETURN WRAPPING DIV WITH CORRECT PADDING AMOUNT IF MLSP VIDEO
		$add_class = 'class="mlsp-embed-container"';
		} 
		return '<div ' . $add_class . $padding . '>' . $html . '</div>';
	}
endif;

function build_embed_stylesheet() {
	?>
<style>
video {
    max-width: 100%;
    height: auto;
}
.mlsp-embed-container {
    height: auto;
    max-width: 100%;
    margin-bottom: 20px;
    overflow: hidden;
    position: relative;
    padding-top: 30px
}
.mlsp-embed-container iframe,   .mlsp-embed-container object,   .mlsp-embed-container embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
	height: 100%;
}
</style>
<?php
}

// ADD MLSP oEMBED
function add_mlsp_oembed() {
	wp_oembed_add_provider( '#http(s)?://(www\.)?(mlsp|mlmleadsystempro|myleadsystempro).com/media/*/*#i', 'https://www.myleadsystempro.com/media/oembed', true );
}
add_action( 'init', 'add_mlsp_oembed' );
?>
