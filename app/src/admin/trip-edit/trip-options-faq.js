import { Button, Notice, PanelBody, PanelRow, TabPanel, TextareaControl, TextControl } from '@wordpress/components';
import { dispatch, useSelect } from '@wordpress/data';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

const WPTravelTripOptionsFaqContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData, addNewFaq } = dispatch('WPTravel/TripEdit');
    const { faqs, utilities } = allData;
    
    const updateTripFaqs = (key, value, _faqId) => {

        const { faqs } = allData;

        let _allFaqs = faqs;
        _allFaqs[_faqId][key] = value

        updateTripData({
            ...allData,
            faqs: [ ..._allFaqs ]
        })
    }

    const addFaq = () => {
        addNewFaq({
            question: __('', 'wp-travel'),
            answer: __('', 'wp-travel'),
            global: 'no',

        })
    }
    const updateFaqs = (allFaqs) => { // Remove FAQs
        updateTripData({
            ...allData,
            faqs: [ ...allFaqs ]
        })
    }
    // Final Store Dispatcher.
    const sortFaqs = ( sortedFaqs ) => {
        updateTripData({
            ...allData, // allData
            faqs: sortedFaqs
        })
    }
    return <ErrorBoundary>
        <div className="wp-travel-trip-faq">
            {applyFilters('wp_travel_trip_faq_tab_content', '', allData)}
            
            {typeof faqs != 'undefined' &&  Object.keys(faqs).length > 0 ? <>
            <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFaq()}>{__('+ Add FAQ')}</Button></PanelRow>
            <div className="wp-travel-sortable-component">
                <ReactSortable
                    list={faqs}
                    setList={sortedFaqs => sortFaqs( sortedFaqs)}
                    handle=".components-panel__icon"
                >
                {
                    Object.keys(faqs).map(function (faqId) {
                        let hiddenClass = ''
                        if ( 'undefined' != typeof utilities ) {
                            if ( 'undefined' != utilities.wp_travel_utils_use_global_faq_for_trip && 'undefined' != utilities.wp_travel_utils_use_trip_faq_for_trip ) {
                                if ( 'yes' == utilities.wp_travel_utils_use_global_faq_for_trip ) {
                                    if ( 'yes' != utilities.wp_travel_utils_use_trip_faq_for_trip ) {
                                        if ( faqs[faqId].global != 'yes' ) {
                                            hiddenClass = 'hidden'
                                        }
                                    }
                                } else {
                                    if ( faqs[faqId].global == 'yes' ) {
                                        hiddenClass = 'hidden'
                                    }
                                }
                            } 
                        }
                        return <PanelBody  className={hiddenClass}
                            icon= {alignJustify}
                            title={`${faqs[faqId].question ? faqs[faqId].question : __('FAQ Questions ?', 'wp-travel')}`}
                            initialOpen={false} >

                            <PanelRow>
                                <label>{__('Enter your question', 'wp-travel')}</label>
                                <TextControl
                                    placeholder={__('FAQ Questions ?', 'wp-travel')}
                                    value={faqs[faqId].question ? faqs[faqId].question : ''}
                                    onChange={
                                        (e) => updateTripFaqs('question', e, faqId)
                                    }
                                />
                            </PanelRow>

                            <PanelRow>
                                <label>{__('Your Answer', 'wp-travel')}</label>
                                <TextareaControl
                                    value={faqs[faqId].answer ? faqs[faqId].answer : null}
                                    onChange={
                                        (e) => updateTripFaqs('answer', e, faqId)
                                    }
                                />
                            </PanelRow>
                            <PanelRow className="wp-travel-action-section has-right-padding">
                                <span></span><Button isDefault onClick={() => {
                                    if (!confirm( __( 'Are you sure to delete FAQ?', 'wp-travel' ) )) {
                                        return false;
                                    }
                                    let faqData = [];
                                    faqData = faqs.filter((faq, newFaqId) => {
                                        return newFaqId != faqId;
                                    });
                                    updateFaqs(faqData);
                                }} className="wp-traval-button-danger">{__('- Remove FAQ', 'wp-travel')}</Button>
                            </PanelRow>

                        </PanelBody>
                    })
                }
                </ReactSortable>

            </div>
            { Object.keys(faqs).length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFaq()}>{__('+ Add FAQ')}</Button></PanelRow> }
            </> : 
            <>
                <Notice isDismissible={false} actions={[{
                    'label': __( 'Add FAQ', 'wp-travel' ),
                    onClick: () => {
                        addFaq()
                    },
                    noDefaultClasses: true,
                    className: 'is-link'
                }]}>{ __( 'Please add new FAQ here.', 'wp-travel' ) }</Notice></>
            }
        </div>
    </ErrorBoundary>;
}
addFilter('wp_travel_trip_faq_tab_content', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Tired of updating repitative FAQs ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can create and use Global FAQs in all of your trips !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
        &nbsp;&nbsp;
        <a className="button button-primary" target="_blank" href="https://wptravel.io/downloads/wp-travel-utilities/">{__('Get WP Travel Utilities Addon', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
}, 9);

const WPTravelTripOptionsFaq = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsFaqContent /></div>
}

export default WPTravelTripOptionsFaq;