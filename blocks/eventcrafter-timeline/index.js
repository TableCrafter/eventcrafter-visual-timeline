(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl, RangeControl, Placeholder } = wp.components;
    const { useSelect } = wp.data;
    const { __ } = wp.i18n;
    const { createElement: el, Fragment } = wp.element;

    registerBlockType('eventcrafter/timeline', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { timelineId, sourceUrl, layout, limit } = attributes;
            const blockProps = useBlockProps();

            // Get available timelines
            const timelines = useSelect((select) => {
                return select('core').getEntityRecords('postType', 'eventcrafter_tl', {
                    per_page: -1,
                    status: 'publish'
                }) || [];
            }, []);

            // Transform timelines for select options
            const timelineOptions = [
                { value: '', label: __('Select a timeline...', 'eventcrafter-visual-timeline') },
                ...timelines.map(timeline => ({
                    value: timeline.id.toString(),
                    label: timeline.title.rendered || __('(no title)', 'eventcrafter-visual-timeline')
                }))
            ];

            const layoutOptions = [
                { value: 'vertical', label: __('Vertical', 'eventcrafter-visual-timeline') },
                { value: 'horizontal', label: __('Horizontal', 'eventcrafter-visual-timeline') }
            ];

            return el(Fragment, {},
                el('div', blockProps,
                    el(Placeholder, {
                        icon: 'excerpt-view',
                        label: __('EventCrafter Timeline', 'eventcrafter-visual-timeline'),
                        instructions: timelineId || sourceUrl 
                            ? __('Timeline configured. Use sidebar to modify settings.', 'eventcrafter-visual-timeline')
                            : __('Select a timeline or enter a source URL to display your timeline.', 'eventcrafter-visual-timeline')
                    },
                        !timelineId && !sourceUrl && 
                        el('div', { style: { marginTop: '16px' } },
                            el(SelectControl, {
                                label: __('Choose Timeline', 'eventcrafter-visual-timeline'),
                                value: timelineId,
                                options: timelineOptions,
                                onChange: (value) => setAttributes({ timelineId: value, sourceUrl: '' }),
                                style: { minWidth: '200px' }
                            })
                        )
                    )
                ),
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Timeline Settings', 'eventcrafter-visual-timeline'),
                        initialOpen: true
                    },
                        el(SelectControl, {
                            label: __('Timeline', 'eventcrafter-visual-timeline'),
                            value: timelineId,
                            options: timelineOptions,
                            onChange: (value) => setAttributes({ timelineId: value, sourceUrl: value ? '' : sourceUrl }),
                            help: __('Select a timeline created in the admin area.', 'eventcrafter-visual-timeline')
                        }),
                        el('hr'),
                        el(TextControl, {
                            label: __('Or JSON Source URL', 'eventcrafter-visual-timeline'),
                            value: sourceUrl,
                            onChange: (value) => setAttributes({ sourceUrl: value, timelineId: value ? '' : timelineId }),
                            placeholder: __('https://example.com/data.json', 'eventcrafter-visual-timeline'),
                            help: __('External JSON source URL (overrides timeline selection).', 'eventcrafter-visual-timeline')
                        }),
                        el('hr'),
                        el(SelectControl, {
                            label: __('Layout', 'eventcrafter-visual-timeline'),
                            value: layout,
                            options: layoutOptions,
                            onChange: (value) => setAttributes({ layout: value }),
                            help: __('Choose the timeline layout style.', 'eventcrafter-visual-timeline')
                        }),
                        el(RangeControl, {
                            label: __('Event Limit', 'eventcrafter-visual-timeline'),
                            value: limit === -1 ? 0 : limit,
                            onChange: (value) => setAttributes({ limit: value === 0 ? -1 : value }),
                            min: 0,
                            max: 100,
                            help: limit === -1 
                                ? __('Show all events', 'eventcrafter-visual-timeline')
                                : __(`Show ${limit} events`, 'eventcrafter-visual-timeline')
                        })
                    )
                )
            );
        },

        save: function() {
            // Server-side rendering - return null
            return null;
        }
    });

})(window.wp);