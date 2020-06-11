import { TextControl, PanelRow, PanelBody, SelectControl, ToggleControl,Notice, Button } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import _ from 'lodash';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';

const WPTravelTripPricingCategories = ({priceIndex}) => {
    const { pricing_categories, pricings } = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripPricing } = dispatch('WPTravel/TripEdit');
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
        if ( ! confirm( __( 'Are you sure to delete category?', 'wp-travel' ) ) ) {
            return false;
        }

        let priceData = price;
        priceData.categories = priceData.categories.filter((cat)=>{
            return categoryID!=cat.id;
        })
        
        // if(false !== price.id){
        //     console.log(price);
        //     apiFetch( { url: `${ajaxurl}?action=wp_travel_remove_pricing_category&pricing_id=${price.id}&category_id=${categoryID}` } ).then( res => {
        //         if( res.success && "WP_TRAVEL_REMOVED_TRIP_PRICING_CATEGORY" === res.data.code){
        //             updateTripPricing(priceData, priceIndex);
        //         }
        //     } );
        // } else {
            updateTripPricing(priceData, priceIndex);
        // }
        
    }
    // console.log(price);
    
    return <>{ 'undefined' !== typeof price && 'undefined' !== typeof price.categories && price.categories.length > 0 ?<>{ price.categories.map((category, catIndex) => {
        let currentCategory = _.find(pricing_categories, function(o) { 
            let catId = category.id>0?category.id:pricing_categories[0].id;
            return o.id === catId; 
        });
        
        return 'undefined' !== typeof currentCategory && <PanelBody
            title={currentCategory.title}
            initialOpen={(price.categories.length - 1 === catIndex)}>
                <PanelRow>
                    <label>{ __( 'Category', 'wp-travel' ) }</label>
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
                    <label>{ __( 'Price Per', 'wp-travel' ) }</label>
                    <SelectControl
                        value={ category.price_per }
                        options={ [
                            {
                                label: __( 'Person', 'wp-travel' ),
                                value:'person'
                            }, {
                                label: __( 'Group', 'wp-travel' ),
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
                    <label>{ __( 'Price', 'wp-travel' ) }</label>
                    <TextControl
                        value={ category.regular_price }
                        onChange={ ( regular_price ) => {
                            let priceData = price;
                            priceData.categories[catIndex].regular_price = regular_price;
                            updateTripPricing(priceData, priceIndex)
                        } }
                    />
                </PanelRow>
                <PanelRow>
                    <label>{ __( 'Enable Sale', 'wp-travel' ) }</label>
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
                    <label>{ __( 'Sale Price', 'wp-travel' ) }</label>
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
                    <label>{ __( 'Default Pax', 'wp-travel' ) }</label>
                    <TextControl 
                    value={ category.default_pax }
                    type="number" autoComplete="off" min={0} onChange={ ( default_pax ) => {
                            let priceData = price;
                            priceData.categories[catIndex].default_pax = default_pax;
                            updateTripPricing(priceData, priceIndex)
                        } } />
                </PanelRow>
                {applyFilters('wp_travel_after_pricings_category_fields',[], priceIndex, catIndex )}
                <hr/>
                <PanelRow className="wp-travel-action-section">
                    <span></span>
                    <Button isDefault onClick={() => removePricingCategory(category.id,priceIndex)} className="wp-traval-button-danger" >{ __( '- Remove Category', 'wp-travel' ) }</Button>
                </PanelRow>
            </PanelBody>})}
            {applyFilters('wp_travel_pricing_option_content_after_category', '', price, priceIndex )}
            </>:<Notice isDismissible={false} actions={[{
                    'label':__( 'Add Category', 'wp-travel' ),
                    onClick:()=>{
                        addPricingCategory()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{ __( 'No categories found.', 'wp-travel' ) }</Notice>
    }</>
}

export default WPTravelTripPricingCategories;