import { withSelect, dispatch } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
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
const Gallery = ( props ) => {
    const {
        images: gallery,
        featuredImage,
        onImageRemove,
        onChange,
        onImagesSort,
        onItemClick,
        drag,
        allData,
        setFeaturedImage
    } = props
    let draggable = 'undefined' != typeof drag ? drag : true;
    const { updateTripData } = dispatch('WPTravel/TripEdit')
    const setThumbnail = id => e => { 
        updateTripData({ ...allData, _thumbnail_id: id });
        setFeaturedImage( id );
    }

    if ( props && props.featuredImageId && props.featuredImageId !== allData._thumbnail_id ) {
        updateTripData({ ...allData, _thumbnail_id: props.featuredImageId  });
    }
    return <ErrorBoundary>
        { 'undefined' != typeof gallery && gallery && gallery.length > 0 &&
            <ReactSortable
                disabled={! draggable }
                list={gallery}
                setList={newList => onImagesSort({ gallery: newList })}
                tag="ul"
                className="wp-travel-gallery-list">
                {gallery.map((image, index) => {
                    const featuredClass        = 'undefined' != typeof featuredImage && ( featuredImage === parseInt( image.id ) ) ? ' featured-image' : '';
                    const featuredPreviewClass = 'undefined' != typeof image.transient && image.transient ? ' gallery-item-preview' : '';
                    return <li key={index} className={`gallery-item ${ featuredClass } ${featuredPreviewClass}`} style={{position:'relative'}}>
                        <div className={`wptravel-swap-list`}>
                            <Button
                            disabled={0 == index}
                            onClick={(e) => {
                                let sorted = swapList( gallery, index, index - 1 )
                                onImagesSort(sorted)
                            }}>
                                <svg className="gallery-prev-arrow" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path></svg>
                                </Button>
                            <Button 
                            disabled={( Object.keys(gallery).length - 1 ) === index}
                            onClick={(e) => {
                                let sorted = swapList( gallery, index, index + 1 )
                                onImagesSort(sorted)
                            }}>
                                <svg className="gallery-next-arrow" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><path d="M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"></path></svg>
                                </Button>
                        </div>
                        <figure>
                            <img src={image.thumbnail} onClick={setThumbnail(image.id)}  />
                            {
                                image.transient && <span className="loader">
                                    <Spinner />
                                </span>
                            }
                        </figure>
                        <Button onClick={onImageRemove(index)}>
                            <svg className="gallery-delete-button" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg>
                            </Button>
                    </li>
                })}
            </ReactSortable>
            || <Notice isDismissible={false} status="info">{__i18n.messages.no_gallery}</Notice>}
    </ErrorBoundary>
}
// remove @since 6.4.0
export default withSelect( (select ) => { })( Gallery )