# Product Dropdown Widget for WooCommerce

This plugin creates a widget to display and link to WooCommerce products by category in a dropdown menu. Visitors to your site will have one-click access to products in your sidebar (or other widgetized areas) without valuable real estate being used up by a regular products list.

- **WordPress.org:** https://wordpress.org/plugins/woo-product-dropdown-widget/
- **Plugin page:** https://razorfrog.com/plugin-release-woocommerce-dropdown-widget/
- **Current release:** 1.1.4 · requires WordPress 5.2 and PHP 7.2 · tested up to WordPress 6.9
- **License:** GPLv2 or later

`readme.txt` is the WordPress.org plugin page (description, FAQ, changelog). For help, use the plugin's support forum on WordPress.org.

## Checks

Every push runs `.github/workflows/check.yml`:

- `php -l` on every PHP file under PHP 7.2 and 8.4
- PHP_CodeSniffer with `phpcs.xml.dist`: PHP 7.2+ compatibility (PHPCompatibilityWP) plus WordPress security sniffs (output escaping, nonces, input sanitizing, prepared SQL, deprecated functions)

Run the same locally with `phpcs` from the repo root once WPCS and PHPCompatibilityWP are installed.
