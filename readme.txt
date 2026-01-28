=== EventCrafter – Responsive Timelines, Roadmaps & Events Builder ===
Contributors: fahdi
Tags: timeline, json, roadmap, history, events
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.1.7
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create beautiful vertical timelines, product roadmaps, and event history. Manage your events using the intuitive Visual Builder.

== Description ==

**EventCrafter** is a powerful tool to create professional timeline visualizations. Unlike other timeline plugins that are bloated or difficult to use, EventCrafter offers a streamlined Visual Builder for effortless content management.

Perfect for:

*   Project Roadmaps
*   Company History / Milestones
*   Event Schedules
*   Changelogs

**Why EventCrafter?**

*   **👑 Visual Builder:** The easiest way to build timelines. Drag, drop, done.
*   **📱 Fully Responsive:** Adapts to any screen size automatically.
*   **🎨 Easy Customization:** Control colors directly in the editor.
*   **⚙️ Advanced JSON:** Load data from external APIs if you need to.

**🚀 Try it Live:** [Test EventCrafter in WordPress Playground](https://playground.wordpress.net/scope:ambitious-modern-city/sample-page/) - No installation needed!

== Installation ==

1. Upload the `eventcrafter-visual-timeline` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the shortcode `[eventcrafter id="123"]` (or `source="URL"`) in any post or page.

== Frequently Asked Questions ==

= Is it responsive? =

Yes! The specific vertical layout is designed to work perfectly on mobile devices, tablets, and desktops automatically.

= How do I modify colors? =

You can set specific colors for each event directly within the Visual Builder.

== Screenshots ==

1. **Visual Builder** - Easily manage events in the WordPress Admin.
2. **Vertical Timeline** - A clean, modern vertical representation of your events.

== Changelog ==

= 1.1.7 =
* **Fix**: Replaced direct database queries with WordPress functions (Plugin Check compliance)
* **Fix**: Improved performance by eliminating slow meta_key queries
* **Fix**: Enhanced data migration with proper caching and WordPress API usage

= 1.1.6 =
* **Fix**: Resolved custom post type name length issue (WordPress 20-character limit)
* **Fix**: Enhanced custom post type registration with backup registration method
* **Fix**: Improved admin menu visibility with proper activation hooks
* **Fix**: Added comprehensive data migration from old post type names

= 1.1.5 =
* **Fix**: Added proper activation hooks to ensure menu appears after plugin activation
* **Fix**: Added data migration for existing users upgrading from older versions
* **Fix**: Improved custom post type registration with explicit capabilities

= 1.1.4 =
* **Fix**: Enhanced data sanitization, validation, and escaping for improved security
* **Fix**: Updated custom post type registration to use proper "eventcrafter_timeline" prefix
* **Fix**: Improved function naming conventions with proper prefixing

= 1.1.3 =
* **Fix**: Security improvements (nonce verification, input sanitization).
* **Fix**: Removed dev files from distribution.

= 1.1.2 =
* **Feature**: Added `eventcrafter_timeline_data` and `eventcrafter_single_event_data` filters for developers.
* **Docs**: Updated documentation to prioritize Visual Builder workflow.

= 1.1.1 =
* **Enhancement**: Added 'Copy Shortcode' button to Timelines list table with visual feedback.
* **Fix**: Improved JavaScript clipboard compatibility.
* **Tests**: Added comprehensive Unit Test suite (100% coverage for core logic).

= 1.1.0 =
* **Feature**: Added Visual Builder (Admin UI) for creating timelines.
* **Feature**: Added `id` attribute to shortcode for easier embedding.
* **New**: Added `Timelines` custom post type.

= 1.0.1 =
* **Security Fix**: Improved output escaping for timeline events.
* **Compatibility**: Confirmed compatibility with WordPress 6.9.

= 1.0.0 =
* Initial release.
