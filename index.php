<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Enable SVG Uploads
 * Plugin URI:        https://github.com/Lewiscowles1986/WordPressSVGPlugin
 * Description:       Enable SVG uploads in Media Library and other file upload fields.
 * Version:           1.8.1
 * Author:            Lewis Cowles
 * Author URI:        https://www.lewiscowles.co.uk/
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * GitHub Plugin URI: Lewiscowles1986/WordPressSVGPlugin
 */
namespace lewiscowles\WordPress\Compat\FileTypes;

class SVGSupport {

	function __construct() {
		add_action( 'admin_init', [ $this, 'add_svg_upload' ], 75 );
		add_action( 'admin_head', [ $this, 'custom_admin_css' ], 75 );
		add_action( 'load-post.php', [ $this, 'add_editor_styles' ], 75 );
		add_action( 'load-post-new.php', [ $this, 'add_editor_styles' ], 75 );
		add_action( 'after_setup_theme', [ $this, 'theme_prefix_setup' ], 75 );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'fix_mime_type_svg' ], 75, 4 );
	}

	public function theme_prefix_setup() {
		$existing = get_theme_support( 'custom-logo' );
		if ( $existing ) {
			$existing = current( $existing );
			$existing['flex-width'] = true;
			$existing['flex-height'] = true;
			add_theme_support( 'custom-logo', $existing );
		}
	}

	public function add_svg_upload() {
		ob_start();
		add_action( 'wp_ajax_adminlc_mce_svg.css', [ $this, 'tinyMCE_svg_css' ], 10 );
		add_filter( 'image_send_to_editor', [ $this, 'remove_dimensions_svg' ], 10, 1 );
		add_filter( 'upload_mimes', [ $this, 'filter_mimes' ], 10, 1 );
		add_action( 'shutdown', [ $this, 'on_shutdown' ], 0 );
		add_filter( 'final_output', [ $this, 'fix_template' ], 99, 1 );
	}

	public function add_editor_styles() {
		add_filter( 'mce_css', [ $this, 'filter_mce_css' ] );
	}

	public function filter_mce_css( $mce_css ) {
		global $current_screen;
		$mce_css .= ', ' . get_admin_url( 'admin-ajax.php?action=adminlc_mce_svg.css' );
		return $mce_css;
	}

	public function remove_dimensions_svg( $html = '' ) {
		return str_ireplace( [ " width=\"1\"", " height=\"1\"" ], "", $html );
	}

	public function custom_css() {
		echo 'img[src$=".svg"]:not(.emoji) { width: 100% !important; height: auto !important; }';
	}

	public function custom_admin_css() {
		echo '<style>';
		$this->custom_css();
		echo '</style>';
	}

	public function tinyMCE_svg_css() {
		header( 'Content-type: text/css' );
		$this->custom_css();
		exit();
	}

	public function filter_mimes( $mimes = [] ){
		$mimes[ 'svg' ] = 'image/svg+xml';
		return $mimes;
	}

	public function fix_mime_type_svg( $data=null, $file=null, $filename=null, $mimes=null ) {
		$ext = isset( $data['ext'] ) ? $data['ext'] : '';
		if( strlen($ext) < 1 ) {
			$ext = strtolower( end( explode('.', $filename) ) );
		}
		if( $ext === 'svg' ) {
			$data['type'] = 'image/svg+xml';
			$data['ext'] = 'svg';
		}
		return $data;
	}

	public function on_shutdown() {
		$final = '';
		$ob_levels = count( ob_get_level() );
		for ( $i = 0; $i < $ob_levels; $i++ ) {
			$final .= ob_get_clean();
		}
		echo apply_filters( 'final_output', $final );
	}

	public function fix_template( $content = '' ) {
		// Attachment window
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<img class="details-image cd2-svg" src="{{ data.url }}" draggable="false" alt="" />
			<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			$content
		);

		// Grid View
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<div class="centered">
					<img src="{{ data.url }}" class="thumbnail cd2-svg" draggable="false" alt="" />
				</div>
			<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			$content
		);

		// Attachment View (4.7)
		$content = str_replace(
			'<# } else if ( data.sizes && data.sizes.full ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<img class="details-image cd2-svg" src="{{ data.url }}" draggable="false" alt="" />
			<# } else if ( data.sizes && data.sizes.full ) { #>',
			$content
		);
		return $content;
	}
}
new SVGSupport();
