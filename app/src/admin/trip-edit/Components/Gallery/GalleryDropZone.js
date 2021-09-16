import { Button, DropZone, Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const __i18n = {
	..._wp_travel_admin.strings
}

export default ({ onImagesDrop, onMediaLib }) => {
    const [{
        hasDropped
    }, setState] = useState({
        hasDropped: false
    })
    return  <div className="dropzone">
            {hasDropped &&
                <span className="loader">
                    <Spinner />
                </span> ||
                <>
                    <span className="uploader-info">
                        {__i18n.messages.upload_desc}
                    </span>
                    <div className="uploader-buttons">
                        <Button
                            isDefault={true}
                            onClick={(e) => e.target.nextElementSibling.click()}
                        >{__i18n.upload}</Button>
                        <input type="file" multiple id="trip-gallery-upload" onChange={(e) => onImagesDrop(e.target.files)} style={{ display: 'none' }} accept="image/*" />
                        <Button
                            isDefault={true}
                            onClick={() => onMediaLib()}>{__i18n.media_library}</Button>
                    </div>
                </>}
            <DropZone onFilesDrop={(images, position) => onImagesDrop(images)} />
        </div>
}