import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, RadioControl, TextControl, ColorPicker, Button } from '@wordpress/components';
import WPEditor from '../../../../fields/WPEditor';
import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Email Templates Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More email templates according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>

                <BookingEmailTemplates />
                <PaymentEmailTemplates />
                <EnquiryEmailTemplates />

                {applyFilters('wp_travel_tab_content_after_email', [], allData)}
            </div>
        </>
    )
}

const BookingEmailTemplates = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        send_booking_email_to_admin,
        booking_admin_template_settings,
        booking_client_template_settings,
    } = allData;
    let sendBookingEmailToAdmin = 'undefined' != typeof send_booking_email_to_admin ? send_booking_email_to_admin : 'no'

    const updateEmailData = (storeName, storeKey, value) => { // storeName[storeKey] = value
        updateSettings({ ...allData, [storeName]: { ...allData[storeName], [storeKey]: value } })
    }

    // const resetContent = (storeName, storeKey ) => {
    //     if ( confirm( 'are you sure to reset?' ) ) {
    //         updateSettings({ ...allData, [storeName]: { ...allData[storeName], [storeKey]: 'jagat2' } })
    //     }
    // }
    return <ErrorBoundary>
        <PanelBody title={__('Booking Email Templates', 'wp-travel')} initialOpen={true} >
            <h4>{__('Admin Email Template Options', 'wp-travel')}</h4>
            <PanelRow>
                <label>{__('Send Email', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={sendBookingEmailToAdmin == 'yes'}
                        onChange={() => {
                            updateSettings({
                                ...allData,
                                send_booking_email_to_admin: 'yes' == sendBookingEmailToAdmin ? 'no' : 'yes'
                            })
                        }
                        }
                    />
                    <p className="description">{__('Enable or disable Email notification to admin.', 'wp-travel')}</p>
                </div>
            </PanelRow> 
            {applyFilters( 'wp_travel_email_template_content_after_send_email', [], allData ) }

            {applyFilters('wp_travel_utils_booking_notif', [], allData)}

            <PanelRow>
                <label>{__('Booking Email Subject', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={booking_admin_template_settings.admin_subject}
                        onChange={
                            (value) => { updateEmailData('booking_admin_template_settings', 'admin_subject', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Booking Email Title', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={booking_admin_template_settings.admin_title}
                        onChange={
                            (value) => { updateEmailData('booking_admin_template_settings', 'admin_title', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Booking Email Header Color', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <ColorPicker
                        color={booking_admin_template_settings.admin_header_color}
                        onChangeComplete={(value) => { updateEmailData('booking_admin_template_settings', 'admin_header_color', value.hex) }}
                        disableAlpha
                    />
                </div>
            </PanelRow>

            <PanelRow>
                <label>{__('Email Content', 'wp-travel')}</label>
                {/* <div className="wp-travel-field-value">
                    <Button isSecondary onClick={() => resetContent('booking_admin_template_settings', 'email_content')}>{ __( 'Reset Content', 'wp-travel' ) }</Button>
                </div> */}
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                <WPEditor
                    id="booking_admin_template_settings"
                    value={'undefined' !== typeof booking_admin_template_settings.email_content ? booking_admin_template_settings.email_content : ''}
                    onContentChange={(value) => {
                        updateEmailData('booking_admin_template_settings', 'email_content', value)
                    }} />
            </PanelRow>



            <h4>{__('Client Email Template Options', 'wp-travel')}</h4>
            <PanelRow>
                <label>{__('Booking Email Subject', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={booking_client_template_settings.client_subject}
                        onChange={
                            (value) => { updateEmailData('booking_client_template_settings', 'client_subject', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Booking Email Title', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={booking_client_template_settings.client_title}
                        onChange={
                            (value) => { updateEmailData('booking_client_template_settings', 'client_title', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Booking Email Header Color', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <ColorPicker
                        color={booking_client_template_settings.client_header_color}
                        onChangeComplete={(value) => { updateEmailData('booking_client_template_settings', 'client_header_color', value.hex) }}
                        disableAlpha
                    />
                </div>
            </PanelRow>

            <PanelRow>
                <label>{__('Email Content', 'wp-travel')}</label>
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                <WPEditor id="booking_client_template_settings" value={'undefined' !== typeof booking_client_template_settings.email_content ? booking_client_template_settings.email_content : ''}
                    onContentChange={(value) => {
                        updateEmailData('booking_client_template_settings', 'email_content', value)
                    }} />
            </PanelRow>

        </PanelBody>

    </ErrorBoundary>
}

const PaymentEmailTemplates = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        payment_admin_template_settings,
        payment_client_template_settings,
    } = allData;

    const updateEmailData = (storeName, storeKey, value) => { // storeName[storeKey] = value
        updateSettings({ ...allData, [storeName]: { ...allData[storeName], [storeKey]: value } })
    }

    return <>
        <PanelBody title={__('Payment Email Templates', 'wp-travel')} initialOpen={false} >
            <h4>{__('Admin Email Template Options', 'wp-travel')}</h4>

            {applyFilters('wp_travel_utils_payment_notif', [], allData)}

            <PanelRow>
                <label>{__('Payment Email Subject', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={payment_admin_template_settings.admin_subject}
                        onChange={
                            (value) => { updateEmailData('payment_admin_template_settings', 'admin_subject', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Payment Email Title', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={payment_admin_template_settings.admin_title}
                        onChange={
                            (value) => { updateEmailData('payment_admin_template_settings', 'admin_title', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Payment Email Header Color', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <ColorPicker
                        color={payment_admin_template_settings.admin_header_color}
                        onChangeComplete={(value) => { updateEmailData('payment_admin_template_settings', 'admin_header_color', value.hex) }}
                        disableAlpha
                    />
                </div>
            </PanelRow>

            <PanelRow>
                <label>{__('Email Content', 'wp-travel')}</label>
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                <WPEditor id="payment_admin_template_settings" value={'undefined' !== typeof payment_admin_template_settings.email_content ? payment_admin_template_settings.email_content : ''}
                    onContentChange={(value) => {
                        updateEmailData('payment_admin_template_settings', 'email_content', value)
                    }} />
            </PanelRow>



            <h4>{__('Client Email Template Options', 'wp-travel')}</h4>
            <PanelRow>
                <label>{__('Payment Email Subject', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={payment_client_template_settings.client_subject}
                        onChange={
                            (value) => { updateEmailData('payment_client_template_settings', 'client_subject', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Payment Email Title', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={payment_client_template_settings.client_title}
                        onChange={
                            (value) => { updateEmailData('payment_client_template_settings', 'client_title', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Payment Email Header Color', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <ColorPicker
                        color={payment_client_template_settings.client_header_color}
                        onChangeComplete={(value) => { updateEmailData('payment_client_template_settings', 'client_header_color', value.hex) }}
                        disableAlpha
                    />
                </div>
            </PanelRow>

            <PanelRow>
                <label>{__('Email Content', 'wp-travel')}</label>
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                <WPEditor id="payment_client_template_settings" value={'undefined' !== typeof payment_client_template_settings.email_content ? payment_client_template_settings.email_content : ''}
                    onContentChange={(value) => {
                        updateEmailData('payment_client_template_settings', 'email_content', value)
                    }} />
            </PanelRow>

        </PanelBody>

    </>
}
const EnquiryEmailTemplates = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        enquiry_admin_template_settings,
    } = allData;

    const updateEmailData = (storeName, storeKey, value) => { // storeName[storeKey] = value
        updateSettings({ ...allData, [storeName]: { ...allData[storeName], [storeKey]: value } })
    }

    return <>
        <PanelBody title={__('Enquiry Email Templates', 'wp-travel')} initialOpen={false} >
            <h4>{__('Admin Email Template Options', 'wp-travel')}</h4>

            {applyFilters('wp_travel_utils_enquiry_notif', [], allData)}

            <PanelRow>
                <label>{__('Enquiry Email Subject', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={enquiry_admin_template_settings.admin_subject}
                        onChange={
                            (value) => { updateEmailData('enquiry_admin_template_settings', 'admin_subject', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Enquiry Email Title', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={enquiry_admin_template_settings.admin_title}
                        onChange={
                            (value) => { updateEmailData('enquiry_admin_template_settings', 'admin_title', value) }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{__('Enquiry Email Header Color', 'wp-travel')}</label>
                <div className="wp-travel-field-value">
                    <ColorPicker
                        color={enquiry_admin_template_settings.admin_header_color}
                        onChangeComplete={(value) => { updateEmailData('enquiry_admin_template_settings', 'admin_header_color', value.hex) }}
                        disableAlpha
                    />
                </div>
            </PanelRow>

            <PanelRow>
                <label>{__('Email Content', 'wp-travel')}</label>
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                <WPEditor id="enquiry_admin_template_settings" value={'undefined' !== typeof enquiry_admin_template_settings.email_content ? enquiry_admin_template_settings.email_content : ''}
                    onContentChange={(value) => {
                        updateEmailData('enquiry_admin_template_settings', 'email_content', value)
                    }} />
            </PanelRow>

        </PanelBody>

    </>
}

