import { Button, DropZone, DropZoneProvider, Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

export default ({ onImagesDrop, onMediaLib }) => {
    const [{
        hasDropped
    }, setState] = useState({
        hasDropped: false
    })
    return <DropZoneProvider>
        <div className="dropzone">
            {hasDropped &&
                <span className="loader">
                    <Spinner />
                </span> ||
                <>
                    <span className="uploader-info">
                        {__('Drop files here to upload.')}
                    </span>
                    <div className="uploader-buttons">
                        <Button
                            isDefault={true}
                            onClick={(e) => e.target.nextElementSibling.click()}
                        >Upload</Button>
                        <input type="file" multiple id="trip-gallery-upload" onChange={(e) => onImagesDrop(e.target.files)} style={{ display: 'none' }} accept="image/*" />
                        <Button
                            isDefault={true}
                            onClick={() => onMediaLib()}>Media Library</Button>
                    </div>
                </>}
            <DropZone onFilesDrop={(images, position) => onImagesDrop(images)} />
        </div>
    </DropZoneProvider>
}