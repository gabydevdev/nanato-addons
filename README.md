# Nanato Addons

A collection of custom WordPress addons to extend content management and modern web development with SEO optimization, page ordering, and comprehensive SVG support.

## Description

This plugin provides addon functions like advanced page ordering with drag-and-drop, SVG support with inline rendering, and SEO optimization features.

## Version

**Current Version:** 1.0.9

## Features

### Page Ordering System 📋

Advanced drag-and-drop page ordering functionality with comprehensive management options:

#### Core Ordering Features
- **Drag & Drop Interface**: Intuitive reordering with visual feedback and loading states
- **Multiple Post Types**: Support for pages, posts, and custom post types
- **Hierarchical Management**: Maintains parent-child relationships during reordering
- **Real-time Updates**: AJAX processing without page refreshes
- **Batch Processing**: Efficient handling of multiple items with optimized database queries

#### REST API Integration
- **Programmatic Access**: `/wp-json/nanato-addons/v1/page-ordering` endpoint
- **Bulk Operations**: Update multiple post orders in a single request
- **Validation**: Comprehensive data validation and error handling
- **Authentication**: Respects WordPress user capabilities and permissions

#### Advanced Management
- **User Permissions**: Automatic capability checking for edit rights
- **Settings Integration**: Configure which post types are sortable
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **Performance Optimized**: Minimal database queries and efficient processing

#### Usage Examples
```javascript
// Programmatic ordering via REST API
fetch('/wp-json/nanato-addons/v1/page-ordering', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': wpApiSettings.nonce
    },
    body: JSON.stringify({
        post_id: 123,
        menu_order: 5,
        post_type: 'page'
    })
});
```

#### Admin Interface
- **Settings Page**: Configure sortable post types under Settings → Nanato Addons
- **Visual Feedback**: Drag placeholders and loading indicators
- **Mobile Responsive**: Touch-friendly interface for mobile devices
- **Error Handling**: Clear feedback for successful operations and errors

### SVG Support 🎨

Advanced SVG file support with security, optimization, and inline rendering capabilities:

#### Core SVG Features
- **Secure SVG Uploads**: Upload SVG files to your media library with automatic sanitization
- **Media Library Integration**: Proper SVG thumbnails and file information display
- **Security Sanitization**: Removes potentially harmful scripts and event handlers
- **MIME Type Support**: Full WordPress media library integration

#### Inline SVG Rendering
- **CSS Class Targeting**: Add `style-svg` class to convert images to inline SVG
- **Direct Element Styling**: Style SVG paths, circles, and elements with CSS
- **JavaScript Access**: Manipulate SVG elements with JavaScript
- **Animation Support**: CSS animations and hover effects on SVG elements
- **Custom Target Class**: Configure your own CSS class for targeting

#### Advanced Options
- **Force Inline SVG**: Automatically convert ALL SVG images to inline (perfect for page builders)
- **Auto Insert Class**: Automatically add target class when inserting SVGs in Classic Editor
- **Dynamic Content Support**: Handles dynamically loaded content and page builders
- **Responsive Design**: SVG images scale properly on all devices

#### Usage Examples
```html
<!-- Basic Usage: Add class to any img tag -->
<img class="style-svg" src="icon.svg" alt="My Icon" />

<!-- The above becomes inline SVG automatically -->
<svg class="replaced-svg svg-replaced-1" viewBox="0 0 24 24">
  <title>My Icon</title>
  <path d="..."/>
</svg>
```

#### CSS Styling Example
```css
/* Style SVG elements directly */
.replaced-svg path {
    fill: #ff6b6b;
    transition: fill 0.3s ease;
}

.replaced-svg:hover path {
    fill: #4ecdc4;
}
```

### SEO Optimization

#### Noindex Archive Pages
- Add `noindex` meta tags to archive pages to improve SEO
- Configure which archive types to noindex (Category, Tag, Author, Date)
- Option to only noindex paginated pages (page 2 and beyond)
- Simple settings page under 'Settings' → 'Nanato Addons'
- Helps prevent duplicate content issues

## Installation

### Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

### Manual Installation

1. Download the plugin files
2. Upload the `nanato-addons` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the features through **Settings > Nanato Addons**

## Usage

### Page Ordering

After activation, page ordering functionality becomes available:

#### Enable Page Ordering
1. Go to **Settings > Nanato Addons > Page Ordering** tab
2. Select which post types should be sortable
3. Enable sorting for posts if desired (adds page-attributes support)
4. **Save Settings**

#### Using Drag & Drop Ordering

**In Post List Pages:**
1. Navigate to any enabled post type list (Pages, Posts, etc.)
2. Drag and drop items to reorder them
3. Changes save automatically with visual feedback
4. Hierarchical relationships are maintained

**Programmatic Access:**
```javascript
// Update post order via REST API
const response = await fetch('/wp-json/nanato-addons/v1/page-ordering', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': wpApiSettings.nonce
    },
    body: JSON.stringify({
        post_id: 123,
        menu_order: 5,
        post_type: 'page'
    })
});
```

#### Page Ordering Configuration Options

**Enable for Posts**: Add page-attributes support to posts for ordering
**Sortable Post Types**: Select custom post types that should be sortable
**Automatic Detection**: Hierarchical post types are automatically sortable
**Permission Checks**: Users must have edit capabilities for the post type

