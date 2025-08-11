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
// Get the SVG options
$svg_options = get_option( 'nanato_addons_svg_options', array() );
// Get the page ordering options
$page_ordering_options = get_option( 'nanato_addons_page_ordering_options', array() );
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Nanato Addons Settings', 'nanato-addons' ); ?></h1>
	
	<h2 class="nav-tab-wrapper">
		<a href="#noindex-settings" class="nav-tab nav-tab-active" data-tab="noindex-settings"><?php esc_html_e( 'Noindex Settings', 'nanato-addons' ); ?></a>
		<a href="#svg-settings" class="nav-tab" data-tab="svg-settings"><?php esc_html_e( 'SVG Support', 'nanato-addons' ); ?></a>
		<a href="#page-ordering-settings" class="nav-tab" data-tab="page-ordering-settings"><?php esc_html_e( 'Page Ordering', 'nanato-addons' ); ?></a>
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

	<div id="svg-settings" class="tab-content" style="display: none;">
		<h3><?php esc_html_e( 'SVG Support Settings', 'nanato-addons' ); ?></h3>
		<p><?php esc_html_e( 'Configure SVG upload support and inline rendering options.', 'nanato-addons' ); ?></p>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'nanato_addons_svg' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Basic SVG Support', 'nanato-addons' ); ?></th>
					<td>
						<p><?php esc_html_e( '✅ SVG uploads are enabled by default', 'nanato-addons' ); ?></p>
						<p><?php esc_html_e( '✅ Basic security sanitization is active', 'nanato-addons' ); ?></p>
						<p><?php esc_html_e( '✅ Media library display is fixed', 'nanato-addons' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="enable_inline_svg"><?php esc_html_e( 'Enable Inline SVG', 'nanato-addons' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="enable_inline_svg" name="nanato_addons_svg_options[enable_inline_svg]" value="1" <?php checked( ! empty( $svg_options['enable_inline_svg'] ) ); ?> />
						<label for="enable_inline_svg">
							<?php esc_html_e( 'Enable inline SVG rendering for images with the target class', 'nanato-addons' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'When enabled, img tags with the target class will be replaced with inline SVG code, allowing direct CSS styling of SVG elements.', 'nanato-addons' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="svg_target_class"><?php esc_html_e( 'CSS Target Class', 'nanato-addons' ); ?></label>
					</th>
					<td>
						<?php $target_class = isset( $svg_options['svg_target_class'] ) ? $svg_options['svg_target_class'] : 'style-svg'; ?>
						<input type="text" id="svg_target_class" name="nanato_addons_svg_options[svg_target_class]" value="<?php echo esc_attr( $target_class ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'CSS class to target for inline SVG replacement. Default: style-svg', 'nanato-addons' ); ?>
							<br>
							<?php esc_html_e( 'Example usage:', 'nanato-addons' ); ?> 
							<code>&lt;img class="<?php echo esc_attr( $target_class ); ?>" src="image.svg" alt="My SVG" /&gt;</code>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="force_inline_svg"><?php esc_html_e( 'Force Inline SVG', 'nanato-addons' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="force_inline_svg" name="nanato_addons_svg_options[force_inline_svg]" value="1" <?php checked( ! empty( $svg_options['force_inline_svg'] ) ); ?> />
						<label for="force_inline_svg">
							<?php esc_html_e( 'Force all SVG images to render inline', 'nanato-addons' ); ?>
						</label>
						<p class="description">
							<strong><?php esc_html_e( 'Use with caution!', 'nanato-addons' ); ?></strong>
							<?php esc_html_e( 'This will automatically convert ALL SVG images to inline, regardless of CSS classes. Useful for page builders that don\'t allow custom CSS classes.', 'nanato-addons' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="auto_insert_class"><?php esc_html_e( 'Auto Insert Class', 'nanato-addons' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="auto_insert_class" name="nanato_addons_svg_options[auto_insert_class]" value="1" <?php checked( ! empty( $svg_options['auto_insert_class'] ) ); ?> />
						<label for="auto_insert_class">
							<?php esc_html_e( 'Automatically add target class to SVG images', 'nanato-addons' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( '(Classic Editor Only) Automatically adds the target class when inserting SVG images. Removes default WordPress classes and only affects SVG files.', 'nanato-addons' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'How it works', 'nanato-addons' ); ?></th>
					<td>
						<div class="nanato-info-box">
							<h4><?php esc_html_e( 'Inline SVG Benefits:', 'nanato-addons' ); ?></h4>
							<ul>
								<li><?php esc_html_e( '🎨 Direct CSS styling of SVG elements', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( '⚡ Better performance (no additional HTTP requests)', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( '🎯 JavaScript access to SVG DOM elements', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( '✨ CSS animations and hover effects', 'nanato-addons' ); ?></li>
							</ul>
							
							<h4><?php esc_html_e( 'Usage Examples:', 'nanato-addons' ); ?></h4>
							<p><strong><?php esc_html_e( 'In Gutenberg/Block Editor:', 'nanato-addons' ); ?></strong></p>
							<p><?php esc_html_e( '1. Add an Image block', 'nanato-addons' ); ?></p>
							<p><?php esc_html_e( '2. Upload your SVG file', 'nanato-addons' ); ?></p>
							<p><?php esc_html_e( '3. In Advanced settings, add the CSS class:', 'nanato-addons' ); ?> <code><?php echo esc_attr( $target_class ); ?></code></p>
							
							<p><strong><?php esc_html_e( 'In Classic Editor or HTML:', 'nanato-addons' ); ?></strong></p>
							<code>&lt;img class="<?php echo esc_attr( $target_class ); ?>" src="your-image.svg" alt="Description" /&gt;</code>
						</div>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>

	<div id="page-ordering-settings" class="tab-content" style="display: none;">
		<h3><?php esc_html_e( 'Page Ordering Settings', 'nanato-addons' ); ?></h3>
		<p><?php esc_html_e( 'Configure drag and drop page ordering functionality for your content.', 'nanato-addons' ); ?></p>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'nanato_addons_page_ordering' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Default Features', 'nanato-addons' ); ?></th>
					<td>
						<p><?php esc_html_e( '✅ Drag and drop ordering for Pages (hierarchical post types)', 'nanato-addons' ); ?></p>
						<p><?php esc_html_e( '✅ Drag and drop ordering for Custom Post Types with page-attributes support', 'nanato-addons' ); ?></p>
						<p><?php esc_html_e( '✅ AJAX-powered real-time updates', 'nanato-addons' ); ?></p>
						<p><?php esc_html_e( '✅ Hierarchical relationship management for pages', 'nanato-addons' ); ?></p>
						<p><?php esc_html_e( '✅ REST API endpoint for programmatic ordering', 'nanato-addons' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="ordering_batch_size"><?php esc_html_e( 'Batch Processing Size', 'nanato-addons' ); ?></label>
					</th>
					<td>
						<?php $batch_size = isset( $page_ordering_options['batch_size'] ) ? $page_ordering_options['batch_size'] : 50; ?>
						<input type="number" id="ordering_batch_size" name="nanato_addons_page_ordering_options[batch_size]" value="<?php echo esc_attr( $batch_size ); ?>" min="5" max="100" class="small-text" />
						<p class="description">
							<?php esc_html_e( 'Number of items to process in each AJAX request (default: 50). Lower values help with slower hosting environments.', 'nanato-addons' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="enable_for_posts"><?php esc_html_e( 'Enable for Posts', 'nanato-addons' ); ?></label>
					</th>
					<td>
						<input type="checkbox" id="enable_for_posts" name="nanato_addons_page_ordering_options[enable_for_posts]" value="1" <?php checked( ! empty( $page_ordering_options['enable_for_posts'] ) ); ?> />
						<label for="enable_for_posts">
							<?php esc_html_e( 'Enable page ordering for regular Posts', 'nanato-addons' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'By default, regular Posts are ordered by date. This option adds page-attributes support to enable manual ordering.', 'nanato-addons' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Custom Post Types', 'nanato-addons' ); ?></th>
					<td>
						<?php
						$post_types = get_post_types(
							array(
								'public'   => true,
								'_builtin' => false,
							),
							'objects'
						);
						if ( ! empty( $post_types ) ) :
							?>
							<fieldset>
								<?php foreach ( $post_types as $post_type ) : ?>
									<?php
									$is_enabled = isset( $page_ordering_options['post_types'][ $post_type->name ] ) ?
										$page_ordering_options['post_types'][ $post_type->name ] :
										( post_type_supports( $post_type->name, 'page-attributes' ) || is_post_type_hierarchical( $post_type->name ) );
									?>
									<label>
										<input type="checkbox" name="nanato_addons_page_ordering_options[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="1" <?php checked( $is_enabled ); ?>>
										<?php echo esc_html( $post_type->label ); ?> (<?php echo esc_html( $post_type->name ); ?>)
										<?php if ( is_post_type_hierarchical( $post_type->name ) ) : ?>
											<span class="description"> - <?php esc_html_e( 'Hierarchical', 'nanato-addons' ); ?></span>
										<?php elseif ( post_type_supports( $post_type->name, 'page-attributes' ) ) : ?>
											<span class="description"> - <?php esc_html_e( 'Has page-attributes', 'nanato-addons' ); ?></span>
										<?php endif; ?>
									</label><br>
								<?php endforeach; ?>
							</fieldset>
						<?php else : ?>
							<p><?php esc_html_e( 'No custom post types found.', 'nanato-addons' ); ?></p>
						<?php endif; ?>
						<p class="description">
							<?php esc_html_e( 'Select which custom post types should have drag and drop ordering enabled.', 'nanato-addons' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'How to Use', 'nanato-addons' ); ?></th>
					<td>
						<div class="nanato-info-box">
							<h4><?php esc_html_e( 'Using Page Ordering:', 'nanato-addons' ); ?></h4>
							<ol>
								<li><?php esc_html_e( 'Go to Pages or any enabled post type admin page', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( 'Click "Sort by Order" filter link to enable ordering mode', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( 'Drag and drop rows to reorder them', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( 'Changes are saved automatically via AJAX', 'nanato-addons' ); ?></li>
							</ol>
							
							<h4><?php esc_html_e( 'Additional Features:', 'nanato-addons' ); ?></h4>
							<ul>
								<li><?php esc_html_e( '📋 Row actions for hierarchical post types (move under parent/sibling)', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( '🔄 Reset ordering functionality in help tab', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( '🎯 User capability awareness (only editors/admins can reorder)', 'nanato-addons' ); ?></li>
								<li><?php esc_html_e( '⚡ Batch processing for large datasets', 'nanato-addons' ); ?></li>
							</ul>
							
							<h4><?php esc_html_e( 'REST API:', 'nanato-addons' ); ?></h4>
							<p><strong><?php esc_html_e( 'Endpoint:', 'nanato-addons' ); ?></strong> <code>/wp-json/nanato-addons/v1/page_ordering</code></p>
							<p><strong><?php esc_html_e( 'Method:', 'nanato-addons' ); ?></strong> POST</p>
							<p><strong><?php esc_html_e( 'Parameters:', 'nanato-addons' ); ?></strong> id, previd, nextid, start (optional), exclude (optional)</p>
						</div>
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
.nanato-info-box {
	background: #f0f8ff;
	border: 1px solid #cce7ff;
	border-radius: 4px;
	padding: 15px;
	margin-top: 10px;
}
.nanato-info-box h4 {
	margin-top: 0;
	color: #0073aa;
}
.nanato-info-box ul {
	margin-left: 20px;
}
.nanato-info-box code {
	background: #fff;
	padding: 2px 6px;
	border-radius: 3px;
	font-family: Monaco, Consolas, "Andale Mono", monospace;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Tab switching functionality
	$('.nav-tab').on('click', function(e) {
		e.preventDefault();
		
		// Remove active class from all tabs
		$('.nav-tab').removeClass('nav-tab-active');
		// Add active class to clicked tab
		$(this).addClass('nav-tab-active');
		
		// Hide all tab content
		$('.tab-content').hide();
		
		// Show selected tab content
		var tabId = $(this).data('tab') || $(this).attr('href').substring(1);
		$('#' + tabId).show();
	});
});
</script>
