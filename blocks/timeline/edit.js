/**
 * EventCrafter Timeline Block - Editor Component
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl,
    ToggleControl,
    TextControl,
    Placeholder,
    Spinner,
    Button,
    __experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Editor component for the EventCrafter Timeline block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {Element} Editor element.
 */
export default function Edit({ attributes, setAttributes }) {
    const { timelineId, source, layout, limit, showAnimation } = attributes;
    const blockProps = useBlockProps();

    // Fetch available timelines from REST API
    const { timelines, isLoading } = useSelect(
        (select) => {
            const { getEntityRecords, isResolving } = select('core');
            return {
                timelines: getEntityRecords('postType', 'ec_timeline', {
                    per_page: 100,
                    status: 'publish',
                }),
                isLoading: isResolving('getEntityRecords', [
                    'postType',
                    'ec_timeline',
                    { per_page: 100, status: 'publish' },
                ]),
            };
        },
        []
    );

    // Build timeline options for dropdown
    const timelineOptions = [
        {
            value: 0,
            label: __('Select a timeline...', 'eventcrafter-visual-timeline'),
        },
        ...(timelines || []).map((timeline) => ({
            value: timeline.id,
            label: timeline.title.rendered,
        })),
    ];

    const hasSelection = timelineId > 0 || source;

    // Inspector controls (sidebar)
    const inspectorControls = (
        <InspectorControls>
            <PanelBody
                title={__(
                    'Timeline Settings',
                    'eventcrafter-visual-timeline'
                )}
            >
                <SelectControl
                    label={__('Timeline', 'eventcrafter-visual-timeline')}
                    value={timelineId}
                    options={timelineOptions}
                    onChange={(value) =>
                        setAttributes({ timelineId: parseInt(value, 10) })
                    }
                />
                <SelectControl
                    label={__('Layout', 'eventcrafter-visual-timeline')}
                    value={layout}
                    options={[
                        {
                            value: 'vertical',
                            label: __(
                                'Vertical',
                                'eventcrafter-visual-timeline'
                            ),
                        },
                        {
                            value: 'horizontal',
                            label: __(
                                'Horizontal',
                                'eventcrafter-visual-timeline'
                            ),
                        },
                    ]}
                    onChange={(value) => setAttributes({ layout: value })}
                />
                <NumberControl
                    label={__(
                        'Max Events (-1 for all)',
                        'eventcrafter-visual-timeline'
                    )}
                    value={limit}
                    onChange={(value) =>
                        setAttributes({ limit: parseInt(value, 10) })
                    }
                    min={-1}
                />
                <ToggleControl
                    label={__(
                        'Enable Animations',
                        'eventcrafter-visual-timeline'
                    )}
                    checked={showAnimation}
                    onChange={(value) =>
                        setAttributes({ showAnimation: value })
                    }
                />
            </PanelBody>
            <PanelBody
                title={__('Advanced', 'eventcrafter-visual-timeline')}
                initialOpen={false}
            >
                <TextControl
                    label={__(
                        'External Source URL',
                        'eventcrafter-visual-timeline'
                    )}
                    value={source}
                    onChange={(value) => setAttributes({ source: value })}
                    help={__(
                        'Optional: Load timeline from external JSON URL',
                        'eventcrafter-visual-timeline'
                    )}
                />
            </PanelBody>
        </InspectorControls>
    );

    // Placeholder when no timeline selected
    if (!hasSelection) {
        return (
            <div {...blockProps}>
                {inspectorControls}
                <Placeholder
                    icon="excerpt-view"
                    label={__(
                        'EventCrafter Timeline',
                        'eventcrafter-visual-timeline'
                    )}
                    instructions={__(
                        'Select an existing timeline or enter a JSON URL.',
                        'eventcrafter-visual-timeline'
                    )}
                >
                    {isLoading ? (
                        <Spinner />
                    ) : (
                        <div className="ec-block-placeholder-content">
                            <SelectControl
                                value={timelineId}
                                options={timelineOptions}
                                onChange={(value) =>
                                    setAttributes({
                                        timelineId: parseInt(value, 10),
                                    })
                                }
                            />
                            <Button
                                variant="secondary"
                                href={
                                    (window.ecBlockData?.adminUrl ||
                                        '/wp-admin/') +
                                    'post-new.php?post_type=ec_timeline'
                                }
                                target="_blank"
                            >
                                {__(
                                    '+ Create New Timeline',
                                    'eventcrafter-visual-timeline'
                                )}
                            </Button>
                        </div>
                    )}
                </Placeholder>
            </div>
        );
    }

    // Live preview with ServerSideRender
    return (
        <div {...blockProps}>
            {inspectorControls}
            <ServerSideRender
                block="eventcrafter/timeline"
                attributes={attributes}
                LoadingResponsePlaceholder={() => (
                    <Placeholder icon="excerpt-view">
                        <Spinner />
                    </Placeholder>
                )}
                ErrorResponsePlaceholder={() => (
                    <Placeholder
                        icon="warning"
                        label={__(
                            'Error loading timeline',
                            'eventcrafter-visual-timeline'
                        )}
                    >
                        {__(
                            'There was an error loading this timeline. Please check your selection.',
                            'eventcrafter-visual-timeline'
                        )}
                    </Placeholder>
                )}
            />
            {timelineId > 0 && (
                <div className="ec-block-edit-link">
                    <Button
                        variant="secondary"
                        href={
                            (window.ecBlockData?.adminUrl || '/wp-admin/') +
                            `post.php?post=${timelineId}&action=edit`
                        }
                        target="_blank"
                    >
                        {__('Edit Timeline', 'eventcrafter-visual-timeline')}
                    </Button>
                </div>
            )}
        </div>
    );
}
