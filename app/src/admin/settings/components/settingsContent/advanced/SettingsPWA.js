import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ColorPalette, ToggleControl, TextControl, Tooltip, Icon, Notice } from '@wordpress/components';
import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

const __i18n = {
    ..._wp_travel_admin.strings
}


export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {
        enable_pwa,
        pwa_app_name,
        pwa_app_short_name,
        pwa_app_start_url,
        pwa_app_logo,
        options } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');

    const onClick = (e) => {
        e.preventDefault();
        var m = wp.media({
            'title': 'Media',
            button: {
                text: 'Select',
            },
            multiple: false  // Set to true to allow multiple files to be selected
        }).open();

        m.on('select', () => {
            var attachment = m.state().get('selection').first().toJSON();

            updateSettings({
                ...allData,
                pwa_app_logo: attachment.url
            });
            console.log(attachment.url);
        });
    };

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("PWA Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("Additional PWA settings.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {
                        _wp_travel.is_pro_enable == 'no' &&
                        <Notice isDismissible={false} status="informational">
                            <strong>{__('Want to add PWA?', 'wp-travel')}</strong>
                            <br />
                            {__('Get WP Travel Pro modules for PWA features.', 'wp-travel')}
                            <br />
                            <br />
                            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                        </Notice>
                        ||
                        <>
                            <PanelRow>
                                <label>{__('Enable PWA', 'wp-travel')}</label>
                                <div className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={enable_pwa == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                enable_pwa: 'yes' == enable_pwa ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{__('Enable to activate PWA on your site', 'wp-travel')}</p>
                                </div>
                            </PanelRow>
                            {
                                enable_pwa == "yes" &&
                                <>
                                    <PanelRow>
                                        <label>{__('App Fullname', 'wp-travel')}</label>
                                        <div className="wp-travel-field-value">
                                            <TextControl
                                                // help={__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}
                                                value={pwa_app_name}
                                                onChange={
                                                    (value) => {
                                                        updateSettings({
                                                            ...allData,
                                                            pwa_app_name: value
                                                        })
                                                    }
                                                }
                                            />
                                            <p className="description">{__('This sets the App fullname', 'wp-travel')}</p>
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__('App short name', 'wp-travel')}</label>
                                        <div className="wp-travel-field-value">
                                            <TextControl
                                                // help={__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}
                                                value={pwa_app_short_name}
                                                onChange={
                                                    (value) => {
                                                        updateSettings({
                                                            ...allData,
                                                            pwa_app_short_name: value
                                                        })
                                                    }
                                                }
                                            />
                                            <p className="description">{__('This sets the App short name', 'wp-travel')}</p>
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__('Start Url', 'wp-travel')}</label>
                                        <div className="wp-travel-field-value">
                                            <TextControl
                                                // help={__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}
                                                value={pwa_app_start_url}
                                                type="url"
                                                onChange={
                                                    (value) => {
                                                        updateSettings({
                                                            ...allData,
                                                            pwa_app_start_url: value
                                                        })
                                                    }
                                                }
                                            />
                                            <p className="description">{__('This sets the App short name', 'wp-travel')}</p>
                                        </div>
                                    </PanelRow>

                                    <PanelRow>
                                        <label>{__('APP Logo', 'wp-travel')}</label>
                                        <div className="wp-travel-field-value">
                                            <img src={pwa_app_logo} />
                                            <br />
                                            <button className="components-button" href="#" id="upload-app-logo" onClick={onClick}>{__('Change Image')}</button>
                                            <p className="description">{__('The image must be of size 192px*192px', 'wp-travel')}</p>
                                        </div>
                                    </PanelRow>
                                </>
                            }
                        </>
                    }
                </ErrorBoundary>
            </div>
        </>
    )
}