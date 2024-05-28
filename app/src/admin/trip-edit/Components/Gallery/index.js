import { addFilter } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';
import { dispatch, useSelect } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
import { useState } from '@wordpress/element';
import { PanelRow, TextControl, Tooltip } from '@wordpress/components';    /** import WordPress components @since 7.7.0 */
import Gallery from './Gallery';
import GalleyDropZone from './GalleryDropZone';
import { __ } from '@wordpress/i18n'

const __i18n = {
	..._wp_travel_admin.strings
}


// Single Components for hook callbacks.

/**
 * Add Featured Trip Video Option to Gallery tab
 * 
 * @since 7.7.0
 * @param {Object} allData
 * @returns JSX
 */
const FeaturedTripVideo = ({ allData }) => {
    const { trip_video_code } = allData;
    const { updateTripData } = dispatch("WPTravel/TripEdit");

    return (
        <>
            <div className='wp-travel-itinerary-title'>
                <h3 className='wp-travel-tab-content-title'>{ __i18n.featured_trip_video }</h3>
            </div>
            <PanelRow>
                <label>
                    { __i18n.video_url }{ " " }
                    <Tooltip text="The embeded video will appear as a play button overlay on top of featured image">
                        <i className='fa fa-info-circle'></i>
                    </Tooltip>
                </label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={trip_video_code}
                        onChange={(trip_video_code) => {
                            updateTripData({
                                ...allData,
                                trip_video_code: trip_video_code,
                            });
                        }}
                    />
                    <p className='description'>{__i18n.notices.featured_trip_video_option.description}</p>
                </div>
            </PanelRow>
        </>
    );
}

/**
 * Add Gallery Title to Gallery Images section
 * 
 * @since 7.7.0
 * @returns JSX
 */
const GalleryTitle = () => {
    return (
        <div className='wp-travel-itinerary-title'>
            <h3 className='wp-travel-tab-content-title'>{ __i18n.gallery_images }</h3>
        </div>
    );
}

const SimpleGallery = ({allData, drag=true }) => {
    const [{ isUploading, isOpenModal }, setState] = useState({
        isUploading: false,
        isOpenModal: false
    })
    const store = useSelect(select => select('WPTravel/TripEdit'));
    const { updateTripData } = dispatch('WPTravel/TripEdit')

    const { gallery, _thumbnail_id } = allData
    const galleryMediaInstance = wp.media({
        multiple: true
    })

    galleryMediaInstance
        .on('open', () => {
            const library = galleryMediaInstance.state().get('library')
            let currentGallery = store.getAllStore().gallery;
            if ( currentGallery ) {
                currentGallery.forEach(element => {
                    var attachment = wp.media.attachment(element.id)
                    library.remove(attachment ? [attachment] : [])
                });
            }

        })
        .on('select', () => {
            const selectedItems = galleryMediaInstance.state().get('selection').toJSON()
            let currentGallery = store.getAllStore().gallery ? store.getAllStore().gallery : [];
            selectedItems.length > 0 && updateTripData({
                ...allData,
                gallery: [...currentGallery, ...selectedItems.map(item => ({ id: item.id, thumbnail: item.url }))]
            })
        })

    const previewImages = files => {
        return new Promise(async (resolve, reject) => {
            let previewData = []
            for (let i = 0; i < files.length; i++) {
                let tempUrl = URL.createObjectURL(files[i])
                previewData = [...previewData, { id: i, thumbnail: tempUrl, transient: true }]
            }
            resolve([...previewData])
        })
    }


    const onImagesDropHandle = async files => {
        setState(state => ({ ...state, isUploading: true }))
        if (files.length > 0) {
            let previewData = await previewImages(files)
            let galleryData = store.getAllStore().gallery ? store.getAllStore().gallery : [];
            updateTripData({
                ...allData,
                gallery: [...galleryData, ...previewData],
            })
            const formData = new FormData()
            const headers = new Headers()
            headers.append('X-WP-Nonce', wpApiSettings.nonce)
            for (let i = 0; i < files.length; i++) {
                formData.append('file', files[i])
                const requestOptions = {
                    method: 'POST',
                    headers,
                    body: formData,
                }
                const res = await apiFetch({
                    path: '/wp/v2/media',
                    ...requestOptions
                })
                if (res.id) {
                    let newGallery = [...store.getAllStore().gallery]
                    newGallery.splice(newGallery.length - previewData.length, 1, { id: res.id, thumbnail: res.source_url })
                    previewData.shift()
                    updateTripData({
                        ...allData,
                        gallery: [...newGallery],
                    })
                }
            }
        }
        setState(state => ({ ...state, isUploading: false }))
    }

    const onImagesSortHandle = (data) => {
        updateTripData({
            ...allData,
            ...data
        })
    }

    const onRemoveImageHandle = index => e => {
        e.stopPropagation()
        if (confirm( __i18n.alert.remove_gallery )) {
            updateTripData({
                ...allData,
                gallery: gallery.filter((el, i) => i !== index)
            })
        }
    }

    const onMediaLibHandle = () => {
        galleryMediaInstance && galleryMediaInstance.open()
    }

    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-sortable-component">
        <Gallery
            images={gallery}
            onImageRemove={onRemoveImageHandle}
            featuredImage={_thumbnail_id}
            onChange={() => console.log('changes')}
            onImagesSort={onImagesSortHandle}
            drag={drag}
            allData={allData}
        />
        {!isUploading && <GalleyDropZone onImagesDrop={onImagesDropHandle} onMediaLib={onMediaLibHandle} />}
    </div>
}

// Callbacks.
const SimpleGalleryCB = ( content, allData ) => {
    return [ ...content, <SimpleGallery allData={allData} key="SimpleGallery" /> ];
}

const GalleryTitleCB = ( content ) => {
    return [ ...content, <GalleryTitle key="GalleryTitle" /> ];
}

const FeaturedTripVideoCB = ( content, allData ) => {
    return [ ...content, <FeaturedTripVideo allData={allData} key="FeaturedTripVideo" /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_gallery', 'WPTravel/TripEdit/FeaturedTripVideo', FeaturedTripVideoCB, 10 );
addFilter( 'wptravel_trip_edit_tab_content_gallery', 'WPTravel/TripEdit/GalleryTitle', GalleryTitleCB, 20 );
addFilter( 'wptravel_trip_edit_tab_content_gallery', 'WPTravel/TripEdit/SimpleGallery', SimpleGalleryCB, 30 );
addFilter( 'wptravel_trip_edit_block_tab_gallery', 'WPTravel/TripEdit/Block/Gallery/SimpleGallery', ( content, allData ) => {
    return [ ...content, <SimpleGallery allData={allData} key="SimpleGallery" drag={false} /> ];
}, 40 );
