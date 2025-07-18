# ACF Headline Field

A custom Advanced Custom Fields (ACF) field type for creating structured headlines with title, subtitle, heading tag selection, and HTML attributes.

## Features

- **Title Input**: Main headline text
- **Title Tag Selection**: Choose from H1-H6, P, or DIV tags
- **Subtitle Support**: Optional subtitle field (can be disabled)
- **HTML Attributes**: Custom ID and CSS classes
- **Clean Interface**: Organized sections with responsive grid layout
- **Auto-ID Generation**: Automatically generates ID from title (if empty)

## Field Settings

### Show Subtitle
- **Type**: True/False
- **Default**: True
- **Description**: Allow users to add a subtitle below the main headline

### Show HTML Attributes
- **Type**: True/False
- **Default**: True
- **Description**: Allow users to set custom ID and CSS classes for the headline

## Usage

### In PHP Templates

```php
$headline = get_field('my_headline_field');

if ($headline) {
    $title = $headline['title'];
    $title_tag = $headline['title_tag'] ?: 'h2';
    $subtitle = $headline['subtitle'];
    $html_id = $headline['html_id'];
    $css_classes = $headline['css_classes'];
    
    $attributes = '';
    if (!empty($html_id)) {
        $attributes .= ' id="' . esc_attr($html_id) . '"';
    }
    if (!empty($css_classes)) {
        $attributes .= ' class="' . esc_attr($css_classes) . '"';
    }
    
    echo '<' . $title_tag . $attributes . '>' . esc_html($title) . '</' . $title_tag . '>';
    
    if (!empty($subtitle)) {
        echo '<p class="subtitle">' . esc_html($subtitle) . '</p>';
    }
}
```

### Using Helper Method

```php
$headline_data = nanato_addons_acf_field_headline::get_headline_data(get_field('my_headline_field'));

if ($headline_data) {
    $title = $headline_data['title'];
    $title_tag = $headline_data['title_tag'];
    $subtitle = $headline_data['attributes'];
    $css_classes = implode(' ', $headline_data['css_classes']);
    
    // Render your headline
}
```

## Default Values

- `title`: Empty string
- `title_tag`: 'h2'
- `subtitle`: Empty string
- `html_id`: Empty string
- `css_classes`: Empty string

## Requirements

- Advanced Custom Fields Pro or Free
- WordPress 5.0+
- PHP 7.4+

## Field Settings

When adding this field to a field group, you can configure which sections appear to content editors:

- **Show Headline Field**: Allow users to add a headline
- **Require Headline**: Make the headline required when content is provided
- **Show Icon Field**: Allow icon uploads via Media Library
- **Show Layout & Style Options**: Enable type, style, and color customization
- **Show HTML Attributes**: Allow custom ID and CSS classes

## Usage in Templates

### Basic Usage
```php
$info_box = get_field('my_info_box');
$data = Nanato_Addons_ACF_Field_Info_Box::get_info_box_data($info_box);

if ($data) {
    echo '<div class="' . implode(' ', $data['css_classes']) . '">';
    if (!empty($data['headline'])) {
        echo '<h3>' . esc_html($data['headline']) . '</h3>';
    }
    if (!empty($data['icon']['url'])) {
        echo '<img src="' . esc_url($data['icon']['url']) . '" alt="' . esc_attr($data['icon']['alt']) . '">';
    }
    echo '<p>' . esc_html($data['text']) . '</p>';
    echo '</div>';
}
```

### Advanced Usage (Respecting Field Settings)
```php
$field = get_field_object('my_info_box');
$info_box = $field['value'];

if ($info_box) {
    $settings = Nanato_Addons_ACF_Field_Info_Box::get_field_settings($field);
    $data = Nanato_Addons_ACF_Field_Info_Box::get_info_box_data($info_box, $settings);
    
    // Now $data will only contain sections that are enabled in field settings
}
```

## Data Structure

The `get_info_box_data()` method returns an array with the following structure:

```php
array(
    'text' => 'Main content text',
    'headline' => 'Optional headline', // Only if show_headline is enabled
    'type' => 'info', // info|warning|error|success (if layout & style enabled)
    'style' => 'default', // default|bordered|filled (if layout & style enabled)
    'colors' => array(
        'text' => '#000000',
        'background' => '#ffffff',
        'icon' => '#333333'
    ), // Only if layout & style enabled
    'attributes' => array(
        'id' => 'custom-id',
        'classes' => 'custom-class another-class'
    ), // Only if HTML attributes enabled
    'icon' => array(
        'id' => 123,
        'url' => 'https://example.com/icon.png',
        'alt' => 'Icon description'
    ), // Only if show_icon enabled
    'css_classes' => array(
        'nanato-info-box',
        'nanato-info-box--info',
        'nanato-info-box--style-default',
        'custom-class'
    )
)
```

## Styling

The field generates CSS classes following BEM methodology:

- `.nanato-info-box` - Base class
- `.nanato-info-box--{type}` - Type modifier (info, warning, error, success)
- `.nanato-info-box--style-{style}` - Style modifier (default, bordered, filled)

Custom classes from the HTML attributes section are also included in the `css_classes` array.

## Field Output

This field returns structured data (arrays/objects) rather than HTML, giving theme developers full control over the markup and styling. This approach is more flexible and follows WordPress best practices.
