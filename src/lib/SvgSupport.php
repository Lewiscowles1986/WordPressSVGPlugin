<?php

namespace lewiscowles\WordPress\Compat\FileTypes;

use function lewiscowles\Utils\FileSystem\Extension\fixExtensionIfNeeded;

/**
 * AIO Grab-bag of functionality to enable this plugin
 *
 * @package LewisCowles\Plugin\PHP\SvgSupport
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */
class SvgSupport {

	/**
	 * Setup Class
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', [ $this, 'add_svg_upload' ], 75 );
		add_action( 'admin_head', [ $this, 'custom_admin_css' ], 75 );
		add_action( 'load-post.php', [ $this, 'add_editor_styles' ], 75 );
		add_action( 'load-post-new.php', [ $this, 'add_editor_styles' ], 75 );
		add_action( 'after_setup_theme', [ $this, 'theme_prefix_setup' ], 75 );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'fix_mime_type_svg' ], 75, 4 );
		add_filter(
			'wp_update_attachment_metadata',
			[ $this, 'ensure_svg_metadata' ],
			10,
			2
		);
	}

	/**
	 * Add Admin Legacy AJAX for CSS & Filters
	 *
	 * @return void
	 */
	public function add_svg_upload() {
		add_action( 'wp_ajax_adminlc_mce_svg.css', [ $this, 'tinymce_svg_css' ], 10 );
		add_filter(
			'image_send_to_editor',
			[ $this, 'remove_incorrect_dimensions_svg' ],
			10,
			1
		);
		add_filter( 'upload_mimes', [ $this, 'filter_mimes' ], 10, 1 );
	}

	/**
	 * Inline Admin CSS
	 *
	 * @codeCoverageIgnore
	 * if this breaks, php is broken
	 *
	 * @return void
	 */
	public function custom_admin_css() {
		echo '<style>';
		$this->custom_css();
		echo '</style>';
	}

	/**
	 * Enqueue TinyMCE custom CSS using WordPress filter
	 *
	 * @return void
	 */
	public function add_editor_styles() {
		add_filter( 'mce_css', [ $this, 'filter_mce_css' ] );
	}

	/**
	 * Augment Theme to ensure that custom logo support is added
	 *
	 * @return void
	 */
	public function theme_prefix_setup() {
		$existing = get_theme_support( 'custom-logo' );
		if ( $existing ) {
			$existing                = current( $existing );
			$existing['flex-width']  = true;
			$existing['flex-height'] = true;
			add_theme_support( 'custom-logo', $existing );
		}
	}

	/**
	 * Fix the MIME type returned for SVG
	 *
	 * @param array|null  $data     Incoming MIME data.
	 * @param object|null $file     Unused.
	 * @param string|null $filename The Filename.
	 * @param array       $mimes    Unused.
	 *
	 * @codeCoverageIgnore
	 * Simple Wrapper for fixExtensionIfNeeded
	 *
	 * @return array
	 */
	public function fix_mime_type_svg(
		$data = null, $file = null, $filename = null, $mimes = null
	) {
		$original_extension = ( isset( $data['ext'] ) ? $data['ext'] : '' );
		$ext                = fixExtensionIfNeeded( $original_extension, $filename );
		if ( $ext === 'svg' ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = 'svg';
		}
		return $data;
	}

	/**
	 * Ensure uploaded media contains width and height metadata
	 *
	 * @param array $data An array of medatadata for media.
	 * @param int   $id   The Post ID of the upload.
	 *
	 * @return array
	 */
	public function ensure_svg_metadata( $data, $id ) {
		$attachment = get_post( $id );
		$mime_type  = $attachment->post_mime_type;

		if ( $mime_type == 'image/svg+xml' ) {
			if ( $this->missing_or_invalid_svg_dimensions( $data ) ) {
				$xml           = simplexml_load_file( get_attached_file( $id ) );
				$attr          = $xml->attributes();
				$view_box_attr = 'viewBox';
				$viewbox       = explode( ' ', $attr->{$view_box_attr} );

				$this->fill_in_svg_dimensions( $viewbox, $attr, $data, 'width', 2 );
				$this->fill_in_svg_dimensions( $viewbox, $attr, $data, 'height', 3 );
			}
		}
		return $data;
	}

