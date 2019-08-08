<?php
namespace CommandPalette;

use PHP_CodeSniffer\Reports\Source;

class SourceProvider {
	private $registeredSources;

	public function __construct() {
		$this->registerSources();
	}

	public function getSources() {
		return apply_filters(
			'command_palette_sources',
			[
				'AdminPages',
			]
		);
	}

	public function getRegisteredSources() {
		return $this->registeredSources;
	}

	private function registerSources() {
		array_map(
			[ $this, 'registerSingleSource' ],
			$this->getSources()
		);
	}

	private function registerSingleSource( $sourceClass ) {
		$sourceInstance = new $sourceClass();

		if ( ! $sourceInstance instanceof Sources\Base ) {
			return new \WP_Error( 'wrong_command_source' );
		}

		$this->registeredSources[ $sourceInstance->get_id() ] = $sourceInstance;
	}
}
