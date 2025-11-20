# ACF Tooltips by SimpliWeb

A WordPress plugin that adds customizable tooltip functionality to Advanced Custom Fields (ACF). Display helpful information next to field labels with beautiful hover tooltips featuring custom icons, colors, and positioning.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-green.svg)
![ACF](https://img.shields.io/badge/ACF-6.0+-orange.svg)
![License](https://img.shields.io/badge/license-GPL%20v2+-red.svg)

## Features

- 🎨 **Rich Content Editor** - Use the WYSIWYG editor to create formatted tooltip content
- 🎯 **4 Positioning Options** - Display tooltips above, below, left, or right of the icon
- 🎭 **80+ Dashicons** - Choose from WordPress's built-in icon library
- 🌈 **Custom Colors** - Set custom background colors for each tooltip
- 📏 **Adjustable Width** - Control the pixel width of each tooltip
- 🔧 **Works with All ACF Fields** - Automatically adds tooltip settings to every ACF field type
- 💼 **Easy to Use** - Simple interface integrated directly into ACF's field settings

## Screenshots

### Field Settings Panel
![Field Settings](images/acf-tooltip-settings.png)
![Field View](images/acf-tooltip-field.png)

## Requirements

- WordPress 5.0 or higher
- Advanced Custom Fields (ACF) 6.0 or higher (Free or Pro)
- PHP 7.0 or higher

## Installation

### Manual Installation

1. Download the latest release from this repository
2. Upload the `acf-tooltips` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to any ACF Field Group and edit a field to see the new Tooltip Settings

### Git Clone
```bash
cd wp-content/plugins
git clone https://github.com/yourusername/acf-tooltips.git
```

Then activate the plugin in WordPress.

## Usage

### Adding a Tooltip to an ACF Field

1. Edit any ACF Field Group
2. Click on a field to open its settings
3. Scroll to the **Presentation** tab
4. Find the **Tooltip Settings** section
5. Configure your tooltip:
   - **Tooltip Content** - Enter your help text (supports HTML formatting)
   - **Tooltip Position** - Choose where the tooltip appears (top, right, bottom, left)
   - **Tooltip Icon** - Select a Dashicon to display next to the label
   - **Tooltip Background** - Pick a custom background color
   - **Tooltip Width** - Set the pixel width of the tooltip popup

### Example Use Cases

- **Help Documentation** - Provide detailed instructions for complex fields
- **Field Clarification** - Explain what data should be entered
- **Technical Notes** - Add developer notes or field relationships
- **User Guidance** - Help content editors understand field purposes
- **Validation Rules** - Explain format requirements or restrictions

## Configuration

All tooltip settings are configured per-field through ACF's field settings interface. No global configuration is required.

### Available Settings

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| Tooltip Content | WYSIWYG | Empty | The HTML content displayed in the tooltip |
| Tooltip Position | Select | Top | Where the tooltip appears relative to the icon |
| Tooltip Icon | Radio (Icons) | Info | The Dashicon displayed next to the label |
| Tooltip Background | Color Picker | #111111 | Background color of the tooltip popup |
| Tooltip Width | Number | 60px | Pixel width of the tooltip popup |

## File Structure
```
acf-tooltips/
├── css/
│   └── admin.css          # Tooltip and icon selector styling
├── js/
│   └── admin.js           # Tooltip JavaScript functionality
├── acf-tooltips.php       # Main plugin file
├── README.md
└── LICENSE
```

## Customization

### Styling Tooltips

You can override the default tooltip styles by adding CSS to your theme:
```css
/* Change tooltip font size */
.sb-acf-tooltip-inner {
    font-size: 14px;
}

/* Adjust tooltip shadow */
.sb-acf-tooltip-inner {
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* Modify icon size */
.sb-acf-tooltip {
    font-size: 18px;
}
```

### Adding Custom Icons

To add custom Dashicons, modify the `get_dashicons()` method in `acf-tooltips.php`:
```php
private function get_dashicons()
{
    return array(
        'dashicons-custom' => '<span class="dashicons dashicons-custom"></span>',
        // ... existing icons
    );
}
```

## Troubleshooting

### Tooltips Not Appearing

1. Make sure you've entered content in the **Tooltip Content** field
2. Verify ACF is installed and activated
3. Clear your browser cache
4. Check browser console for JavaScript errors

### WYSIWYG Editor Not Loading

This plugin includes fixes for ACF's WYSIWYG editor initialization issues. If you still experience problems:

1. Try disabling other plugins that modify ACF
2. Update to the latest version of ACF
3. Check for JavaScript conflicts in the browser console

### Icon Not Displaying

1. Ensure Dashicons are loaded (they should be by default in WordPress admin)
2. Check that you've selected an icon in the field settings
3. Verify no CSS is hiding the icon

## Technical Notes

### WYSIWYG Editor Configuration

The plugin uses specific settings to prevent editor initialization conflicts:
- `'delay' => 1` - Delays editor initialization
- `'quicktags' => false` - Disables quicktags to prevent JavaScript errors

These settings resolve the common ACF error: `Cannot read properties of undefined (reading 'buttons')`

### Hooks & Filters

The plugin hooks into ACF at these points:
- `acf/init` - Registers tooltip settings for all field types
- `acf/render_field_presentation_settings/type={$type}` - Adds settings to each field type
- `acf/input/admin_enqueue_scripts` - Loads admin assets
- `acf/render_field` - Renders tooltips on field output

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- IE11+ (basic functionality)

## Changelog

### 1.0.0 (2024-11-20)
- Initial release
- WYSIWYG editor for tooltip content
- 4 positioning options (top, right, bottom, left)
- 80+ Dashicon selection
- Custom background color picker
- Adjustable tooltip width
- Support for all ACF field types

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Development

### Local Development Setup

1. Clone the repository into your WordPress plugins directory
2. Ensure ACF is installed
3. Activate both plugins
4. Create a test field group to work with

### Code Standards

- Follow WordPress Coding Standards
- Use proper PHPDoc blocks
- Comment complex logic
- Maintain singleton pattern

## License

This plugin is licensed under the GPL v2 or later.
```
Copyright (C) 2025 SimpliWeb

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## Credits

**Developed by:** [SimpliWeb](https://simpliweb.com.au)  
**Author:** Jon  
**Icons:** WordPress Dashicons

## Support

For bugs, feature requests, or support:
- Open an issue on [GitHub](https://github.com/westcoastdigital/Simpli-ACF-Tooltips/issues)
- Visit [SimpliWeb](https://simpiweb.com.au)

## Roadmap

Potential future features:
- [ ] Custom icon upload support
- [ ] Tooltip animations
- [ ] Mobile-specific tooltip behavior
- [ ] Tooltip templates/presets
- [ ] Multi-language support
- [ ] Frontend tooltip display option
- [ ] Tooltip click-to-open option
- [ ] Accessibility improvements (ARIA labels)

---

**Made with ❤️ by SimpliWeb**