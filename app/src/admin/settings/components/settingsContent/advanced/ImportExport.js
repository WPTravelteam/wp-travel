import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ColorPalette, ToggleControl, TextControl, Tooltip, Icon, Notice } from '@wordpress/components';
import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';


export default () => {

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Import Export Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("Additional PWA settings.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <h3>{__("Export Settings", "wp-travel")}</h3>
                <p>{__("Click Export to export all the WP Travel settings Data.", "wp-travel")}</p>
                <button type="button" id="wp-travel-export-settings" class="components-button is-secondary">{__("Export", "wp-travel")}</button>
                <br/><br/>
                <h3>{__("Import Settings", "wp-travel")}</h3>
                <p>{__("Upload file and click Import file to import settings data.", "wp-travel")}</p>
                <button type="button" id="wp-travel-import-settings" class="components-button is-secondary">{__("Import", "wp-travel")}</button>

            </div>
        </>
    )
}