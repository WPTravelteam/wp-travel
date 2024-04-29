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
        });
    };
    console.log( _wp_travel.pro_version );
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.pwa.pwa_settings}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("Additional PWA settings.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {
                        _wp_travel.is_pro_enable == 'yes' && _wp_travel.pro_version >= 5.4 &&
                        <>
                            <PanelRow>
                                <label>{_wp_travel.setting_strings.pwa.enable_pwa}</label>
                                <div id="wp-travel-pwa-enable" className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={enable_pwa == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                enable_pwa: 'yes' == enable_pwa ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{_wp_travel.setting_strings.pwa.enable_pwa_note}</p>
                                </div>
                            </PanelRow>
                            {
                                enable_pwa == "yes" &&
                                <>
                                    <PanelRow>
                                        <label>{_wp_travel.setting_strings.pwa.app_fullname}</label>
                                        <div id="wp-travel-pwa-app-fullname" className="wp-travel-field-value">
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
                                            <p className="description">{_wp_travel.setting_strings.pwa.app_fullname_note}</p>
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{_wp_travel.setting_strings.pwa.app_short_name}</label>
                                        <div id="wp-travel-pwa-app-shortname" className="wp-travel-field-value">
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
                                            <p className="description">{_wp_travel.setting_strings.pwa.app_short_name_note}</p>
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{_wp_travel.setting_strings.pwa.start_url}</label>
                                        <div id="wp-travel-pwa-start-url" className="wp-travel-field-value">
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
                                            <p className="description">{_wp_travel.setting_strings.pwa.start_url_note}</p>
                                        </div>
                                    </PanelRow>

                                    <PanelRow>
                                        <label>{_wp_travel.setting_strings.pwa.app_logo}</label>
                                        <div id="wp-travel-pwa-app-logo" className="wp-travel-field-value">
                                            <div className="wp-travel-field-image-container">
                                                <img src={pwa_app_logo} />
                                            </div>
                                            <button className="components-button is-secondary" href="#" id="upload-app-logo" onClick={onClick}>{__('Change Image')}</button>
                                            <p className="description">{_wp_travel.setting_strings.pwa.app_logo_note}</p>
                                        </div>
                                    </PanelRow>
                                </>
                            }
                        </>
                        // || _wp_travel.pro_version < 5.4 && _wp_travel.pro_version != null &&
                        // <Notice isDismissible={false} status="informational">
                        //     <strong>{__('Looks like you haven\'t updated your WP Travel Pro plugin.', 'wp-travel')}</strong>
                        //     <br />
                        //     {__('Update WP Travel Pro to gain access to the new PWA feature as well as other additional settings.', 'wp-travel')}
                        //     <br />
                        //     <br />
                        //     <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Update WP Travel Pro', 'wp-travel')}</a>
                        // </Notice>
                        ||
                        <Notice isDismissible={false} status="informational">
                            <strong>{__('Want to add PWA?', 'wp-travel')}</strong>
                            <br />
                            {__('Get WP Travel Pro modules for PWA features.', 'wp-travel')}
                            <br />
                            <br />
                            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                        </Notice>
                    }
                </ErrorBoundary>
            </div>
        </>
    )
}