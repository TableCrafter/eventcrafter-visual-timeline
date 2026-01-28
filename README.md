# 🚀 EventCrafter – Professional WordPress Timeline Plugin

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/eventcrafter-visual-timeline.svg)](https://wordpress.org/plugins/eventcrafter-visual-timeline/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/eventcrafter-visual-timeline.svg)](https://wordpress.org/plugins/eventcrafter-visual-timeline/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/r/eventcrafter-visual-timeline.svg)](https://wordpress.org/plugins/eventcrafter-visual-timeline/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Create stunning **timelines**, **roadmaps**, and **event histories** for WordPress with our intuitive drag-drop Visual Builder. Perfect for project management, company milestones, and product launches.

## ⭐ Why Choose EventCrafter?

- **🎯 Zero Learning Curve** - Build professional timelines in under 5 minutes
- **📱 Mobile-First Design** - Stunning display on all devices and screen sizes  
- **🎨 Unlimited Customization** - Colors, spacing, fonts, and layout control
- **⚡ Performance Optimized** - Lightning-fast loading and SEO-friendly
- **♿ Accessibility Ready** - WCAG 2.1 compliant for inclusive design
- **🔗 API Integration** - Connect external data sources and JSON feeds

## 🚀 [Try Live Demo](https://playground.wordpress.net/scope:ambitious-modern-city/sample-page/)

Test EventCrafter instantly in WordPress Playground - no installation required!

## 🎯 Perfect Use Cases

| Use Case | Description | Ideal For |
|----------|-------------|-----------|
| **Project Roadmaps** | Visualize development milestones and feature releases | SaaS companies, agencies, startups |
| **Company History** | Showcase your journey and key achievements | Corporate websites, about pages |
| **Product Launches** | Create anticipation with launch timelines | E-commerce, product marketing |
| **Event Schedules** | Display conferences, webinars, workshops | Event organizers, conferences |
| **Process Documentation** | Step-by-step workflow visualization | Documentation, tutorials |
| **Portfolio Showcases** | Career progression and project timelines | Personal branding, freelancers |

## 🛠 Installation & Setup

### WordPress Repository (Recommended)
```bash
# Install from WordPress admin
1. Go to Plugins → Add New
2. Search "EventCrafter"
3. Install & Activate
```

### Manual Installation
```bash
1. Download from https://wordpress.org/plugins/eventcrafter-visual-timeline/
2. Upload to /wp-content/plugins/
3. Activate in WordPress admin
```

### Quick Start
```php
// Basic usage
[eventcrafter id="123"]

// With custom styling
[eventcrafter id="123" layout="vertical"]

// From JSON source
[eventcrafter source="https://api.example.com/timeline.json"]
```

## 📸 Screenshots

<details>
<summary>View Screenshots</summary>

| Feature | Preview |
|---------|---------|
| **Visual Builder** | ![Admin Interface](screenshot-1.png) |
| **Timeline Display** | ![Frontend Timeline](screenshot-2.png) |
| **Mobile Responsive** | ![Mobile View](screenshot-3.png) |
| **Customization** | ![Color Options](screenshot-4.png) |

</details>

## 🔧 Advanced Features

### JSON API Integration
```javascript
// Load timeline data from any API
fetch('https://your-api.com/timeline')
  .then(response => response.json())
  .then(data => {
    // EventCrafter automatically formats your data
  });
```

### Custom Styling
```css
/* Customize timeline appearance */
.eventcrafter-timeline {
  /* Your custom styles */
}
```

### WordPress Hooks
```php
// Modify timeline data before display
add_filter('eventcrafter_timeline_data', function($data) {
    // Your customizations
    return $data;
});

// Modify individual events
add_filter('eventcrafter_single_event_data', function($event, $index) {
    // Your event customizations
    return $event;
}, 10, 2);

// Add custom CSS classes
add_filter('eventcrafter_wrapper_classes', function($classes) {
    $classes[] = 'my-custom-class';
    return $classes;
});
```

### JSON Data Schema
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

## 📈 SEO & Performance

- ✅ **Semantic HTML** for better search engine indexing
- ✅ **Schema.org markup** for rich snippets
- ✅ **Optimized loading** with lazy loading and caching
- ✅ **Core Web Vitals** optimized for Google rankings

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md).

### Development Setup
```bash
git clone https://github.com/TableCrafter/eventcrafter-visual-timeline.git
cd eventcrafter-visual-timeline
npm install
npm run dev
```

### Testing
```bash
# Run PHP tests
composer install
composer test

# Run accessibility tests  
npm run test:accessibility
```

## 📄 License

GPL v2 or later - see [LICENSE](LICENSE) file.

## 🌟 Support EventCrafter

- ⭐ [Rate us on WordPress.org](https://wordpress.org/plugins/eventcrafter-visual-timeline/)
- 🐛 [Report issues on GitHub](https://github.com/TableCrafter/eventcrafter-visual-timeline/issues)
- 💬 [Get support on WordPress.org](https://wordpress.org/support/plugin/eventcrafter-visual-timeline/)

---

**Created by [Fahad Murtaza](https://github.com/fahdi)** | **Part of the [TableCrafter Suite](https://github.com/TableCrafter)**