/**
 * Included when headline fields are rendered for editing by publishers.
 */

( function ( $ ) {
	function initialize_field( $field ) {
		// Add any headline-specific JavaScript functionality here
		// For now, this is a placeholder for future enhancements

		// Example: Auto-generate ID from title
		$field.find( 'input[name$="[title]"]' ).on( 'input', function () {
			var title = $( this ).val();
			var id = title
				.toLowerCase()
				.replace( /[^a-z0-9\s-]/g, '' ) // Remove special characters
				.replace( /\s+/g, '-' ) // Replace spaces with hyphens
				.replace( /-+/g, '-' ) // Replace multiple hyphens with single
				.replace( /^-|-$/g, '' ); // Remove leading/trailing hyphens

			var idField = $field.find( 'input[name$="[html_id]"]' );
			if ( idField.val() === '' ) {
				idField.val( id );
			}
		} );
	}

	if ( typeof acf.add_action !== 'undefined' ) {
		/**
		 * Run initialize_field when existing fields of this type load,
		 * or when new fields are appended via repeaters or similar.
		 */
		acf.add_action( 'ready_field/type=headline', initialize_field );
		acf.add_action( 'append_field/type=headline', initialize_field );
	}
} )( jQuery );
