/**
 * Included when info_button fields are rendered for editing by publishers.
 */

(function ($) {
	function initialize_field($field) {
		// Handle link type change
		$field.find('.link-type-select').on('change', function () {
			var linkType = $(this).val();
			var linkContentField = $field.find('.acf-info-button-link-content');

			linkContentField.find('.acf-info-button-internal-link').toggle(linkType === 'internal');
			linkContentField.find('.acf-info-button-external-link').toggle(linkType === 'external');
		});

		// Handle image selection for background images
		$field.find('.select-image').on('click', function (e) {
			e.preventDefault();

			var button = $(this);
			var field = button.closest('.acf-info-button-subfield');

			var customUploader = wp.media({
				title: 'Select Background Image',
				button: {
					text: 'Use This Image',
				},
				multiple: false,
			});

			customUploader.on('select', function () {
				var attachment = customUploader.state().get('selection').first().toJSON();
				field.find('input[type="hidden"]').val(attachment.id);
				field.find('.image-preview').html('<img src="' + attachment.sizes.thumbnail.url + '" />');
				field.find('.remove-image').show();
			});

			customUploader.open();
		});

		// Handle image removal for background images
		$field.find('.remove-image').on('click', function (e) {
			e.preventDefault();
			var field = $(this).closest('.acf-info-button-subfield');
			field.find('input[type="hidden"]').val('');
			field.find('.image-preview').html('');
			$(this).hide();
		});

		// Handle icon selection (SVG and image files)
		$field.find('.select-icon').on('click', function (e) {
			e.preventDefault();

			var button = $(this);
			var field = button.closest('.acf-info-button-subfield');

			var iconUploader = wp.media({
				title: 'Select Icon',
				button: {
					text: 'Use This Icon',
				},
				multiple: false,
				library: {
					type: ['image'],
				},
			});

			iconUploader.on('select', function () {
				var attachment = iconUploader.state().get('selection').first().toJSON();
				field.find('input[type="hidden"]').val(attachment.id);

				// Use full size for SVG, thumbnail for other images
				var imageUrl = attachment.subtype === 'svg+xml' ? attachment.url : attachment.sizes.thumbnail.url;
				field.find('.icon-preview').html('<img src="' + imageUrl + '" />');
				field.find('.remove-icon').show();
			});

			iconUploader.open();
		});

		// Handle icon removal
		$field.find('.remove-icon').on('click', function (e) {
			e.preventDefault();
			var field = $(this).closest('.acf-info-button-subfield');
			field.find('input[type="hidden"]').val('');
			field.find('.icon-preview').html('');
			$(this).hide();
		});
	}

	if (typeof acf.add_action !== 'undefined') {
		/**
		 * Run initialize_field when existing fields of this type load,
		 * or when new fields are appended via repeaters or similar.
		 */
		acf.add_action('ready_field/type=info_button', initialize_field);
		acf.add_action('append_field/type=info_button', initialize_field);
	}
})(jQuery);
