import { TextControl, PanelRow, PanelBody, SelectControl, ToggleControl, CheckboxControl, Notice, Button } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import _ from 'lodash';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
const WPTravelTripPricingCategories = ({priceIndex}) => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { pricing_categories, pricings, default_pricing_id, default_category_id } = allData;

    let defaultPricingId = 'undefined' != typeof default_pricing_id ? default_pricing_id : null
    let defaultCategoryId = 'undefined' != typeof default_category_id ? default_category_id : null
    const { updateTripPricing, updateTripData } = dispatch('WPTravel/TripEdit');
    let price = pricings[priceIndex];
    let pricingCategories = [];
    if ( pricing_categories.length > 0 ) {
        pricing_categories.map((cat)=>{
            pricingCategories = [...pricingCategories,{
                label:cat.title,
                value:cat.id,
            }]
        });
    }

    let addPricingCategory = () => {
        let priceData = price;
        if ( pricing_categories.length > 0 ) {
            priceData.categories = [...priceData.categories, {
                id: pricing_categories[0].id,
                price_per: 'person',
                regular_price: 0,
                is_sale: false,
                sale_price: 0
            }];
            updateTripPricing(priceData, priceIndex)
        }
    }

    let removePricingCategory = (categoryID,priceIndex) => {
        if ( ! confirm( __i18n.alert.remove_category ) ) {
            return false;
        }

        let priceData = price;
        priceData.categories = priceData.categories.filter((cat)=>{
            return categoryID!=cat.id;
        })
        updateTripPricing(priceData, priceIndex);
    }
    
    return <ErrorBoundary>{ 'undefined' !== typeof price && 'undefined' !== typeof price.categories && price.categories.length > 0 ?<>{ price.categories.map((category, catIndex) => {
        let currentCategory = _.find(pricing_categories, function(o) { 
            let catId = category.id>0?category.id:pricing_categories[0].id;
            return o.id === catId; 
        });
        
        return 'undefined' !== typeof currentCategory && <PanelBody
            title={currentCategory.title}
            initialOpen={(price.categories.length - 1 === catIndex)}>
                <PanelRow>
                    <label>{ __i18n.category }</label>
                    {pricing_categories.length>0 && <SelectControl
                        value={ category.id }
                        options={ pricingCategories }
                        onChange={ ( id ) => {
                            let priceData = price;
                            priceData.categories[catIndex].id = Math.abs(id);
                            updateTripPricing(priceData, priceIndex)
                        } }
                    />}    
                </PanelRow>
                <PanelRow>
                    <label>{ __i18n.price_per }</label>
                    <SelectControl
                        value={ category.price_per }
                        options={ [
                            {
                                label: __i18n.person,
                                value:'person'
                            }, {
                                label: __i18n.group,
                                value:'group'
                            }
                        ] }
                        onChange={ ( price_per ) => {
                            let priceData = price;
                            priceData.categories[catIndex].price_per = price_per;
                            updateTripPricing(priceData, priceIndex)
                        } }
                    />
                </PanelRow>
                <PanelRow>
                    <label>{ __i18n.price }</label>
                    <TextControl
                        value={ category.regular_price }
                        onChange={ ( regular_price ) => {
                            let priceData = price;
                            priceData.categories[catIndex].regular_price = regular_price;
                            updateTripPricing(priceData, priceIndex)
                        } }
                    />
                </PanelRow>
                {/* <PanelRow>
                    <label>{ __( 'Set as default price', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                        <ToggleControl
                            checked={ price.id == defaultPricingId && category.id == defaultCategoryId }
                            onChange={ () => {
                                // console.log(' price.id ', price.id )
                                // console.log(' category.id ', category.id )
                                
                            } }
                        />

                        <CheckboxControl
                            label={ __( 'Set as default price', 'wp-travel' ) }
                            checked={ price.id == defaultPricingId && category.id == defaultCategoryId }
                            onChange={ (v) => {
                                if ( v ) {

                                    updateTripData({
                                        ...allData,
                                        default_pricing_id: price.id
                                    })
                                    updateTripData({
                                        ...allData,
                                        default_category_id: category.id
                                    })
                                } else {
                                    updateTripData({
                                        ...allData,
                                        default_pricing_id: null
                                    })
                                    updateTripData({
                                        ...allData,
                                        default_category_id: null
                                    })
                                }
                            } }
                        />
                        <p className="components-form-token-field__help">{__( 'Display as Starting from price', 'wp-travel' )}</p>
                    </div>
                </PanelRow> */}
                <PanelRow>
                    <label>{ __i18n.enable_sale }</label>
                    <ToggleControl
                        checked={ category.is_sale }
                        onChange={ () => {
                            let priceData = price;
                            priceData.categories[catIndex].is_sale = !category.is_sale;
                            updateTripPricing(priceData, priceIndex)
                        } }
                    />
                </PanelRow>
                {category.is_sale&&<PanelRow>
                    <label>{ __i18n.sale_price }</label>
                    <TextControl
                        value={ category.sale_price }
                        onChange={ ( sale_price ) => {
                            let priceData = price;
                            priceData.categories[catIndex].sale_price = sale_price;
                            updateTripPricing(priceData, priceIndex)
                        } }
                    />
                </PanelRow>}
                <PanelRow>
                    <label>{ __i18n.default_pax}</label>
                    <TextControl 
                    value={ category.default_pax }
                    type="number" autoComplete="off" min={0} onChange={ ( default_pax ) => {
                            let priceData = price;
                            priceData.categories[catIndex].default_pax = default_pax;
                            updateTripPricing(priceData, priceIndex)
                        } } />
                </PanelRow>
                {applyFilters('wp_travel_after_pricings_category_ege',[], price, priceIndex, catIndex )}
                {applyFilters('wp_travel_after_pricings_category_fields',[], priceIndex, catIndex )}
                <hr/>
                <PanelRow className="wp-travel-action-section">
                    <span></span>
                    <Button isDefault onClick={() => removePricingCategory(category.id,priceIndex)} className="wp-traval-button-danger" >{ __i18n.remove_category }</Button>
                </PanelRow>
            </PanelBody>})}
            {applyFilters('wp_travel_pricing_option_content_after_category', '', price, priceIndex )}
            </>:<Notice isDismissible={false} actions={[{
                    'label':__i18n.add_category,
                    onClick:()=>{
                        addPricingCategory()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{ __i18n.empty_results.category }</Notice>
    }</ErrorBoundary>
}

export default WPTravelTripPricingCategories;