### SVG Support

After activation, SVG support is automatically enabled:

#### Basic SVG Upload
1. Navigate to **Media Library** in your WordPress admin
2. Upload SVG files like any other image
3. SVG files will display properly with thumbnails

#### Enable Inline SVG Rendering
1. Go to **Settings > Nanato Addons > SVG Support** tab
2. Check **"Enable Inline SVG"**
3. Configure your target CSS class (default: `style-svg`)
4. **Save Settings**

#### Using Inline SVG

**In Gutenberg/Block Editor:**
1. Add an Image block
2. Upload your SVG file
3. In the block settings, add the CSS class: `style-svg`
4. The image will automatically convert to inline SVG on the frontend

**In Classic Editor:**
- Enable **"Auto Insert Class"** option for automatic class insertion
- Or manually add: `<img class="style-svg" src="image.svg" alt="Description" />`

**For Page Builders (Divi, Elementor, etc.):**
- Enable **"Force Inline SVG"** to automatically convert ALL SVG images
- No CSS classes needed - works automatically

#### SVG Configuration Options

**Enable Inline SVG**: Base functionality for inline rendering
**CSS Target Class**: Customize the class name (default: `style-svg`)
**Force Inline SVG**: Convert ALL SVG images automatically (use with caution)
**Auto Insert Class**: Automatically add target class in Classic Editor

## Development

### Project Structure

```
nanato-addons/
├── admin/                # Admin-specific functionality
│   ├── css/              # Admin stylesheets
│   │   └── nanato-page-ordering.css # Page ordering interface styles
│   └── js/               # Admin JavaScript files
│       └── nanato-page-ordering.js  # Page ordering drag-drop functionality
├── assets/               # CSS and JavaScript assets
│   ├── css/              # Stylesheets
│   │   └── svg-inline.css # SVG inline rendering styles
│   └── js/               # JavaScript files
│       └── svg-inline.js  # SVG inline rendering script
├── includes/             # Core plugin classes
│   ├── class-nanato-addons-page-ordering.php # Page ordering functionality
│   └── ... # Other core classes
├── public/               # Public-facing functionality
├── composer.json         # PHP dependencies
├── package.json          # Node.js dependencies
└── nanato-addons.php     # Main plugin file
```

## Configuration

### Filters and Hooks

The plugin provides several filters for customization:

#### Page Ordering Customization
- `nanato_page_ordering_is_sortable` - Control which post types are sortable
- `nanato_page_ordering_edit_rights` - Modify user edit permissions
- `nanato_page_ordering_ajax_check` - Customize AJAX request validation
- `nanato_page_ordering_new_order` - Modify order calculation logic

#### SVG Support Customization
- `nanato_addons_svg_target_class` - Modify the default target CSS class
- `nanato_addons_svg_sanitize` - Customize SVG sanitization process
- `nanato_addons_svg_force_inline` - Control force inline behavior



### JavaScript Events

The plugin dispatches custom events for integration:

#### Page Ordering Events
```javascript
// Listen for page ordering events
document.addEventListener('pageOrderingComplete', function(event) {
    const postId = event.detail.postId;
    const newOrder = event.detail.newOrder;
    console.log('Page order updated:', postId, newOrder);
});
```

#### SVG Events
The SVG inline rendering system dispatches custom events:

```javascript
// Listen for SVG replacement events
document.addEventListener('svgReplaced', function(event) {
    const svg = event.detail.svg;
    const replacements = event.detail.replacements;
    console.log('SVG replaced:', svg);
});
```

## Changelog

### Version 1.0.6
- **NEW**: Comprehensive page ordering system with drag-and-drop functionality
- **NEW**: REST API endpoint for programmatic page ordering
- **NEW**: AJAX-powered real-time reordering without page refreshes
- **NEW**: Support for hierarchical post type management
- **NEW**: User permission checking and capability validation
- **NEW**: Batch processing for efficient database operations
- **NEW**: Admin settings page for configuring sortable post types
- **NEW**: Mobile-responsive drag-and-drop interface
- **NEW**: Custom JavaScript events for developer integration
- **IMPROVED**: Enhanced admin interface with page ordering tab
- **IMPROVED**: Better performance with optimized database queries
- **SECURITY**: Comprehensive nonce verification and user capability checks

### Version 1.0.5
- **NEW**: Comprehensive SVG support with security and inline rendering
- **NEW**: SVG upload support with automatic sanitization
- **NEW**: Inline SVG rendering with CSS class targeting
- **NEW**: Force inline SVG option for page builders
- **NEW**: Auto-insert class functionality for Classic Editor
- **NEW**: Advanced SVG settings page with multiple configuration options
- **NEW**: Dynamic content support with MutationObserver
- **NEW**: Custom CSS and JavaScript assets for SVG handling
- **IMPROVED**: Enhanced admin interface with tabbed settings
- **IMPROVED**: Better media library integration for SVG files
- **SECURITY**: SVG sanitization removes scripts and event handlers

### Version 1.0.4
- Enhanced admin interface
- Improved code standards compliance

### Version 1.0.0
- Initial release
- Core plugin architecture

---

*This plugin is designed specifically for Nanato Media and provides enhanced functionality for content management and website development.*
