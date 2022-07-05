<?php

namespace lewiscowles\WordPress\Compat\FileTypes\Tests;

use \lewiscowles\WordPress\Compat\FileTypes\SvgSupport;
/**
 * Unit Tests for the Main Plugin File
 */
class TestThemeLogic extends \BaseWPMockTestCase
{
  /**
   * @test
   * @dataProvider theme_custom_logo_provider
   */
  public function theme_with_custom_logo_flex_works($theme_custom_logo_support)
  {
    $pluginInstance = new SvgSupport();

    /* cheeky way of raising exception if the function does not set the
       flex-{width,height} of custom-logo to true in the case it is set */
    $add_theme_support = \WP_Mock::wpFunction( 'add_theme_support' )->once()
      ->andReturnUsing(
        function ($name, $data) {
          if( (!$data['flex-width']) || (!$data['flex-height']) ) {
            throw new Exception('add_theme_support should always receive true if called');
          }
        }
      );

    \WP_Mock::wpFunction( 'get_theme_support' )
      ->with( 'custom-logo' )
      ->andReturn( $theme_custom_logo_support );

    try {
      $pluginInstance->theme_prefix_setup();
    } catch(\Exception $e) {
      $this->fail('there should be no exceptions');
    }
  }

  public static function theme_custom_logo_provider()
  {
    return [
      [[['flex-width'=>true, 'flex-height'=>true]]],
      [[['flex-width'=>true, 'flex-height'=>false]]],
      [[['flex-width'=>false, 'flex-height'=>true]]],
      [[['flex-width'=>false, 'flex-height'=>false]]],
    ];
  }
}
