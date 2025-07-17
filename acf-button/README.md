# ACF Button Field

A modern Advanced Custom Fields (ACF) custom field type that provides a comprehensive button configuration interface with responsive design and extensive customization options. Features a clean, organized admin interface with support for icons, colors, styling options, and flexible link handling.

## Features

The Button field provides 15 configuration options organized into 4 logical sections:

### Content Section
1. **Button Text** - Main button text
2. **Icon** - WordPress media library integration for SVG icons and images

### Layout & Style Section
3. **Layout** - Button appearance style (Filled, Outlined) - *Filterable*
4. **Style** - Color scheme variation (Primary, Secondary, Light, Dark) - *Filterable*
5. **Custom Text Color** - Color picker for text color override
6. **Custom Background Color** - Color picker for background color override

### Link Configuration Section
7. **Link Type** - Choose between internal WordPress content or external URL
8. **Internal Link** - Dropdown to select from WordPress pages/posts (grouped by post type)
9. **External URL** - Flexible URL input supporting http://, https://, tel:, mailto:, and anchor links
10. **Link Target** - Choose to open in same window or new window

### HTML Attributes Section
11. **Button ID** - Custom HTML ID attribute for the button
12. **CSS Classes** - Additional CSS classes for styling and JavaScript targeting

## Usage

### Getting Field Data

The field returns a comprehensive array with all configuration values:

```php
$button = get_field('your_button_field_name');

// $button will be an array like:
// [
//     'text' => 'Click here',
//     'url' => 'https://example.com',
//     'target' => '_blank',
//     'text_color' => '#ffffff',
//     'background_color' => '#007cba',
//     'icon' => '456', // WordPress attachment ID for icon image
//     'link_type' => 'external',
//     'internal_link' => '',
//     'button_id' => 'my-cta-button',
//     'css_classes' => 'btn btn-primary custom-class',
//     'layout' => 'filled',
//     'style' => 'primary'
// ]
```

### Complete Template Example

```php
$button = get_field('my_button');

if ($button && !empty($button['text'])) {
    // Determine the URL
    $url = '';
    if ($button['link_type'] === 'internal' && !empty($button['internal_link'])) {
        $url = get_permalink($button['internal_link']);
    } elseif ($button['link_type'] === 'external' && !empty($button['url'])) {
        $url = $button['url'];
    }
    
    // Skip if no URL is available
    if (empty($url)) {
        return;
    }
    
    // Build attributes
    $target = ($button['target'] === '_blank') ? ' target="_blank"' : '';
    $id_attr = !empty($button['button_id']) ? ' id="' . esc_attr($button['button_id']) . '"' : '';
    
    // Build CSS classes
    $classes = array('button');
    $classes[] = 'layout-' . esc_attr($button['layout']);
    $classes[] = 'style-' . esc_attr($button['style']);
    
    if (!empty($button['css_classes'])) {
        $custom_classes = explode(' ', $button['css_classes']);
        $classes = array_merge($classes, array_map('trim', $custom_classes));
    }
    
    // Build inline styles
    $styles = array();
    if (!empty($button['text_color'])) {
        $styles[] = 'color: ' . esc_attr($button['text_color']);
    }
    if (!empty($button['background_color'])) {
        $styles[] = 'background-color: ' . esc_attr($button['background_color']);
    }
    
    $style_attr = !empty($styles) ? ' style="' . implode('; ', $styles) . '"' : '';
    
    // Output the button
    echo '<a href="' . esc_url($url) . '"' . $target . $id_attr . ' class="' . esc_attr(implode(' ', $classes)) . '"' . $style_attr . '>';
    
    // Icon
    if (!empty($button['icon'])) {
        $icon_url = wp_get_attachment_image_url($button['icon'], 'thumbnail');
        if ($icon_url) {
            echo '<img src="' . esc_url($icon_url) . '" alt="" class="button-icon" />';
        }
    }
    
    // Text content
    echo '<span class="button-text">' . esc_html($button['text']) . '</span>';
    
    echo '</a>';
}
```

### Simplified Usage

For basic usage without custom styling:

```php
$button = get_field('my_button');

if ($button && !empty($button['text'])) {
    // Get URL
    $url = '';
    if ($button['link_type'] === 'internal' && !empty($button['internal_link'])) {
        $url = get_permalink($button['internal_link']);
    } elseif ($button['link_type'] === 'external' && !empty($button['url'])) {
        $url = $button['url'];
    }
    
    if (!empty($url)) {
        $target = ($button['target'] === '_blank') ? ' target="_blank"' : '';
        echo '<a href="' . esc_url($url) . '"' . $target . ' class="button">';
        echo esc_html($button['text']);
        echo '</a>';
    }
}
```

## Data Structure

The field stores and returns an array with 15 configuration keys:

### Content Fields
- `text` (string) - Main button text
- `icon` (string) - WordPress attachment ID for icon image

### Layout & Style Fields
- `layout` (string) - Button appearance style (default: 'filled')
- `style` (string) - Color scheme variation (default: 'primary')
- `text_color` (string) - Hex color code for custom text color
- `background_color` (string) - Hex color code for custom background color

### Link Configuration Fields
- `link_type` (string) - Either 'internal' or 'external' (default: 'internal')
- `internal_link` (string) - WordPress post/page ID (when link_type is 'internal')
- `url` (string) - External URL (when link_type is 'external') - supports http://, https://, tel:, mailto:, and anchor links
- `target` (string) - Either '' (same window) or '_blank' (new window)

### HTML Attribute Fields
- `button_id` (string) - Custom HTML ID attribute for the button
- `css_classes` (string) - Space-separated CSS classes for styling and JavaScript targeting

## CSS Classes for Styling

The template example above includes automatic CSS class generation:

- `button` - Base class for all buttons
- `layout-{layout}` - Layout-specific styling (e.g., `layout-filled`, `layout-outlined`)
- `style-{style}` - Style-specific styling (e.g., `style-primary`, `style-secondary`)
- Custom classes from the `css_classes` field are also added

### Example CSS

```css
.button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.button.layout-filled {
    background-color: var(--primary-color);
    color: white;
}

.button.layout-outlined {
    border: 2px solid var(--primary-color);
    background-color: transparent;
    color: var(--primary-color);
}

.button .button-icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
}
```

## Theme Customization

The field provides three filter hooks for theme developers to customize dropdown options:

### Layout Options Filter
```php
add_filter( 'nanato_addons_acf_field_button_layout_options', function( $options ) {
    return array(
        'filled'     => __( 'Filled', 'textdomain' ),
        'outlined'   => __( 'Outlined', 'textdomain' ),
        'ghost'      => __( 'Ghost', 'textdomain' ),
        'gradient'   => __( 'Gradient', 'textdomain' ),
    );
});
```

### Style Options Filter
```php
add_filter( 'nanato_addons_acf_field_button_style_options', function( $options ) {
    return array(
        'primary'    => __( 'Primary', 'textdomain' ),
        'secondary'  => __( 'Secondary', 'textdomain' ),
        'success'    => __( 'Success', 'textdomain' ),
        'warning'    => __( 'Warning', 'textdomain' ),
        'danger'     => __( 'Danger', 'textdomain' ),
        'light'      => __( 'Light', 'textdomain' ),
        'dark'       => __( 'Dark', 'textdomain' ),
    );
});
```
