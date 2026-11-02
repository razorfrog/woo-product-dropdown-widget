# Product Dropdown Widget for WooCommerce

This plugin creates a widget to display and link to WooCommerce products by category in a dropdown menu. Visitors to your site will have one-click access to products in your sidebar (or other widgetized areas) without valuable real estate being used up by a regular products list.

- **WordPress.org:** https://wordpress.org/plugins/woo-product-dropdown-widget/
- **Plugin page:** https://razorfrog.com/plugin-release-woocommerce-dropdown-widget/
- **Current release:** 1.1.4 · requires WordPress 5.2 and PHP 7.2 · tested up to WordPress 6.9
- **License:** GPLv2 or later

`readme.txt` is the WordPress.org plugin page (description, FAQ, changelog). This file is only for working on the code.

## Releasing

Releases ship to WordPress.org SVN by hand; nothing here deploys automatically.

1. Bump the version in the plugin header and in `readme.txt` (`Stable tag`), and add a changelog entry.
2. Push to `main` and wait for the **Check** workflow to pass.
3. Copy the plugin into SVN `trunk/`, leaving out everything in `.distignore`, then copy `trunk/` to `tags/<version>/` and commit.
4. Tag the release here: `git tag <version> && git push --tags`.

Up to the `razorfrog/woo-product-dropdown-widget` import on 2026-11-02, this history has one commit per WordPress.org release (from the release zips), not every SVN commit.

## Checks

Every push runs `.github/workflows/check.yml`:

- `php -l` on every PHP file under PHP 7.2 and 8.4
- PHP_CodeSniffer with `phpcs.xml.dist`: PHP 7.2+ compatibility (PHPCompatibilityWP) plus WordPress security sniffs (output escaping, nonces, input sanitizing, prepared SQL, deprecated functions)

Run the same locally with `phpcs` from the repo root once WPCS and PHPCompatibilityWP are installed.
