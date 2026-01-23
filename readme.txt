=== EventCrafter – Responsive Timelines, Roadmaps & Events Builder ===
Contributors: fahdi
Tags: timeline, json, roadmap, history, events
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The first API-native timeline builder for WordPress. Transform any JSON data into beautiful, responsive vertical timelines.

== Description ==

**EventCrafter** is a developer-first tool that transforms JSON data into professional timeline visualizations. Unlike other timeline plugins that force you to manually enter data into a slow UI, EventCrafter renders purely from your data source.

Perfect for:
*   Project Roadmaps
*   Company History / Milestones
*   Event Schedules
*   Changelogs

**Why EventCrafter?**
*   **🚀 Zero Manual Entry:** Just provide a URL to a JSON file.
*   **⚡ Performance First:** No database bloat. No complex queries.
*   **📱 Fully Responsive:** Beautiful vertical layout that adapts to mobile.
*   **🎨 Developer Friendly:** Customize via CSS variables or JSON settings.

== Installation ==

1. Upload the `eventcrafter-timeline` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the shortcode `[eventcrafter source="URL_TO_JSON"]` in any post or page.

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

1. **Vertical Timeline** - A clean, modern vertical representation of your events.

== Changelog ==

= 1.0.0 =
* Initial release.
