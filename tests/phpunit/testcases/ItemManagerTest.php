<?php
use CommandPalette\ItemManager;

class ItemManagerTest extends CPTestCase {
	public $instance;

	public function setUp() : void {
		$this->instance = new ItemManager( [] );
		parent::setUp();
	}

	public function testHooks() {
		WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $this->instance, 'printItemsJson' ] );
		$this->instance->hooks();
	}

	// public function testPrintItemsJson() {
		// WP_Mock::userFunction( 'wp_localize_script' )
			// ->with( Mockery::type( 'string' ), Mockery::type( 'string' ), Mockery::type('array' ) )
			// ->andReturnTrue();
		// $this->assertTrue( $this->instance->printItemsJson() );
	// }

	// public function testGetItemsForCurrentUser() {

	// }
}
