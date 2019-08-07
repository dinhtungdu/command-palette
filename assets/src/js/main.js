import Mousetrap from 'mousetrap';
import 'mousetrap/plugins/global-bind/mousetrap-global-bind';

class CommandPalette {
	constructor() {
		this.cacheVariables();
		this.registerEvents();
		this.registerKeyboardShortcut();
	}

	cacheVariables() {
		this.wrapper = document.getElementById( 'command-palette-wrapper' );
		this.dialog = document.getElementById( 'command-palette-dialog' );
		this.dialogClose = document.getElementById( 'command-palette-dialog-close' );
		this.searchInput = document.getElementById( 'command-palette-search-input' );
	}

	registerEvents() {
		this.dialogClose.addEventListener( 'click', this.hideWrapper.bind( this ) );
	}

	registerKeyboardShortcut() {
		Mousetrap.bind( 'shift shift', () => {
			this.showWrapper();
			this.focusInput();
		} );

		Mousetrap.bindGlobal( 'esc', this.hideWrapper.bind( this ) );
	}

	showWrapper() {
		this.wrapper.style.display = 'block';
		document.addEventListener( 'click', this.handleOutsideClick.bind( this ) );
	}

	hideWrapper() {
		this.wrapper.style.display = 'none';
		document.removeEventListener( 'click', this.handleOutsideClick.bind( this ) );
	}

	focusInput() {
		this.searchInput.focus();
	}

	clearInput() {
		this.searchInput.value = '';
	}

	handleOutsideClick( event ) {
		if (
			this.dialog.contains( event.target ) ||
			'none' == this.wrapper.style.display
		) {
			return;
		}

		this.hideWrapper();
	}
}

new CommandPalette();
