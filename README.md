# EventCrafter – Responsive Timelines & Roadmaps

**Create beautiful vertical timelines, product roadmaps, and event history.**

![Version](https://img.shields.io/badge/version-1.1.2-blue.svg) ![License](https://img.shields.io/badge/license-GPLv2-green.svg)

EventCrafter is a powerful tool designed for content editors and site owners. It transforms your events into professional vertical timeline visualizations using a simple drag-and-drop Visual Builder. No technical knowledge required.

## 🚀 Key Features

*   **👑 Visual Builder First**: Create stunning timelines using our drag-and-drop editor. No code required.
*   **📱 Fully Responsive**: Vertical layouts that look great on mobile, tablet, and desktop.
*   **🎨 Customization**: Control colors and styles visually.
*   **👨‍💻 Developer Friendly**: Extensive hooks, filters, and optional JSON support for deep customization.

## 📦 Installation

1.  Download the plugin zip file.
2.  Upload to your WordPress site via **Plugins > Add New > Upload Plugin**.
3.  Activate **EventCrafter Visual Timeline**.
4.  Navigate to **Timelines** in the admin menu.

## 🛠 Usage

### 1. Visual Builder (Recommended)
The easiest way to build a roadmap or history timeline.
1.  Go to **Timelines > Add New**.
2.  Use the **Visual Builder** to add events.
3.  Drag and drop to reorder.
4.  Copy the shortcode from the top bar: `[eventcrafter id="123"]`.

### 2. Advanced: Remote JSON
For developers or dynamic data needs, you can power a timeline via JSON URL:
```shortcode
[eventcrafter source="https://api.example.com/roadmap.json"]
```

## 👨‍💻 Developer Hooks

EventCrafter is built to be extended.

### Filters
*   `eventcrafter_timeline_data` `(array $data, string $source)`: Modify the entire timeline data array before rendering.
*   `eventcrafter_single_event_data` `(array $event, int $index)`: Modify individual event data just before rendering.
*   `eventcrafter_wrapper_classes` `(array $classes)`: Add or remove CSS classes from the timeline wrapper.

### JSON Data Schema
If you are loading data from an external API, your JSON should follow this structure:

```json
{
  "events": [
    {
      "date": "2025 Q1",
      "title": "Product Launch",
      "description": "HTML supported description of the event.",
      "color": "#3b82f6",
      "category": "Milestone",
      "link": {
        "url": "https://example.com",
        "text": "Read Case Study",
        "target": "_blank"
      }
    }
  ]
}
```

### Development
EventCrafter is built with TDD principles.
```bash
composer install
composer test
```
