/**
 * Nanato Page Ordering JavaScript
 *
 * Handles drag and drop page ordering functionality.
 *
 * @package Nanato_Addons
 * @since   1.0.6
 */

( function ( $ ) {
	'use strict';

	var sortablePostTable = $( '.wp-list-table tbody' );

	/**
	 * Callback function for handling AJAX response
	 *
	 * @param {string} response The AJAX response
	 */
	function updatePageOrderingCallback( response ) {
		if ( response === 'children' ) {
			window.location.reload();
			return;
		}

		var changes = $.parseJSON( response );
		var newPos = changes.new_pos;

		// Update positions in the inline data
		for ( var key in newPos ) {
			if ( key === 'next' ) {
				continue;
			}

			var inlineKey = document.getElementById( 'inline_' + key );
			if ( inlineKey !== null && newPos.hasOwnProperty( key ) ) {
				var domMenuOrder = inlineKey.querySelector( '.menu_order' );

				if ( typeof newPos[ key ].menu_order !== 'undefined' ) {
					if ( domMenuOrder !== null ) {
						domMenuOrder.textContent = newPos[ key ].menu_order;
					}

					var domPostParent =
						inlineKey.querySelector( '.post_parent' );
					if ( domPostParent !== null ) {
						domPostParent.textContent = newPos[ key ].post_parent;
					}

					// Update hierarchical display for pages
					var postTitle = null;
					var domPostTitle = inlineKey.querySelector( '.post_title' );
					if ( domPostTitle !== null ) {
						postTitle = domPostTitle.innerHTML;
					}

					var dashes = 0;
					while ( dashes < newPos[ key ].depth ) {
						postTitle = '&mdash; ' + postTitle;
						dashes++;
					}

					var domRowTitle =
						inlineKey.parentNode.querySelector( '.row-title' );
					if ( domRowTitle !== null && postTitle !== null ) {
						domRowTitle.innerHTML = decodeEntities( postTitle );
					}
				} else if ( domMenuOrder !== null ) {
					domMenuOrder.textContent = newPos[ key ];
				}
			}
		}

		// Continue with next batch if needed
		if ( changes.next ) {
			$.post(
				nanato_page_ordering_data.ajaxurl,
				{
					action: 'nanato_page_ordering',
					id: changes.next.id,
					previd: changes.next.previd,
					nextid: changes.next.nextid,
					start: changes.next.start,
					_wpnonce: nanato_page_ordering_data._wpnonce,
					excluded: JSON.stringify( changes.next.excluded ),
				},
				updatePageOrderingCallback
			);
		} else {
			// Remove loading indicators
			$( '.nanato-updating-row' )
				.removeClass( 'nanato-updating-row' )
				.find( '.check-column' )
				.removeClass( 'spinner is-active' );
			sortablePostTable
				.removeClass( 'nanato-updating' )
				.sortable( 'enable' );
		}
	}

	/**
	 * Decode HTML entities
	 *
	 * @param {string} html The HTML string to decode
	 * @return {string} Decoded string
	 */
	function decodeEntities( html ) {
		var textarea = document.createElement( 'textarea' );
		textarea.innerHTML = html;
		return textarea.value;
	}

	// Initialize sortable functionality
	sortablePostTable.sortable( {
		items: '> tr',
		cursor: 'move',
		axis: 'y',
		containment: 'table.widefat',
		cancel: 'input, textarea, button, select, option, .inline-edit-row',
		distance: 2,
		opacity: 0.8,
		tolerance: 'pointer',

		create: function () {
			// Handle ESC key to cancel sorting
			$( document ).keydown( function ( e ) {
				var key = e.key || e.keyCode;
				if ( key === 'Escape' || key === 'Esc' || key === 27 ) {
					sortablePostTable.sortable(
						'option',
						'preventUpdate',
						true
					);
					sortablePostTable.sortable( 'cancel' );
				}
			} );
		},

		start: function ( e, ui ) {
			// Close any open inline edit rows
			if ( typeof inlineEditPost !== 'undefined' ) {
				inlineEditPost.revert();
			}
			ui.placeholder.height( ui.item.height() );
			ui.placeholder.empty();
		},

		helper: function ( e, ui ) {
			// Fix widths for dragged item
			var children = ui.children();
			for ( var i = 0; i < children.length; i++ ) {
				var selector = $( children[ i ] );
				selector.width( selector.width() );
			}
			return ui;
		},

		stop: function ( e, ui ) {
			if ( sortablePostTable.sortable( 'option', 'preventUpdate' ) ) {
				sortablePostTable.sortable( 'option', 'preventUpdate', false );
			}
			// Remove fixed widths
			ui.item.children().css( 'width', '' );
		},

		update: function ( e, ui ) {
			if ( sortablePostTable.sortable( 'option', 'preventUpdate' ) ) {
				sortablePostTable.sortable( 'option', 'preventUpdate', false );
				return;
			}

			sortablePostTable
				.sortable( 'disable' )
				.addClass( 'nanato-updating' );
			ui.item.addClass( 'nanato-updating-row' );
			ui.item.find( '.check-column' ).addClass( 'spinner is-active' );

			var postid = ui.item[ 0 ].id.substr( 5 ); // Remove 'post-' prefix

			var prevpostid = false;
			var prevpost = ui.item.prev();
			if ( prevpost.length > 0 ) {
				prevpostid = prevpost.attr( 'id' ).substr( 5 );
			}

			var nextpostid = false;
			var nextpost = ui.item.next();
			if ( nextpost.length > 0 ) {
				nextpostid = nextpost.attr( 'id' ).substr( 5 );
			}

			// Send AJAX request
			$.post(
				nanato_page_ordering_data.ajaxurl,
				{
					action: 'nanato_page_ordering',
					id: postid,
					previd: prevpostid,
					nextid: nextpostid,
					_wpnonce: nanato_page_ordering_data._wpnonce,
				},
				updatePageOrderingCallback
			);

			// Fix row colors
			var tableRows = document.querySelectorAll( 'tr.iedit' );
			var tableRowCount = tableRows.length;
			while ( tableRowCount-- ) {
				if ( tableRowCount % 2 === 0 ) {
					$( tableRows[ tableRowCount ] ).addClass( 'alternate' );
				} else {
					$( tableRows[ tableRowCount ] ).removeClass( 'alternate' );
				}
			}
		},
	} );

	// Handle reset ordering button
	$( document ).ready( function () {
		$( '#nanato-page-ordering-reset' ).on( 'click', function ( e ) {
			e.preventDefault();
			var postType = $( this ).data( 'posttype' );
			if ( confirm( nanato_page_ordering_data.reset_confirm_msg ) ) {
				$.post(
					nanato_page_ordering_data.ajaxurl,
					{
						action: 'nanato_reset_page_ordering',
						post_type: postType,
						_wpnonce: nanato_page_ordering_data._wpnonce,
					},
					function () {
						window.location.reload();
					}
				);
			}
		} );
	} );
} )( jQuery );
