/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
 
const blockStyle = {
    backgroundColor: '#900',
    color: '#fff',
    padding: '20px',
};
 
registerBlockType( 'wptravel/searchfilter', {
    apiVersion: 2,
    title: 'WP Travel Search Filter',
    icon: 'universal-access-alt',
    category: 'widgets',
	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'core/legacy-widget' ],
				isMatch: ( { idBase, instance } ) => {
					// console.log('idBase', idBase)
					// console.log('instance', instance)
					if ( ! instance?.raw ) {
						// Can't transform if raw instance is not shown in REST API.
						return false;
					}
					return idBase === 'wp_travel_search_filter_widget';
				},
				transform: ( { instance } ) => {
					return createBlock( 'wptravel/searchfilter', {
						name: instance.raw.name,
					} );
				},
			},
		]
	},
    edit() {
        const blockProps = useBlockProps( { style: blockStyle } );
 
        return (
            <div { ...blockProps }>Hello World (from the editor).</div>
        );
    },
    save() {
        const blockProps = useBlockProps.save( { style: blockStyle } );
 
        return (
            <div { ...blockProps }>
                Hello World (from the frontend).
            </div>
        );
    },
} );

