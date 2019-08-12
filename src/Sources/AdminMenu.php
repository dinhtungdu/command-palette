<?php
namespace CommandPalette\Sources;

class AdminMenu extends Base {

	public function get_id() {
		return 'AdminMenu';
	}

	protected function prepareItems() {
		global $menu, $submenu;

		array_map(
			[ $this, 'addMenuItem' ],
			array_filter( $menu, [ $this, 'removeInvalidItem' ] )
		);

		foreach ( $submenu as $parentMenu => $submenuItems ) {
			$this->addSubmenuItems( $parentMenu, $submenuItems );
		}
	}

	private function addMenuItem( $menuItem ) {
		$this->addItem(
			[
				'id'         => $menuItem[2],
				'capability' => $menuItem[1],
				'title'      => $this->removeSpan( $menuItem[0] ),
				'url'        => $this->processAdminUrl( $menuItem[2] ),
			]
		);
	}

	private function addSubmenuItems( $parentMenu, $submenuItems ) {
		foreach ( $submenuItems as $menuItem ) {
			$this->addItem(
				[
					'id'         => $menuItem[2] . '-' . $menuItem[1],
					'capability' => $menuItem[1],
					'title'      => $this->removeSpan( $menuItem[0] ),
					'url'        => $this->processAdminUrl( $menuItem[2] ),
					'category'   => $this->items[ $parentMenu ]['title'],
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
