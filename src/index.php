<?php
/**
 * WordPress SVG Plugin.
 *
 * @package LewisCowles\Plugin\PHP
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name:       Enable SVG Uploads
 * Version:           2.1.5
 * Plugin URI:        https://github.com/Lewiscowles1986/WordPressSVGPlugin
 * Description:       Enable SVG uploads in Media Library & other file upload fields.
 * Author:            Lewis Cowles
 * Author URI:        https://www.lewiscowles.co.uk/
 * Text Domain:       enable-svg-uploads
 * Domain Path:       /languages/
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.4
 * Requires PHP:      7.1.0
 * GitHub Plugin URI: Lewiscowles1986/WordPressSVGPlugin
 * Release Asset:     true
 */
namespace lewiscowles\Plugins\EnableSVG;

if ( version_compare( PHP_VERSION, '7.1', '<' ) ) {
	?>
	<div id="error-page">
		<p>This plugin requires PHP 7.1 or higher.
			Please contact your hosting provider about upgrading your
			server software. Your PHP version is
			<b><?php echo PHP_VERSION; ?></b></p>
	</div>
	<?php
	die();
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/svgsupport.php';

if ( defined( 'ABSPATH' ) ) {
	$svg_support = new \lewiscowles\WordPress\Compat\FileTypes\SVGSupport();
	$svg_support->init();
}
