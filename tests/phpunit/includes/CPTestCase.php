<?php
class CPTestCase extends WP_Mock\Tools\TestCase {
	public function setUp(): void {
		WP_Mock::passthruFunction( 'plugin_dir_url' );
		WP_Mock::passthruFunction( 'plugin_dir_path' );
		parent::setUp();
	}

	public function tearDown(): void {
		$this->addToAssertionCount(
			Mockery::getContainer()->mockery_getExpectationCount()
		);
		parent::tearDown();
	}
}
