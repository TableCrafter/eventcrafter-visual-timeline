=== EventCrafter – Responsive Timelines, Roadmaps & Events Builder ===
Contributors: fahdi
Tags: timeline, json, roadmap, history, events
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.1.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The first API-native visual timeline builder for WordPress. Transform JSON into beautiful vertical timelines.

== Description ==

**EventCrafter** is a developer-first tool that transforms JSON data into professional timeline visualizations. Unlike other timeline plugins that force you to manually enter data into a slow UI, EventCrafter renders purely from your data source.

Perfect for:
*   Project Roadmaps
*   Company History / Milestones
*   Event Schedules
*   Changelogs

**Why EventCrafter?**
*   **🚀 Visual Builder:** Manage your events with a drag-and-drop interface (New in 1.1).
*   **⚡ Zero Manual Entry:** Support for loading JSON from URL or using the builder.
*   **📱 Fully Responsive:** Beautiful vertical layout that adapts to mobile.
*   **🎨 Developer Friendly:** Customize via CSS variables or JSON settings.

== Installation ==

1. Upload the `eventcrafter-visual-timeline` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the shortcode `[eventcrafter id="123"]` (or `source="URL"`) in any post or page.

== Frequently Asked Questions ==

= How do I structure my JSON? =
The plugin includes an `event-schema.json` file in the plugin directory that defines the structure. A basic example:
```json
{
  "events": [
    {
      "date": "2025-01-01",
      "title": "My Event",
      "description": "Description here",
      "color": "#3b82f6"
    }
  ]
}
```

= Can I load JSON from a remote URL? =
Yes! Just use `[eventcrafter source="https://api.example.com/events.json"]`.

== Screenshots ==

1. **Visual Builder** - Easily manage events in the WordPress Admin.
2. **Vertical Timeline** - A clean, modern vertical representation of your events.

== Changelog ==

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
