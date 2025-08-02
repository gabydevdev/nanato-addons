<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://nanatomedia.com
 * @since      1.0.0
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/admin/partials
 */

// Get the noindex options
$noindex_options = get_option( 'nanato_addons_noindex_options', array() );
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Nanato Addons Settings', 'nanato-addons' ); ?></h1>
	
	<h2 class="nav-tab-wrapper">
		<a href="#noindex-settings" class="nav-tab nav-tab-active"><?php esc_html_e( 'Noindex Settings', 'nanato-addons' ); ?></a>
	</h2>
	
	<div id="noindex-settings" class="tab-content">
		<h3><?php esc_html_e( 'Archive Pages Noindex Settings', 'nanato-addons' ); ?></h3>
		<p><?php esc_html_e( 'Configure which archive pages should have a noindex meta tag to prevent them from being indexed by search engines.', 'nanato-addons' ); ?></p>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'nanato_addons_noindex' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Apply noindex to:', 'nanato-addons' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="nanato_addons_noindex_options[category]" value="1" <?php checked( ! empty( $noindex_options['category'] ) ); ?>>
								<?php esc_html_e( 'Category Archives', 'nanato-addons' ); ?>
							</label><br>
							<label>
								<input type="checkbox" name="nanato_addons_noindex_options[tag]" value="1" <?php checked( ! empty( $noindex_options['tag'] ) ); ?>>
								<?php esc_html_e( 'Tag Archives', 'nanato-addons' ); ?>
							</label><br>
							<label>
								<input type="checkbox" name="nanato_addons_noindex_options[author]" value="1" <?php checked( ! empty( $noindex_options['author'] ) ); ?>>
								<?php esc_html_e( 'Author Archives', 'nanato-addons' ); ?>
							</label><br>
							<label>
								<input type="checkbox" name="nanato_addons_noindex_options[date]" value="1" <?php checked( ! empty( $noindex_options['date'] ) ); ?>>
								<?php esc_html_e( 'Date Archives', 'nanato-addons' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Pagination Settings:', 'nanato-addons' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="nanato_addons_noindex_options[paginated_only]" value="1" <?php checked( ! empty( $noindex_options['paginated_only'] ) ); ?>>
								<?php esc_html_e( 'Only noindex paginated pages (page 2 and beyond)', 'nanato-addons' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'When enabled, only paginated archive pages will be noindexed, leaving the first page indexable.', 'nanato-addons' ); ?></p>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
</div>

<style>
.nav-tab-wrapper {
	margin-bottom: 20px;
}
.tab-content {
	display: block;
}
.form-table th {
	vertical-align: top;
	padding-top: 10px;
}
.form-table fieldset label {
	margin-bottom: 5px;
	display: block;
}
</style>
