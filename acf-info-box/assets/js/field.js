/**
 * Included when info-box fields are rendered for editing by publishers.
 */

( function ( $ ) {
	function initialize_field( $field ) {
		// Handle icon selection (SVG and image files)
		$field.find( '.select-icon' ).on( 'click', function ( e ) {
			e.preventDefault();

			var button = $( this );
			var field = button.closest( '.acf-info-box-subfield' );

			var iconUploader = wp.media( {
				title: 'Select Icon',
				button: {
					text: 'Use This Icon',
				},
				multiple: false,
				library: {
					type: [ 'image' ],
				},
			} );

			iconUploader.on( 'select', function () {
				var attachment = iconUploader
					.state()
					.get( 'selection' )
					.first()
					.toJSON();
				field.find( 'input[type="hidden"]' ).val( attachment.id );

				// Use full size for SVG, thumbnail for other images
				var imageUrl =
					attachment.subtype === 'svg+xml'
						? attachment.url
						: attachment.sizes.thumbnail.url;
				field
					.find( '.icon-preview' )
					.html( '<img src="' + imageUrl + '" />' );
				field.find( '.remove-icon' ).show();
			} );

			iconUploader.open();
		} );

		// Handle icon removal
		$field.find( '.remove-icon' ).on( 'click', function ( e ) {
			e.preventDefault();
			var field = $( this ).closest( '.acf-button-subfield' );
			field.find( 'input[type="hidden"]' ).val( '' );
			field.find( '.icon-preview' ).html( '' );
			$( this ).hide();
		} );
	}

	if ( typeof acf.add_action !== 'undefined' ) {
		/**
		 * Run initialize_field when existing fields of this type load,
		 * or when new fields are appended via repeaters or similar.
		 */
		acf.add_action( 'ready_field/type=info-box', initialize_field );
		acf.add_action( 'append_field/type=info-box', initialize_field );
	}
} )( jQuery );
