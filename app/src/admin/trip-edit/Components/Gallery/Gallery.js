import { Button, Spinner, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';
import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
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
                    return <li key={index} onClick={onItemClick(image.id)} className={`gallery-item${featuredImage === parseInt(image.id) ? ' featured-image' : ''} ${image.transient && 'gallery-item-preview'}`}>
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