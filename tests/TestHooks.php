<?php


namespace lewiscowles\WordPress\Compat\FileTypes\Tests;

use \lewiscowles\WordPress\Compat\FileTypes\SVGSupport;
/**
 * Unit Tests for the Main Plugin File
 */
class TestHooks extends \BaseWPMockTestCase
{

  /**
   * @test
   */
  public function plugin_init_actions_fire()
  {
    $pluginInstance = new SVGSupport();

    \WP_Mock::expectActionAdded( 'admin_init', [ $pluginInstance, 'add_svg_upload' ], 75 );
		\WP_Mock::expectActionAdded( 'admin_head', [ $pluginInstance, 'custom_admin_css' ], 75 );
		\WP_Mock::expectActionAdded( 'load-post.php', [ $pluginInstance, 'add_editor_styles' ], 75 );
		\WP_Mock::expectActionAdded( 'load-post-new.php', [ $pluginInstance, 'add_editor_styles' ], 75 );
		\WP_Mock::expectActionAdded( 'after_setup_theme', [ $pluginInstance, 'theme_prefix_setup' ], 75 );

    $pluginInstance->init();

    \WP_Mock::assertHooksAdded();
  }

  /**
   * @test
   */
  public function plugin_init_filters_fire()
  {
    $pluginInstance = new SVGSupport();

		\WP_Mock::expectFilterAdded( 'wp_check_filetype_and_ext', [ $pluginInstance, 'fix_mime_type_svg' ], 75, 4 );
		\WP_Mock::expectFilterAdded( 'wp_update_attachment_metadata', [ $pluginInstance, 'ensure_svg_metadata' ], 10, 2 );

    $pluginInstance->init();

    \WP_Mock::assertHooksAdded();
  }

  /**
   * @test
   */
  public function add_svg_upload_actions_fire()
  {
    $pluginInstance = new SVGSupport();

    \WP_Mock::expectActionAdded( 'wp_ajax_adminlc_mce_svg.css', [ $pluginInstance, 'tinyMCE_svg_css' ], 10 );

    $pluginInstance->add_svg_upload();

    \WP_Mock::assertHooksAdded();
  }

  /**
   * @test
   */
  public function add_svg_upload_filters_fire()
  {
    $pluginInstance = new SVGSupport();

    \WP_Mock::expectFilterAdded( 'image_send_to_editor', [ $pluginInstance, 'remove_dimensions_svg' ], 10, 1 );
    \WP_Mock::expectFilterAdded( 'upload_mimes', [ $pluginInstance, 'filter_mimes' ], 10, 1 );

    $pluginInstance->add_svg_upload();

    \WP_Mock::assertHooksAdded();
  }

  /**
   * @test
   */
  public function add_editor_styles_filters_fire()
  {
    $pluginInstance = new SVGSupport();

    \WP_Mock::expectFilterAdded( 'mce_css', [ $pluginInstance, 'filter_mce_css' ] );

    $pluginInstance->add_editor_styles();

    \WP_Mock::assertHooksAdded();
  }
}
