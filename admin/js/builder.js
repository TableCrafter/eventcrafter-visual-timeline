/**
 * EventCrafter Visual Builder
 */
jQuery(document).ready(function($) {
    if (!$('#ec-builder-app').length) return;

    var $container = $('#ec-events-list');
    var $input = $('#eventcrafter_tl_data');
    var $tmpl = $('#tmpl-ec-event').html();
    
    // Parse Initial Data
    var currentState = {
        settings: { layout: 'vertical' },
        events: []
    };

    try {
        var raw = $input.val();
        if (raw) currentState = JSON.parse(raw);
    } catch (e) {
        console.error('Invalid JSON in eventcrafter_tl_data', e);
    }

    // Initialize
    renderEvents();
    renderEvents();
    renderPreview();
    initColorPickers();

    // Event Handlers
    $('#ec-copy-shortcode').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.text();
        var code = $('#ec-shortcode-preview').text();
        
        var success = function() {
            $btn.text('Copied!');
            $btn.removeClass('button-secondary').addClass('button-primary');
            setTimeout(function() { 
                $btn.text(originalText); 
                $btn.removeClass('button-primary').addClass('button-secondary'); // Assuming it started as secondary or default
            }, 2000);
        };

        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(success).catch(function() {
                // Fallback if promise fails
                fallbackCopy(code, success);
            });
        } else {
            fallbackCopy(code, success);
        }
    });

    function fallbackCopy(text, callback) {
        var textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed"; // Prevent scrolling
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
            document.execCommand("copy");
            if (callback) callback();
        } catch (err) {
            console.error('Fallback copy failed', err);
            alert('Could not copy text. Please select and copy manually.');
        }
        document.body.removeChild(textarea);
    }

    $('#ec-add-event').on('click', function() {
        var newEvent = {
            id: Date.now(),
            title: 'New Event',
            date: new Date().getFullYear(),
            description: '',
            color: '#3b82f6',
            category: ''
        };
        currentState.events.push(newEvent);
        renderEvents();
        updateStorage();
    });

    // Delegate Events for dynamic items
    $container.on('click', '.ec-toggle-edit', function() {
        $(this).closest('.ec-event-card').find('.ec-event-body').slideToggle();
    });

    $container.on('click', '.ec-delete-event', function() {
        if (!confirm('Are you sure you want to delete this event?')) return;
        var id = $(this).closest('.ec-event-card').data('id');
        currentState.events = currentState.events.filter(function(ev) { return ev.id != id; });
        renderEvents();
        updateStorage();
    });

    // Input Changes
    $container.on('change keyup', 'input, textarea', function() {
        var $card = $(this).closest('.ec-event-card');
        var id = $card.data('id');
        var event = currentState.events.find(function(ev) { return ev.id == id; });
        
        if (event) {
            if ($(this).hasClass('ec-input-title')) event.title = $(this).val();
            if ($(this).hasClass('ec-input-date')) event.date = $(this).val();
            if ($(this).hasClass('ec-input-desc')) event.description = $(this).val();
            if ($(this).hasClass('ec-input-category')) event.category = $(this).val();
            // Color is handled by wpColorPicker callbacks usually, but change triggers on input update
            if ($(this).hasClass('ec-input-color')) event.color = $(this).val();
            
            // Update preview text
            $card.find('.ec-event-preview-title').text(event.title);
            $card.find('.ec-event-preview-date').text(event.date);
            
            updateStorage();
        }
    });

    function renderEvents() {
        $container.empty();
        
        if (!currentState.events || currentState.events.length === 0) {
            $container.html('<p class="ec-empty-state">No events yet. Click "Add Event" to start building your timeline.</p>');
            return;
        }

        currentState.events.forEach(function(event) {
             // Assign temp ID if missing
             if (!event.id) event.id = Date.now() + Math.random();
             
             var html = $tmpl
                .replace(/{{id}}/g, event.id)
                .replace(/{{title}}/g, event.title || '')
                .replace(/{{date}}/g, event.date || '')
                .replace(/{{description}}/g, event.description || '')
                .replace(/{{color}}/g, event.color || '#3b82f6')
                .replace(/{{category}}/g, event.category || '');
             
             $container.append(html);
        });
        
        initColorPickers();
        initSortable();
    }

    function initSortable() {
        if ($container.data('ui-sortable')) {
            $container.sortable('refresh');
            return;
        }

        $container.sortable({
            handle: '.ec-drag-handle',
            placeholder: 'ec-sortable-placeholder',
            forcePlaceholderSize: true,
            update: function(event, ui) {
                var newOrder = [];
                $container.find('.ec-event-card').each(function() {
                     var id = $(this).data('id');
                     var eventObj = currentState.events.find(function(ev) { return ev.id == id; });
                     if (eventObj) newOrder.push(eventObj);
                });
                currentState.events = newOrder;
                updateStorage();
            }
        });
    }

    function renderPreview() {
        var $preview = $('#ec-live-preview');
        
        if (!currentState.events || currentState.events.length === 0) {
            $preview.html('<p style="color:#666; font-style:italic;">No events to preview.</p>');
            return;
        }

        var html = '<div class="eventcrafter-timeline eventcrafter-vertical">';
        
        currentState.events.forEach(function(event) {
            var color = event.color || '#3b82f6';
            html += '<div class="eventcrafter-item" style="--event-color: ' + color + ';">';
            html += '<div class="eventcrafter-marker"></div>';
            html += '<div class="eventcrafter-content">';
            
            html += '<div class="eventcrafter-header">';
            if (event.date) {
                html += '<span class="eventcrafter-date">' + escapeHtml(event.date) + '</span>';
            }
            if (event.category) {
                html += '<span class="eventcrafter-category">' + escapeHtml(event.category) + '</span>';
            }
            html += '</div>'; // .eventcrafter-header

            html += '<h3 class="eventcrafter-title">' + escapeHtml(event.title || 'Untitled') + '</h3>';
            
            if (event.description) {
                // Determine if description has HTML or is plain text.
                // For security in preview (and general safety), we should strip unsafe tags or just allow basic formatting.
                // For this MVP preview, we'll assume the user is trusted admin and allow inserting HTML, 
                // but usually wp.editor would cover this.
                html += '<div class="eventcrafter-description">' + event.description + '</div>';
            }

            html += '</div></div>'; // .eventcrafter-content, .eventcrafter-item
        });

        html += '</div>'; // .eventcrafter-timeline
        $preview.html(html);
    }
    
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function initColorPickers() {
        if (!$.fn.wpColorPicker) return;

        $('.ec-input-color').each(function() {
            var $input = $(this);
            // Verify if already initialized to avoid double init (though renderEvents uses empty() so likely safe)
            if ($input.hasClass('wp-color-picker-initialized')) return;

            $input.wpColorPicker({
                change: function(event, ui) {
                    // Direct state update to avoid bubbling issues
                    var color = ui.color.toString();
                    var $card = $(this).closest('.ec-event-card');
                    var id = $card.data('id');
                    var eventObj = currentState.events.find(function(ev) { return ev.id == id; });
                    
                    if (eventObj) {
                        eventObj.color = color;
                        // Also update the input value physically just in case
                        $(this).val(color);
                        updateStorage();
                    }
                },
                clear: function() {
                    // Handle clear if needed
                    var $card = $(this).closest('.ec-event-card');
                    var id = $card.data('id');
                    var eventObj = currentState.events.find(function(ev) { return ev.id == id; });
                    if (eventObj) {
                         eventObj.color = '';
                         updateStorage();
                    }
                }
            }).addClass('wp-color-picker-initialized');
        });
    }

    function updateStorage() {
        $input.val(JSON.stringify(currentState));
        renderPreview();
    }
});
