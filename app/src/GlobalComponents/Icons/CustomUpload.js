import { _n, __ } from '@wordpress/i18n';
import { PanelRow, Spinner, Button } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

// Custom Upload Content.
const CustomUploadContent = (props) => {
    sessionStorage.setItem('WPTravelLastSelectedTab', 'custom-upload');

    const [{ imageUrl, isFetchingImage }, setState] = useState({
        imageUrl: props.fact.icon_img ? props.fact.icon_img : null,
        isFetchingImage: false,
    })

    const mediaInstance = wp.media({
        multiple: false
    })

    useEffect(() => {
        
        if ( sessionStorage.length > 1 && "wpTravelIconModuleUploaderData" in sessionStorage && '' != sessionStorage.getItem('wpTravelIconModuleUploaderData') ) {
            setState({
                isFetchingImage: true
            });
            
            const imgDataString = sessionStorage.getItem('wpTravelIconModuleUploaderData');
            const imgData = JSON.parse(imgDataString);

            const [ imgDataObj ] = imgData;

            const { url } = imgDataObj;

            setState({
                imageUrl: url,
                isFetchingImage: false,
            })
        }
    }, []);

    mediaInstance
        .on('select', () => {
            const selectedItems = mediaInstance.state().get('selection').toJSON()
            if ( selectedItems.length > 0 ) {

                sessionStorage.setItem('wpTravelIconModuleUploaderData', '');
                sessionStorage.setItem('wpTravelIconModuleUploaderData', JSON.stringify(selectedItems));
                props.updateFact( 'selected_icon_type', 'custom-upload', props.index );

            }
            // setOpen(true);
            props.tabHandleClick(true);
        })

        const onMediaUploaderBtnClicked = () => {
            mediaInstance.open();
        }

        const onMediaRemoveBtnClicked = () => {
            setState({
                imageUrl: null,
            });
            sessionStorage.removeItem('wpTravelIconModuleUploaderData');

            props.updateFact( 'icon_img', '', props.index );
            props.updateFact( 'icon_img_id', '', props.index );
            props.updateFact( 'selected_icon_type', 'custom-upload', props.index );
        }

        return <>
        <PanelRow>
            <h3>Icon</h3>
            <div className="wp-travel-field-value">
                <div className="media-preview">
                    {isFetchingImage && <Spinner />}
                    {imageUrl && <img src={imageUrl} height="100" width="20%" />}
                    <div className="wti_custom_uploader_btn_wrapper">
                        <Button variant="primary" onClick={onMediaUploaderBtnClicked}>{ imageUrl ? __('Change image', 'wp-travel' ) : __( 'Select image', 'wp-travel' )}</Button>
                        {
                            imageUrl &&
                            <Button className="wti_custom_remove_btn" isDestructive onClick={onMediaRemoveBtnClicked}>{ __('Remove image', 'wp-travel' ) }</Button>
                        }
                    </div>
                </div>
            </div>
        </PanelRow>
        </>
}

export default CustomUploadContent;