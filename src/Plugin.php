<?php
namespace SnappyCP;

class Plugin {

	private $services = [];

	public function registerServices() {
		$this->AssetManager = new AssetManager();
		$this->AssetManager->hooks();

		$this->TemplateManager = new TemplateManager();
		$this->TemplateManager->hooks();
	}

	public function __get( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	public function __set( $name, $service ) {
		$this->services[ $name ] = $service;
	}
}
