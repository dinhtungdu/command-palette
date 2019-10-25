<?php
class CPTestCase extends WP_Mock\Tools\TestCase {
	public function tearDown(): void {
		$this->addToAssertionCount(
			Mockery::getContainer()->mockery_getExpectationCount()
		);
		parent::tearDown();
	}
}
