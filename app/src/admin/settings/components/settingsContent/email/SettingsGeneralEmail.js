import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, TextControl } from '@wordpress/components';
import Tooltip from '../../UI/Tooltip';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        wp_travel_from_email,
    } = allData;
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("General Email Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More email settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <PanelRow>
                    <label>
                        {__('From Email', 'wp-travel')}
                        <Tooltip
                            text={<strong>{__('Preferred to use webmail like: sales@yoursite.com', 'wp-travel')}</strong>}
                        >
                            <span>
                                <i className="fa fa-info-circle" aria-hidden="true"></i>
                            </span>
                        </Tooltip>
                    </label>
                    <div id="wp-travel-general-email-settings-from-email" className="wp-travel-field-value">
                        <TextControl
                            value={wp_travel_from_email}
                            onChange={
                                (value) => {
                                    updateSettings({
                                        ...allData,
                                        wp_travel_from_email: value
                                    })
                                }
                            }
                        />
                        <p className="description"></p>
                    </div>
                </PanelRow>
                {applyFilters('wp_travel_tab_content_before_booking_tamplate', [], allData)}
            </div>
        </>
    );
}

