<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Enable SVG Uploads
 * Plugin URI:        https://github.com/Lewiscowles1986/WordPressSVGPlugin
 * Description:       Enable SVG uploads in Media Library and other file upload fields.
 * Version:           2.1.1
 * Author:            Lewis Cowles
 * Author URI:        https://www.lewiscowles.co.uk/
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * GitHub Plugin URI: Lewiscowles1986/WordPressSVGPlugin
 * Release Asset: true
 */
namespace lewiscowles\WordPress\Compat\FileTypes;

if ( version_compare( PHP_VERSION, '7', '<' ) ) {
    ?>
    <div id="error-page">
        <p>This plugin requires PHP 7 or higher. Please contact your hosting provider about upgrading your
            server software. Your PHP version is <b><?php echo PHP_VERSION; ?></b></p>
    </div>
    <?php
    die();
}

use function lewiscowles\Utils\FileSystem\Extension\fixExtensionIfNeeded;


class SVGSupport {

	function init() {
		add_action( 'admin_init', [ $this, 'add_svg_upload' ], 75 );
		add_action( 'admin_head', [ $this, 'custom_admin_css' ], 75 );
		add_action( 'load-post.php', [ $this, 'add_editor_styles' ], 75 );
		add_action( 'load-post-new.php', [ $this, 'add_editor_styles' ], 75 );
		add_action( 'after_setup_theme', [ $this, 'theme_prefix_setup' ], 75 );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'fix_mime_type_svg' ], 75, 4 );
		add_filter( 'wp_update_attachment_metadata', [ $this, 'ensure_svg_metadata' ], 10, 2 );
	}

	public function add_svg_upload() {
		add_action( 'wp_ajax_adminlc_mce_svg.css', [ $this, 'tinyMCE_svg_css' ], 10 );
		add_filter( 'image_send_to_editor', [ $this, 'remove_dimensions_svg' ], 10, 1 );
		add_filter( 'upload_mimes', [ $this, 'filter_mimes' ], 10, 1 );
	}

	/**
	 * @codeCoverageIgnore
	 * if this breaks, php is broken
	 */
	public function custom_admin_css() {
		echo '<style>';
		$this->custom_css();
		echo '</style>';
	}

	public function add_editor_styles() {
		add_filter( 'mce_css', [ $this, 'filter_mce_css' ] );
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

	/**
	 * @codeCoverageIgnore
	 * Simple Wrapper for fixExtensionIfNeeded
	 */
	public function fix_mime_type_svg( $data=null, $file=null, $filename=null, $mimes=null ) {
		$OriginalExtension = (isset( $data['ext'] ) ? $data['ext'] : '');
                $ext = fixExtensionIfNeeded($OriginalExtension, $filename);
		if( $ext === 'svg' ) {
			$data['type'] = 'image/svg+xml';
			$data['ext'] = 'svg';
		}
		return $data;
	}

	public function ensure_svg_metadata( $data, $id ) {
		$attachment = get_post( $id );
		$mime_type = $attachment->post_mime_type;

		if ( $mime_type == 'image/svg+xml' ) {
			if( $this->missingOrInvalidSVGDimensions( $data ) ) {
				$xml = simplexml_load_file( get_attached_file( $id ) );
				$attr = $xml->attributes();
				$viewbox = explode( ' ', $attr->viewBox );

				$this->fillSVGDimensions( $viewbox, $attr, $data, 'width', 2 );
				$this->fillSVGDimensions( $viewbox, $attr, $data, 'height', 3 );
			}
		}
		return $data;
	}

	//
	// End of constructor functions.
	//

	/**
	 * @codeCoverageIgnore
	 * if this breaks, php is broken
	 */
	public function tinyMCE_svg_css() {
		header( 'Content-type: text/css' );
		$this->custom_css();
		exit();
	}

	public function remove_dimensions_svg( $html = '' ) {
		return str_ireplace( [ " width=\"1\"", " height=\"1\"" ], "", $html );
	}

	public function filter_mimes( $mimes = [] ){
		$mimes[ 'svg' ] = 'image/svg+xml';
		return $mimes;
	}

	//
	// End of admin_init hook functions
	//

	/**
	 * @codeCoverageIgnore
	 * if this breaks, wordpress is broken
	 */
	public function filter_mce_css( $mce_css ) {
		global $current_screen;
		$mce_css .= ', ' . get_admin_url( 'admin-ajax.php?action=adminlc_mce_svg.css' );
		return $mce_css;
	}

	//
	// End of filter mce css hook
	//

	/**
	 * @codeCoverageIgnore
	 * if this breaks, php is broken
	 */
	protected function custom_css() {
		echo 'body:not(.wp-admin) img[src$=".svg"]:not(.emoji) { width: 100% !important; height: auto !important; }';
	}

	protected function missingOrInvalidSVGDimensions( $data ) {
		if(!is_array($data)) return true;
		if(!isset($data['width']) || !isset($data['height']) ) return true;
		if(is_nan($data['width']) || is_nan($data['height']) ) return true;
		return (
			empty( $data ) || empty( $data['width'] ) || empty( $data['height'] )
			||
			intval($data['width'] < 1) || intval($data['height'] < 1)
		);
	}

	protected function fillSVGDimensions( $viewbox, $attr, &$data, $dimension, $viewboxoffset ) {
		if ( isset( $attr->{ $dimension } ) ) {
			$data[ $dimension ] = intval( $attr->{ $dimension } );
		}
		if( !isset( $data[ $dimension ] ) ) {
			$data[ $dimension ] = 0;
		}
		if ( is_nan( $data[ $dimension ] ) ) {
			$data[ $dimension ] = 0;
		}
		if ( $data[ $dimension ] < 1 ) {
			$data[ $dimension ] = count($viewbox) == 4 ? intval( $viewbox[$viewboxoffset] ) : null;
		}
	}
}

if (defined('ABSPATH')) {
	require_once(__DIR__.'/vendor/autoload.php');
	$svg_support = new SVGSupport();
	$svg_support->init();
}
