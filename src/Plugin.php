<?php
namespace CommandPalette;

class Plugin {

	private $services = [];

	public function registerServices() {
		if ( wp_is_mobile() ) {
			return;
		}

		$this->assetManager = new AssetManager();
		$this->assetManager->hooks();

		$this->templateManager = new TemplateManager();
		$this->templateManager->hooks();

		$this->sourceProvider = new SourceProvider();

		$this->itemManager = new ItemManager( $this->SourceProvider->getRegisteredSources() );
		$this->itemManager->hooks();

		$this->cacheManager = new CacheManager();
		$this->cacheManager->hooks();
	}

	public function __get( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	public function __set( $name, $service ) {
		$this->services[ $name ] = $service;
	}
}
