/**
 * EventCrafter Visual Builder
 */
jQuery(document).ready(function($) {
    if (!$('#ec-builder-app').length) return;

    var $container = $('#ec-events-list');
    var $input = $('#ec_timeline_data');
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
        console.error('Invalid JSON in ec_timeline_data', e);
    }

    // Initialize
    renderEvents();
    initColorPickers();

    // Event Handlers
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
    }

    function initColorPickers() {
        $('.ec-input-color').wpColorPicker({
            change: function(event, ui) {
                // Trigger change event to update state
                $(this).trigger('change');
            }
        });
    }

    function updateStorage() {
        $input.val(JSON.stringify(currentState));
    }
});
