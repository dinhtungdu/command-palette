<?php
namespace CommandPalette\Sources;

class AdminMenu extends Base {

	public function get_id() {
		return 'AdminPages';
	}

	protected function prepareItems() {
		global $menu, $submenu;

		array_map(
			[ $this, 'addMenuItem' ],
			array_filter( $menu, [ $this, 'removeInvalidItem' ] )
		);

		array_map(
			[ $this, 'addSubmenuItems' ],
			$submenu
		);
	}

	private function addMenuItem( $menuItem ) {
		$this->addItem(
			[
				'id'         => $menuItem[5],
				'capability' => $menuItem[1],
				'title'      => $this->removeSpan( $menuItem[0] ),
				'url'        => $this->processAdminUrl( $menuItem[2] ),
				'category'   => __( 'Admin menu', 'command-palette' ),
			]
		);
	}

	private function addSubmenuItems( $submenuItems ) {
		foreach ( $submenuItems as $menuItem ) {
			$this->addItem(
				[
					'id'         => $menuItem[2] . '-' . $menuItem[1],
					'capability' => $menuItem[1],
					'title'      => $this->removeSpan( $menuItem[0] ),
					'url'        => $this->processAdminUrl( $menuItem[2] ),
					'category'   => __( 'Admin menu', 'command-palette' ),
				]
			);
		}
	}

	private function processAdminUrl( $text ) {
		if ( strpos( $text, '.php' ) !== false ) {
			return admin_url( $text );
		}
		return admin_url( 'admin.php?page=' . $text );
	}

	private function removeInvalidItem( $menuItem ) {
		return (bool) $menuItem[0];
	}

	private function removeSpan( $text ) {
			$text = preg_replace( '/<span.*<\/span>/s', '', $text );
			return trim( $text );
	}
}
