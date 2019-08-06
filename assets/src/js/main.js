import Mousetrap from 'mousetrap';
import 'mousetrap/plugins/global-bind/mousetrap-global-bind';

class CommandPalette {
	constructor() {
		this.cacheVariables();
		this.registerEvents();
		this.registerKeyboardShortcut();
	}

	cacheVariables() {
		this.dialog = document.getElementById( 'command-palette-wrapper' );
		this.dialogClose = document.getElementById( 'command-palette-dialog-close' );
		this.searchInput = document.getElementById( 'command-palette-search-input' );
	}

	registerEvents() {
		this.dialogClose.addEventListener( 'click', this.hideDialog.bind( this ) );
	}

	registerKeyboardShortcut() {
		Mousetrap.bind( 'shift shift', () => {
			this.showDialog();
			this.focusInput();
		} );

		Mousetrap.bindGlobal( 'esc', this.hideDialog.bind( this ) );
	}

	showDialog() {
		this.dialog.style.display = 'block';
	}

	hideDialog() {
		this.dialog.style.display = 'none';
	}

	focusInput() {
		this.searchInput.focus();
	}

	clearInput() {
		this.searchInput.value = '';
	}
}

new CommandPalette();
