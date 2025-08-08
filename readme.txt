=== RefundGuard for WooCommerce ===
Contributors: refundguard
Tags: woocommerce, refund, risk, ai, analytics, fraud
Requires at least: 5.6
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

RefundGuard for WooCommerce is a smart refund risk scanner that helps WooCommerce store owners detect and manage potentially risky orders. With refund score badges, analytics, and optional AI-powered tools, RefundGuard gives you peace of mind before shipping anything.

== Description ==

RefundGuard for WooCommerce analyzes every order for refund/return risk using rule-based logic (free) or AI-powered scoring (pro). Get instant risk badges, analytics, alerts, and more.

= Free Features =
* Refund Risk Score (Low / Medium / High) shown on admin order list and order view
* Rule-based logic: Product Category, Order Total, Shipping Country, Previous Orders
* Admin dashboard widget: Today's high-risk count, 7-day average
* Settings page to toggle rules
* No external APIs required (free version)

= Pro Features =
* AI-powered risk scoring (OpenAI or local model)
* Auto-flag high-risk orders (On Hold/Manual Review)
* Advanced analytics dashboard (risk by product, category, country)
* Export high-risk orders to CSV
* WhatsApp/Email alerts for high-risk orders
* Auto-generate PO for low-stock flagged items
* CSV restock importer for bulk inventory updates

= Screenshots =
1. Risk badge on order list
2. Risk badge and reason on order view
3. Dashboard widget
4. Analytics dashboard (Pro)
5. Settings page

= Installation =
1. Upload `refundguard-for-woocommerce` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. (Pro) Upload the Pro files to the `pro/` folder.
4. Configure rules and settings under WooCommerce > RefundGuard.

= FAQ =

= How does the risk scoring work? =
The free version uses rule-based logic. The Pro version uses AI (OpenAI or local model) for advanced analysis.

= Does this plugin share data externally? =
No, unless you enable Pro AI scoring (uses OpenAI API).

= Can I customize the rules? =
Yes, toggle rules on/off in the settings page.

= Changelog =
= 1.0.0 =
* Initial release: Free and Pro features.