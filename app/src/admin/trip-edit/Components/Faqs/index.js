import { Button, Notice, PanelBody, PanelRow, TextareaControl, TextControl, Disabled } from '@wordpress/components';
import { dispatch } from '@wordpress/data';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

const __i18n = {
	..._wp_travel_admin.strings
}

// Single Components for hook callbacks.
const FaqsNotice = () => {
    return <>
        <Notice isDismissible={false} status="informational">
            <strong>{__i18n.notices.global_faq_option.title}</strong>
            <br />
            {__i18n.notices.global_faq_option.description}
            <br />
            <br />
            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
        </Notice><br />
    </>
}

const Faqs = ({allData}) => {

    const { updateTripData, addNewFaq, updateRequestSending } = dispatch('WPTravel/TripEdit');
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
    // Swap any array or object as per provided index.
    const  swapList = (data, old_index, new_index) => {
        if ( 'object' === typeof data ) {
            if (new_index >= Object.keys(data).length) {
                var k = new_index - Object.keys(data).length + 1;
                while (k--) {
                    data.push(undefined);
                }
            }
            data.splice(new_index, 0, data.splice(old_index, 1)[0]);
        }
        if ( 'array' === typeof data ) {
            if (new_index >= data.length) {
                var k = new_index - data.length + 1;
                while (k--) {
                    data.push(undefined);
                }
            }
            data.splice(new_index, 0, data.splice(old_index, 1)[0]);
        }
        return data;
    };

    // get swap index for FAQ.
    let customSwapIndexes = 'undefined' != typeof faqs && Object.keys(faqs).map(function (faqId) {
        let i = parseInt(faqId);
        let globalFaq = 'yes' ===faqs[i].global

        // Default indexes for global + trip faq
        let upIndex   = i-1;
        let downIndex = i+1;

        if ( 'undefined' != typeof utilities ) {
            if ( 'undefined' != utilities.wp_travel_utils_use_global_faq_for_trip && 'undefined' != utilities.wp_travel_utils_use_trip_faq_for_trip ) {
                if ( 'yes' == utilities.wp_travel_utils_use_global_faq_for_trip ) {
                    if ( 'yes' != utilities.wp_travel_utils_use_trip_faq_for_trip ) {
                        // Filter if only global faq is used
                        // Down index calculation
                        let downIndexes = Object.keys(faqs).filter(function (tempFaqId) {
                            let j = parseInt(tempFaqId);
                            let tempGglobalFaq = 'yes' === faqs[j].global
                            if ( globalFaq ) {
                                return tempGglobalFaq && i < j
                            } else {
                                return ! tempGglobalFaq && i < j
                            }
                        })
                        downIndex = 'undefined' != typeof downIndexes && 'undefined' != typeof downIndexes[0] ? parseInt( downIndexes[0] ) : downIndex;

                        // Up index calculation
                        let upIndexes = Object.keys(faqs).filter(function (tempFaqId) {
                            let j = parseInt(tempFaqId);
                            let tempGglobalFaq = 'yes' === faqs[j].global
                            if ( globalFaq ) {
                                return tempGglobalFaq && i > j
                            } else {
                                return ! tempGglobalFaq && i > j
                            }
                        })
                        let len = upIndexes.length;
                        upIndex = 'undefined' != typeof upIndexes && 'undefined' != typeof upIndexes[len-1] ? parseInt( upIndexes[len-1] ) : upIndex;
                    }
                } else {
                    // Filter if only trip faq is used

                    // Down index calculation
                    let downIndexes = Object.keys(faqs).filter(function (tempFaqId) {
                        let j = parseInt(tempFaqId);
                        let tempGglobalFaq = 'yes' === faqs[j].global
               
                        return ! tempGglobalFaq && i < j
                        
                    })
                    downIndex = 'undefined' != typeof downIndexes && 'undefined' != typeof downIndexes[0] ? parseInt( downIndexes[0] ) : downIndex;

                    // Up index calculation
                    let upIndexes = Object.keys(faqs).filter(function (tempFaqId) {
                        let j = parseInt(tempFaqId);
                        let tempGglobalFaq = 'yes' === faqs[j].global

                        return ! tempGglobalFaq && i > j
                      
                    })
                    let len = upIndexes.length;
                    upIndex = 'undefined' != typeof upIndexes && 'undefined' != typeof upIndexes[len-1] ? parseInt( upIndexes[len-1] ) : upIndex;

                }
            }
        }
        return {index:i, upIndex: upIndex, downIndex: downIndex, global:globalFaq }
    })


    let startIndex = 0;
    let endIndex   = typeof faqs != 'undefined' &&  Object.keys(faqs).length > 0 ? parseInt( Object.keys(faqs).length -1 ) : 0;

    if ( 'undefined' != typeof utilities ) {
        if ( 'undefined' != utilities.wp_travel_utils_use_global_faq_for_trip && 'undefined' != utilities.wp_travel_utils_use_trip_faq_for_trip ) {
            if ( 'yes' == utilities.wp_travel_utils_use_global_faq_for_trip ) {
                if ( 'yes' != utilities.wp_travel_utils_use_trip_faq_for_trip ) {
                    // Filter if only global faq is used.
                    let indexes = Object.keys(faqs).filter(function (tempFaqId) {
                            return 'yes' === faqs[tempFaqId].global
                        });
                    if ( 'undefined' != typeof indexes && indexes.length > 0 ) {
                        startIndex = parseInt(indexes[0]);
                        endIndex = parseInt(indexes[ indexes.length - 1 ]);
                    }

                }
            } else {
                // Filter if only trip faq is used
                let indexes = Object.keys(faqs).filter(function (tempFaqId) {
                    return 'yes' !== faqs[tempFaqId].global
                });
                if ( 'undefined' != typeof indexes && indexes.length > 0 ) {
                    startIndex = parseInt(indexes[0]);
                    endIndex = parseInt(indexes[ indexes.length - 1 ]);
                }

            }


        }
    }
    return <ErrorBoundary key="faqsLists">
        <div className="wp-travel-trip-faq">
            {applyFilters('wp_travel_trip_faq_tab_content', '', allData)}

            {typeof faqs != 'undefined' &&  Object.keys(faqs).length > 0 ?
            <>
                {}
                <PanelRow className="wp-travel-action-section"><span></span><Button variant="secondary" onClick={() => addFaq()}>{__i18n.add_faq}</Button></PanelRow>
                <div className="wp-travel-sortable-component">
                    <ReactSortable
                        list={faqs}
                        setList={sortedFaqs => sortFaqs( sortedFaqs)}
                        handle=".components-panel__icon"
                    >
                    {
                        Object.keys(faqs).map(function (faqId) {
                            let index     = parseInt(faqId);
                            let upIndex   = index - 1; // swip item up in the list
                            let downIndex = index + 1; // swip item down in the list

                            upIndex = 'undefined' != typeof customSwapIndexes && 'undefined' != typeof customSwapIndexes[index] && 'undefined' != typeof customSwapIndexes[index].upIndex ? customSwapIndexes[index].upIndex : upIndex
                            downIndex = 'undefined' != typeof customSwapIndexes && 'undefined' != typeof customSwapIndexes[index] && 'undefined' != typeof customSwapIndexes[index].downIndex ? customSwapIndexes[index].downIndex : downIndex

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

                            if ( 'yes' ===faqs[faqId].global ) {
                                return <div style={{position:'relative'}}  data-index={index} key={index} >
                                    <div className={`wptravel-swap-list ${hiddenClass}`}>
                                    <Button
                                    disabled={startIndex == index}
                                    onClick={(e) => {
                                        let sorted = swapList( faqs, index, upIndex )
                                        sortFaqs(sorted)
                                        updateRequestSending(true); // Temp fixes to reload the content.
                                        updateRequestSending(false);
                                    }}><i className="dashicons dashicons-arrow-up"></i></Button>
                                    <Button
                                    disabled={endIndex == index}
                                    onClick={(e) => {
                                        let sorted = swapList( faqs, index, downIndex )
                                        sortFaqs(sorted)
                                        updateRequestSending(true);
                                        updateRequestSending(false);
                                    }}><i className="dashicons dashicons-arrow-down"></i></Button>
                                </div>
                                        <PanelBody  className={hiddenClass}
                                        icon= {alignJustify}
                                        title={`${faqs[faqId].question ? faqs[faqId].question : __i18n.faq_questions}`}
                                        initialOpen={ ( Object.keys(faqs).length - 1 ) === parseInt(index) } >
                                        <Disabled>
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
                                        </Disabled>

                                    </PanelBody>
                                </div>
                            }
                            return <div style={{position:'relative'}} data-index={index} key={index}>
                                <div className={`wptravel-swap-list ${hiddenClass}`} >
                                    <Button
                                    disabled={startIndex == index}
                                    onClick={(e) => {
                                        let sorted = swapList( faqs, index, upIndex )
                                        sortFaqs(sorted)
                                        updateRequestSending(true); // Temp fixes to reload the content.
                                        updateRequestSending(false);
                                    }}><i className="dashicons dashicons-arrow-up"></i></Button>
                                    <Button
                                    disabled={endIndex == index}
                                    onClick={(e) => {
                                        let sorted = swapList( faqs, index, downIndex )
                                        sortFaqs(sorted)
                                        updateRequestSending(true);
                                        updateRequestSending(false);
                                    }}><i className="dashicons dashicons-arrow-down"></i></Button>
                                </div>
                                <PanelBody  className={hiddenClass}
                                    icon= {alignJustify}
                                    title={`${faqs[faqId].question ? faqs[faqId].question : __i18n.faq_questions}`}
                                    initialOpen={ ( Object.keys(faqs).length - 1 ) === parseInt(index) } >

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
                                        <span></span><Button variant="secondary" onClick={() => {
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
                            </div>
                        })
                    }
                    </ReactSortable>

                </div>
                { Object.keys(faqs).length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button variant="secondary" onClick={() => addFaq()}>{__i18n.add_faq}</Button></PanelRow> }
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

// Callbacks.
const FaqsNoticeCB = ( content ) => {
    return [ ...content, <FaqsNotice key="FaqsNotice" /> ];
}
const FaqsCB = ( content, allData ) => {
    return [ ...content, <Faqs allData={allData} key="Faqs" /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_faqs', 'WPTravel/TripEdit/FaqsNotice', FaqsNoticeCB, 10 );
addFilter( 'wptravel_trip_edit_tab_content_faqs', 'WPTravel/TripEdit/Faqs', FaqsCB, 20 );

addFilter( 'wptravel_trip_edit_block_tab_faq', 'WPTravel/TripEdit/Block/FAQ/FaqsNotice', FaqsNoticeCB );
addFilter( 'wptravel_trip_edit_block_tab_faq', 'WPTravel/TripEdit/Block/FAQ/Faqs', FaqsCB );
