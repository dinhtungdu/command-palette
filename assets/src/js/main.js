import fuzzy from 'fuzzy';
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
		this.itemsContainer = document.getElementById( 'command-palette-items' );
	}

	registerEvents() {
		this.dialogClose.addEventListener( 'click', this.hideWrapper.bind( this ) );
		this.searchInput.addEventListener(
			'keyup',
			this.debounce( function( event ) {
				this.filterItems( event );
			}, 100 ).bind( this )
		);
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

	filterItems( event ) {
		var options = {
			extract: function( el ) {
				return el.title;
			}
		};

		this.itemsContainer.innerHTML = '';

		fuzzy.filter( event.target.value, CPItems, options ).map( el => {
			this.itemsContainer.innerHTML += `<a href="${el.original.url}" class="item" data-category="${el.original.category}" data-type="${el.original.type}">${el.string}</a>`;
		} );
	}

	debounce( func, wait, immediate ) {
		var timeout;
		return function() {
			var context = this,
				args = arguments;
			var later = function() {
				timeout = null;
				if ( ! immediate ) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if ( callNow ) {
				func.apply( context, args );
			}
		};
	}
}

new CommandPalette();