	//
	// End of constructor functions.
	//

	/**
	 * Legacy AJAX CSS Handler
	 *
	 * @codeCoverageIgnore
	 * if this breaks, php is broken
	 *
	 * @return void
	 */
	public function tinymce_svg_css() {
		header( 'Content-type: text/css' );
		$this->custom_css();
		exit();
	}

	/**
	 * Remove Incorrect Dimensions (height 1, width 1) from HTML output
	 *
	 * @param string $html an HTML document or fragment.
	 *
	 * @return string
	 */
	public function remove_incorrect_dimensions_svg( $html = '' ) {
		return str_ireplace( [ ' width="1"', ' height="1"' ], '', $html );
	}

	/**
	 * Ensure SVG has MIME
	 *
	 * @param array $mimes A List of key-value pairs of file extension & mime-type.
	 *
	 * @return array
	 */
	public function filter_mimes( $mimes = [] ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Adds CSS URL to CSV list of URLs
	 *
	 * @param string $mce_css A Comma-delimited string of urls to CSS files for TinyMCE.
	 *
	 * @codeCoverageIgnore
	 * if this breaks, wordpress is broken
	 *
	 * @return string
	 */
	public function filter_mce_css( $mce_css ) {
		global $current_screen;
		$mce_css .= ', ' . get_admin_url( 'admin-ajax.php?action=adminlc_mce_svg.css' );
		return $mce_css;
	}

	/**
	 * Custom CSS applied to backend & TinyMCE
	 *
	 * @codeCoverageIgnore
	 * if this breaks, php is broken
	 *
	 * @return void
	 */
	protected function custom_css() {
		echo 'body:not(.wp-admin) img[src$=".svg"]:not(.emoji) {' .
			'width: 100% !important; height: auto !important; }';
	}

	/**
	 * Is SVG Data Missing Dimensions or containing invalid dimensions
	 *
	 * @param array|null $data Dimension data (possibly null).
	 *
	 * @return bool
	 */
	protected function missing_or_invalid_svg_dimensions( $data ) {
		if ( ! is_array( $data ) ) {
			return true;
		}
		if ( ! isset( $data['width'] ) || ! isset( $data['height'] ) ) {
			return true;
		}
		if ( is_nan( $data['width'] ) || is_nan( $data['height'] ) ) {
			return true;
		}
		return (
			empty( $data ) || empty( $data['width'] ) || empty( $data['height'] )
			||
			intval( $data['width'] < 1 ) || intval( $data['height'] < 1 )
		);
	}

	/**
	 * Try to Get Width & Height From a variety of SVG data-points
	 *
	 * @param array      $viewbox       ViewBox Data.
	 * @param object     $attr          SVG / XML Object.
	 * @param array      $data          Metadata for SVG attachment.
	 * @param string     $dimension     Key / Property name. 'width' or 'height'.
	 * @param string|int $viewbox_offset ViewBox Offset for value.
	 *
	 * @return void
	 */
	protected function fill_in_svg_dimensions(
		$viewbox, $attr, &$data, $dimension, $viewbox_offset
	) {
		if ( isset( $attr->{ $dimension } ) ) {
			$data[ $dimension ] = intval( $attr->{ $dimension } );
		}
		if ( ! isset( $data[ $dimension ] ) ) {
			$data[ $dimension ] = 0;
		}
		if ( is_nan( $data[ $dimension ] ) ) {
			$data[ $dimension ] = 0;
		}
		if ( $data[ $dimension ] < 1 ) {
			$data[ $dimension ] = ( count( $viewbox ) === 4 ) ? intval( $viewbox[ $viewbox_offset ] ) : null;
		}
	}
}
