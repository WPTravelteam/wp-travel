import apiFetch from '@wordpress/api-fetch';
import { dispatch, useSelect } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
import { useState } from '@wordpress/element';
import Gallery from './Gallery';
import GalleyDropZone from './GalleryDropZone';
import { __ } from '@wordpress/i18n'
import { Modal, Button } from '@wordpress/components'


export default () => {
    const [{ isUploading, isOpenModal }, setState] = useState({
        isUploading: false,
        isOpenModal: false
    })
    const store = useSelect(select => select('WPTravel/TripEdit'));
    const tripData = store.getAllStore()
    const { updateTripData } = dispatch('WPTravel/TripEdit')

    const { gallery, _thumbnail_id } = tripData
    const galleryMediaInstance = wp.media({
        multiple: true
    })

    galleryMediaInstance
        .on('open', () => {
            const library = galleryMediaInstance.state().get('library')
            let currentGallery = store.getAllStore().gallery
            currentGallery.forEach(element => {
                var attachment = wp.media.attachment(element.id)
                library.remove(attachment ? [attachment] : [])
            });

        })
        .on('select', () => {
            const selectedItems = galleryMediaInstance.state().get('selection').toJSON()
            // console.log(selectedItems)
            let currentGallery = store.getAllStore().gallery
            selectedItems.length > 0 && updateTripData({
                ...tripData,
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

    const onItemClickHandle = id => e => updateTripData({ ...tripData, _thumbnail_id: id })

    const onImagesDropHandle = async files => {
        setState(state => ({ ...state, isUploading: true }))
        if (files.length > 0) {
            let previewData = await previewImages(files)
            // console.debug(previewData)
            updateTripData({
                ...tripData,
                gallery: [...store.getAllStore().gallery, ...previewData],
            })
            const formData = new FormData()
            const headers = new Headers()
            headers.append('X-WP-Nonce', wpApiSettings.nonce)
            // let currentGallery = gallery
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
                        ...tripData,
                        gallery: [...newGallery],
                    })
                }
            }
        }
        setState(state => ({ ...state, isUploading: false }))

    }

    const onImagesSortHandle = (data) => {
        updateTripData({
            ...tripData,
            ...data
        })
    }

    const onRemoveImageHandle = index => e => {
        e.stopPropagation()
        if (confirm(__('Are you sure, want to remove the image from Gallery?'))) {
            updateTripData({
                ...tripData,
                gallery: gallery.filter((el, i) => i !== index)
            })
        }
    }

    const onMediaLibHandle = () => {
        galleryMediaInstance && galleryMediaInstance.open()
    }

    const closeModal = () => {
        setState(state => ({ ...state, isOpenModal: false }))
    }

    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border">
        <Gallery
            images={gallery}
            onImageRemove={onRemoveImageHandle}
            featuredImage={_thumbnail_id}
            onChange={() => console.log('changes')}
            onImagesSort={onImagesSortHandle}
            onItemClick={onItemClickHandle}
        />
        {!isUploading && <GalleyDropZone onImagesDrop={onImagesDropHandle} onMediaLib={onMediaLibHandle} />}
        {/* {
            isOpenModal && <Modal
                title={__('Remove Image?')}
                onRequestClose={closeModal}
            >
                <Button isSecondary onClick={onRemoveImageHandle()}>
                    {__('Remove Image')}
                </Button>
            </Modal>
        } */}
    </div>
}
