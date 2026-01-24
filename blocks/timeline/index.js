/**
 * EventCrafter Timeline Block
 *
 * Registers a Gutenberg block to embed EventCrafter timelines.
 */

import { registerBlockType } from '@wordpress/blocks';
import './editor.scss';
import './style.scss';
import Edit from './edit';
import metadata from './block.json';

/**
 * Register the block.
 * Uses dynamic rendering via PHP - no save function needed.
 */
registerBlockType(metadata.name, {
    edit: Edit,
    // Dynamic block - rendered server-side via render.php
    save: () => null,
});
