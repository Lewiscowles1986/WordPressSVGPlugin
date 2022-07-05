<?php

namespace lewiscowles\WordPress\Compat\FileTypes\Tests;

use \lewiscowles\WordPress\Compat\FileTypes\SvgSupport;
/**
 * Unit Tests for the Main Plugin File
 */
class TestSVGMetaDataLogic extends \BaseWPMockTestCase
{
  /**
   * @test
   * @dataProvider svg_metadata_provider
   */
  public function ensure_svg_metadata($post_id, $dimensions, $att_data, $data )
  {
    \WP_Mock::wpFunction( 'get_post' )
      ->andReturnUsing(
        function ($post_id) use ($att_data) {
          return $att_data;
        });

    \WP_Mock::wpFunction( 'get_attached_file' )
      ->andReturnUsing(
        function ($id) {
          return __DIR__ . "/fixtures/{$id}.svg";
        });

    $pluginInstance = new SvgSupport();
    $result = $pluginInstance->ensure_svg_metadata($data, $post_id);

    $this->assertEquals($dimensions['width'], $result['width']);
    $this->assertEquals($dimensions['height'], $result['height']);
  }

  public static function svg_metadata_provider()
  {
    return [
      [
        5402,
        ['width'=>185, 'height'=>125],
        (object)[
          'post_mime_type' => 'image/svg+xml'
        ],
        []
      ],
      [
        5402,
        ['width'=>185, 'height'=>125],
        (object)[
          'post_mime_type' => 'image/svg+xml'
        ],
        ['width'=>0,'height'=>0]
      ],
      [
        5418,
        ['width'=>256, 'height'=>256],
        (object)[
          'post_mime_type' => 'image/svg+xml'
        ],
        []
      ],
      [
        5430,
        ['width'=>200, 'height'=>71],
        (object)[
          'post_mime_type' => 'image/svg+xml'
        ],
        []
      ],
      [
        5430,
        ['width'=>200, 'height'=>71],
        (object)[
          'post_mime_type' => 'image/svg+xml'
        ],
        ['width'=>NAN, 'height'=>NAN]
      ],
      [
        5430,
        ['width'=>200, 'height'=>200],
        (object)[
          'post_mime_type' => 'notansvg'
        ],
        ['width'=>200, 'height'=>200]
      ]
    ];
  }
}
