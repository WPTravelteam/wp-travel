import { Button, Spinner, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';
import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
// Swap any array or object as per provided index.
const  swapList = (data, old_index, new_index) => {
    if ( 'object' === typeof data ) {
        if (new_index >= Object.keys(data).length) {
            var k = new_index - Object.keys(data).length + 1;
            while (k--) {
                data.push(undefined);
            }
        }
        data.splice(new_index, 0, data.splice(old_index, 1)[0]);
    }
    if ( 'array' === typeof data ) {
        if (new_index >= data.length) {
            var k = new_index - data.length + 1;
            while (k--) {
                data.push(undefined);
            }
        }
        data.splice(new_index, 0, data.splice(old_index, 1)[0]);
    }
    return data;
};
export default props => {
    const {
        images: gallery,
        featuredImage,
        onImageRemove,
        onChange,
        onImagesSort,
        onItemClick
    } = props
    return <ErrorBoundary>
        { 'undefined' != typeof gallery && gallery.length > 0 &&
            <ReactSortable
                list={gallery}
                setList={newList => onImagesSort({ gallery: newList })}
                tag="ul"
                className="wp-travel-gallery-list">
                {gallery.map((image, index) => {
                    return <li key={index} onClick={onItemClick(image.id)} className={`gallery-item${featuredImage === parseInt(image.id) ? ' featured-image' : ''} ${image.transient && 'gallery-item-preview'}`} style={{position:'relative'}}>
                        <div className={`wptravel-swap-list`}>
                            <Button
                            // style={{padding:0, display:'block'}}
                            disabled={0 == index}
                            onClick={(e) => {
                                let sorted = swapList( gallery, index, index - 1 )
                                onImagesSort(sorted)
                                // updateRequestSending(true); // Temp fixes to reload the content.
                                // updateRequestSending(false);
                            }}><i className="dashicons dashicons-arrow-left"></i></Button>
                            <Button 
                            // style={{padding:0, display:'block'}}
                            disabled={( Object.keys(gallery).length - 1 ) === index}
                            onClick={(e) => {
                                let sorted = swapList( gallery, index, index + 1 )
                                onImagesSort(sorted)
                                // updateRequestSending(true);
                                // updateRequestSending(false);
                            }}><i className="dashicons dashicons-arrow-right"></i></Button>
                        </div>
                        <figure>
                            <img src={image.thumbnail} />
                            {
                                image.transient && <span className="loader">
                                    <Spinner />
                                </span>
                            }
                        </figure>
                        <Button onClick={onImageRemove(index)}><div className="dashicons dashicons-no delete-image"></div></Button>
                    </li>
                })}
            </ReactSortable>
            || <Notice isDismissible={false} status="info">{__i18n.messages.no_gallery}</Notice>}
    </ErrorBoundary>
}