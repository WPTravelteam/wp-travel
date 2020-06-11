import { Button, Spinner, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';
export default props => {
    const {
        images: gallery,
        featuredImage,
        onImageRemove,
        onChange,
        onImagesSort,
        onItemClick
    } = props
    return <>
        {gallery.length > 0 &&
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
            || <Notice isDismissible={false} status="info">{__('There are no gallery images.')}</Notice>}
    </>
}