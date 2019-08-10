<?php
namespace CommandPalette;

class SourceProvider {
	private $registeredSources = [];

	public function getSources() {
		return apply_filters(
			'command_palette_sources',
			[ 'AdminMenu', 'Custom' ]
		);
	}

	public function getRegisteredSources() {
		if ( empty( $this->registeredSources ) ) {
			$this->registerSources();
		}

		return $this->registeredSources;
	}

	private function registerSources() {
		array_map(
			[ $this, 'registerSingleSource' ],
			$this->getSources()
		);
	}

	private function registerSingleSource( $sourceClass ) {
		$sourceClass    = __NAMESPACE__ . '\\Sources\\' . $sourceClass;
		$sourceInstance = new $sourceClass();

		if ( ! $sourceInstance instanceof Sources\Base ) {
			return new \WP_Error( 'wrong_command_source' );
		}

		$this->registeredSources[ $sourceInstance->get_id() ] = $sourceInstance;
	}
}
