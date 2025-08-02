# Nanato Addons

A collection of custom WordPress addons to extend content management and modern web development.

## Description

This plugin provides a suite of custom ACF field types and SEO optimization features to build rich, interactive websites with ease. Each field type is designed with modern web standards in mind, featuring responsive interfaces and comprehensive customization options.

## Version

**Current Version:** 1.0.5

## Features

### SEO Optimization

#### Noindex Archive Pages
- Add `noindex` meta tags to archive pages to improve SEO
- Configure which archive types to noindex (Category, Tag, Author, Date)
- Option to only noindex paginated pages (page 2 and beyond)
- Simple settings page under 'Settings' → 'Nanato Addons'
- Helps prevent duplicate content issues

### Custom ACF Field Types

The plugin includes four powerful custom field types:

#### 1. Button Field (`acf-button`)
A comprehensive button configuration field with:
- Custom button text and icon support
- Multiple layout options (Filled, Outlined)
- Color scheme variations (Primary, Secondary, Light, Dark)
- Custom color overrides for text and background
- Flexible link configuration (internal/external)
- HTML attributes and accessibility options
- Size variations and responsive settings

#### 2. Headline Field (`acf-headline`)
A structured headline field featuring:
- Title and subtitle configuration
- Flexible heading tag selection (H1-H6)
- Custom HTML attributes support
- Responsive typography options
- SEO-friendly markup generation

#### 3. Info Box Field (`acf-info-box`)
A versatile info box field with:
- Headline and content sections
- Optional icon integration
- Multiple styling variations
- Custom color schemes
- Responsive layout options
- HTML attribute customization

#### 4. Info Button Field (`acf-info-button`)
An enhanced button field providing:
- Extended button configuration options
- Advanced styling capabilities
- Comprehensive link management
- Accessibility features
- Responsive design controls

## Installation

### Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Advanced Custom Fields (ACF) Pro 5.8 or higher

### Manual Installation

1. Download the plugin files
2. Upload the `nanato-addons` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The custom field types will automatically be available in ACF

## Usage

After activation, the custom field types will be available when creating or editing ACF field groups:

1. Navigate to **Custom Fields > Field Groups** in your WordPress admin
2. Create a new field group or edit an existing one
3. Add a new field and select from the available Nanato field types:
   - Button
   - Headline
   - Info Box
   - Info Button

Each field type provides an intuitive interface with organized sections for easy configuration.

## Development

### Project Structure

```
nanato-addons/
├── acf-button/           # Button field implementation
├── acf-headline/         # Headline field implementation
├── acf-info-box/         # Info Box field implementation
├── acf-info-button/      # Info Button field implementation
├── admin/                # Admin-specific functionality
├── includes/             # Core plugin classes
├── public/               # Public-facing functionality
├── composer.json         # PHP dependencies
├── package.json          # Node.js dependencies
└── nanato-addons.php     # Main plugin file
```

### Development Setup

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node.js dependencies:
   ```bash
   npm install
   ```

### Code Standards

The project follows WordPress coding standards. Use the included tools:

```bash
# Lint PHP code
npm run php:lint
# or
composer run lint

# Format PHP code
npm run php:format
# or
composer run format
```

## Configuration

### Filters and Hooks

The plugin provides several filters for customization:

#### Button Field Customization
- `nanato_addons_button_layouts` - Modify available button layouts
- `nanato_addons_button_styles` - Customize button style options

#### Headline Field Customization
- `nanato_addons_headline_tags` - Modify available heading tags

#### Info Box Customization
- `nanato_addons_info_box_styles` - Customize info box styling options

### Constants

The plugin defines the following constants:

- `NANATO_ADDONS_VERSION` - Plugin version
- `NANATO_ADDONS_DIR` - Plugin directory path
- `NANATO_ADDONS_URL` - Plugin URL

## Compatibility

- **WordPress:** 5.0+
- **PHP:** 7.4+
- **ACF:** 5.8+
- **Browsers:** Modern browsers (IE11+)

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows the WordPress coding standards and includes appropriate tests.

## Support

For support and questions:

- **Author:** Gabriela F.
- **Website:** [https://github.com/gabydevdev/nanato-addons](https://github.com/gabydevdev/nanato-addons)
- **GitHub:** [https://github.com/gabydevdev/nanato-addons](https://github.com/gabydevdev/nanato-addons)

## Changelog

### Version 1.0.4
- Current stable release
- Includes all four custom ACF field types
- Enhanced admin interface
- Improved code standards compliance

### Version 1.0.0
- Initial release
- Basic ACF field type implementations
- Core plugin architecture

## Credits

Developed by [Gabriela F.](https://github.com/gabydevdev) for Nanato Media.

## License

This project is proprietary software developed by Nanato Media for use with Nanato Media themes.

---

*This plugin is designed specifically for Nanato Media and provides enhanced functionality for content management and website development.*
