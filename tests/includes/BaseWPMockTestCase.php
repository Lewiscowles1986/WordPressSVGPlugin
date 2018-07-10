<?php

class BaseWPMockTestCase extends \PHPUnit\Framework\TestCase
{
    function setUp()
    {
        \WP_Mock::setUsePatchwork( true );
        \WP_Mock::setUp();
        \WP_Mock::wpFunction( 'absint' )->andReturnUsing(
          function ($in) {
            $test = intval($in);
            return is_nan($test) ? 0 : $test;
          }
        );
        $this->enqueue_style = \WP_Mock::wpFunction( 'wp_enqueue_style' );
        $this->enqueue_script = \WP_Mock::wpFunction( 'wp_enqueue_script' );
    }
    function tearDown()
    {
        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );
        \WP_Mock::tearDown();
    }
}
