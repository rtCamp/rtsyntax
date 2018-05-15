// Edit and Save class, located in /block directory

import Edit from "./Edit.jsx";
import Save from "./Save.jsx";


// Initializing required components
const {registerBlockType} = wp.blocks;
const {__} = wp.i18n;


// Registering highlighter block
registerBlockType(
	'rtsyntax/rtsyntax-block',
	{
		title   : __('rtSyntax', 'rtsyntax'),
		category: 'formatting',
		icon    : 'editor-code',
		keywords: [__('highlight', 'rtsyntax'), __('code', 'rtsyntax')],
		supports: {
			html: false,
		},

		attributes: {

			// to store current language.
			language: {
				type   : 'string',
				default: 'php',
			},

			// to store only text content
			content: {
				type    : 'string',
				selector: 'pre',
				source  : 'children',
				default : '',
			},

		},

		// Edit is located inside /block/ directory
		edit: Edit,

		// Save is located inside /block/ directory
		save: Save,
	}
);
