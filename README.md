# EventCrafter – Responsive Timelines & Roadmaps

**The first API-native visual timeline builder for WordPress. Transform JSON into beautiful vertical timelines.**

![Version](https://img.shields.io/badge/version-1.1.1-blue.svg) ![License](https://img.shields.io/badge/license-GPLv2-green.svg)

EventCrafter is a developer-first tool that transforms JSON data into professional timeline visualizations. Unlike other timeline plugins that force you to manually enter data into a slow UI, EventCrafter renders purely from your data source—whether it's a remote JSON API or a structured local configuration.

With version 1.1, we've introduced a **Visual Builder** so content editors can easily manage timelines right from the WordPress dashboard, while still maintaining the clean JSON architecture under the hood.

## 🚀 Key Features

*   **Visual Builder**: Manage your events with a drag-and-drop interface.
*   **Zero Manual Entry**: Load JSON from any remote URL or use the builder.
*   **Fully Responsive**: Beautiful vertical layout that adapts perfectly to mobile devices.
*   **Developer Friendly**: Customize appearances via CSS variables or JSON settings.
*   **Shortcode Driven**: Embed anywhere using `[eventcrafter id="123"]`.

## 📦 Installation

1.  Download the plugin zip file.
2.  Upload to your WordPress site via **Plugins > Add New > Upload Plugin**.
3.  Activate **EventCrafter Visual Timeline**.
4.  Navigate to **Timelines** in the admin menu to start building.

## 🛠 Usage

### Using the Visual Builder
1.  Go to **Timelines > Add New**.
2.  Use the **EventCrafter Visual Builder** to add events (Title, Date, Description, Color).
3.  Click the "Copy" button next to the shortcode at the top of the editor.
4.  Paste the shortcode into any page or post.

### Loading from Remote JSON
You can also load data directly from an external API:

```shortcode
[eventcrafter source="https://api.example.com/roadmap.json"]
```

### JSON Structure
If you are building your own JSON feed, follow this structure:

```json
{
  "events": [
    {
      "date": "2025 Q1",
      "title": "Product Launch",
      "description": "Initial release of the platform.",
      "color": "#3b82f6",
      "category": "Milestone"
    },
    {
      "date": "2025 Q2",
      "title": "Mobile App",
      "description": "iOS and Android versions.",
      "color": "#10b981"
    }
  ]
}
```

## 🧪 Development

EventCrafter is built with TDD principles. To run the test suite:

```bash
composer install
composer test
```
