import { Button, Notice, PanelBody, PanelRow, TabPanel, TextareaControl, TextControl } from '@wordpress/components';
import { dispatch, useSelect } from '@wordpress/data';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

const __i18n = {
	..._wp_travel_admin.strings
}

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
            <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFaq()}>{__i18n.add_faq}</Button></PanelRow>
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
                            title={`${faqs[faqId].question ? faqs[faqId].question : __i18n.faq_questions}`}
                            initialOpen={false} >

                            <PanelRow>
                                <label>{__i18n.enter_question}</label>
                                <TextControl
                                    placeholder={__i18n.faq_questions}
                                    value={faqs[faqId].question ? faqs[faqId].question : ''}
                                    onChange={
                                        (e) => updateTripFaqs('question', e, faqId)
                                    }
                                />
                            </PanelRow>

                            <PanelRow>
                                <label>{__i18n.faq_answer}</label>
                                <TextareaControl
                                    value={faqs[faqId].answer ? faqs[faqId].answer : null}
                                    onChange={
                                        (e) => updateTripFaqs('answer', e, faqId)
                                    }
                                />
                            </PanelRow>
                            <PanelRow className="wp-travel-action-section has-right-padding">
                                <span></span><Button isDefault onClick={() => {
                                    if (!confirm( __i18n.alert.remove_faq)) {
                                        return false;
                                    }
                                    let faqData = [];
                                    faqData = faqs.filter((faq, newFaqId) => {
                                        return newFaqId != faqId;
                                    });
                                    updateFaqs(faqData);
                                }} className="wp-traval-button-danger">{__i18n.remove_faq}</Button>
                            </PanelRow>

                        </PanelBody>
                    })
                }
                </ReactSortable>

            </div>
            { Object.keys(faqs).length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFaq()}>{__i18n.add_faq}</Button></PanelRow> }
            </> : 
            <>
                <Notice isDismissible={false} actions={[{
                    'label': __i18n.add_faq,
                    onClick: () => {
                        addFaq()
                    },
                    noDefaultClasses: true,
                    className: 'is-link'
                }]}>{ __i18n.add_new_faq }</Notice></>
            }
        </div>
    </ErrorBoundary>;
}
addFilter('wp_travel_trip_faq_tab_content', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__i18n.notices.global_faq_option.title}</strong>
                <br />
                {__i18n.notices.global_faq_option.description}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
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