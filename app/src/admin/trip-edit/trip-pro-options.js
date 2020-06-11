import {SelectControl,PanelRow, ToggleControl, TextControl,Button, IconButton,Notice } from '@wordpress/components';
import {  addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { sprintf, _n, __} from '@wordpress/i18n';

// import './trip-store';

const PricingOptions = () => {
    const {pricing_type} = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { setTripPricingType } = dispatch('WPTravel/TripEdit');
    
    return <><PanelRow>
        
        <label>{ __( 'Pricing Option', 'wp-travel' ) }</label>
        <SelectControl
            value={ pricing_type }
            options={ [
                { label: __( 'Multiple Price', 'wp-travel' ), value: 'multiple-price' },
                { label: __( 'Custom Booking', 'wp-travel' ), value: 'custom-booking' },
            ] }
            onChange={ ( type ) => { setTripPricingType( type ) } }
        />
    </PanelRow>
    <hr/>
    </>
}

addFilter('WPTravelBeforePricingsOptions','WPTravel',()=>{
    return <PricingOptions />;
});


const PricingCategoryGroupDiscount = ({priceIndex,catIndex}) => {
    const {pricings} = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripPricing } = dispatch('WPTravel/TripEdit');
    
    let price = pricings[priceIndex];
    let category = pricings[priceIndex].categories[catIndex];
    return <>
    <hr/>
    <PanelRow>
                <label>{ __( 'Enable Group Discount', 'wp-travel' ) }</label>
                <ToggleControl
                    checked={ category.has_group_discount }
                    onChange={ () => {
                        let priceData = price;
                        priceData.categories[catIndex].has_group_discount = !category.has_group_discount;
                        updateTripPricing(priceData, priceIndex)
                    } }
                />
        </PanelRow>
        {category.has_group_discount&&<><PanelRow>
                <label>{ __( 'Group Discounts', 'wp-travel' ) }</label>
        </PanelRow>
        <PanelRow>
                <table className="wp-list-table widefat  striped ">
                <thead>
                        <tr>
                            <td>{ __( 'Min Pax', 'wp-travel' ) }</td>
                            <td>{ __( 'Max Pax', 'wp-travel' ) }</td>
                            <td>{ __( 'Price', 'wp-travel' ) }</td>
                            <td></td>
                        </tr>
                        </thead>
                    <tbody>
                        <tr>
                            <td><TextControl /></td>
                            <td><TextControl /></td>
                            <td><TextControl /></td>
                            <td>{ __( 'Remove', 'wp-travel' ) }</td>
                        </tr>
                        <tr>
                            <td><TextControl /></td>
                            <td><TextControl /></td>
                            <td><TextControl /></td>
                            <td>{ __( 'Remove', 'wp-travel' ) }</td>
                        </tr>
                    </tbody>
                </table>
        </PanelRow>
        <PanelRow className="wp-travel-action-section has-right-padding">
            <span></span>
                            <Button isDefault>{ __( '+ Discount', 'wp-travel' ) }</Button>
                        </PanelRow></>}
        </>
}

addFilter('WPTravelAfterPricingsCategoryFields','WPTravel',(content,priceIndex, catIndex)=><PricingCategoryGroupDiscount priceIndex={priceIndex} catIndex={catIndex} />)

const PricingGroupPrice = ({priceIndex}) => {
    const {pricings} = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripPricing } = dispatch('WPTravel/TripEdit');

    let price = pricings[priceIndex];
    let has_group_price = 'undefined' === typeof price.has_group_price ? false : price.has_group_price;
    let group_prices = 'undefined' === typeof price.group_prices ? [] : price.group_prices;
    let addGroupPrice = ()=>{
        let priceData = price;
        priceData.group_prices = [...group_prices, {
            min_pax:'',
            max_pax:'',
            price:''
        }];
        updateTripPricing(priceData, priceIndex)
    }
    return <>
    <hr/>
    <PanelRow>
                <div><label>{ __( 'Enable Group Price', 'wp-travel' ) }</label><p className="description">{ __( 'This pricing will affect all categories.', 'wp-travel' ) }</p></div>
                <ToggleControl
                    checked={ price.has_group_price }
                    onChange={ () => {
                        let priceData = price;
                        priceData.has_group_price = !price.has_group_price;
                        updateTripPricing(priceData, priceIndex)
                    } }
                />
        </PanelRow>

        { has_group_price && <><PanelRow className="wp-travel-has-child-panel">
                <label>{ __( 'Group Prices', 'wp-travel' ) }</label>
       
                {group_prices.length > 0 ?<table className="wp-list-table widefat  striped ">
                <thead>
                        <tr>
                            <td>{ __( 'Min Pax', 'wp-travel' ) }</td>
                            <td>{ __( 'Max Pax', 'wp-travel' ) }</td>
                            <td>{ __( 'Price', 'wp-travel' ) }</td>
                            <td></td>
                        </tr>
                        </thead>
                    <tbody>
                        { group_prices.length > 0 ? group_prices.map((groupPrice, groupPriceIndex)=><tr>
                            <td><TextControl
                                type="number"
                                min="0"
                                autoComplete="off"
                                value={groupPrice.min_pax} onChange={(min_pax)=>{
                                let priceData = price;
                                priceData.group_prices[groupPriceIndex].min_pax = min_pax
                                updateTripPricing(priceData, priceIndex);
                            }}/></td>
                            <td><TextControl
                                type="number"
                                min="0"
                                autoComplete="off"
                                value={groupPrice.max_pax} onChange={(max_pax)=>{
                                let priceData = price;
                                priceData.group_prices[groupPriceIndex].max_pax = max_pax
                                updateTripPricing(priceData, priceIndex);
                            }} /></td>
                            <td><TextControl value={groupPrice.price}  onChange={(gprice)=>{
                                let priceData = price;
                                priceData.group_prices[groupPriceIndex].price = gprice
                                updateTripPricing(priceData, priceIndex);
                            }}/></td>
                            <td><IconButton
                                icon="trash"
                                label="Delete"
                                onClick={()=>{
                                    if( !confirm( __( 'Are you sure to delete group price?', 'wp-travel' ) )){
                                        return false;
                                    }
                                    let priceData = price;
                                    priceData.group_prices = group_prices.filter((groupPriceFilter,groupPriceFilterIndex)=>{
                                        return groupPriceIndex!=groupPriceFilterIndex;
                                    });
                                    
                                    updateTripPricing(priceData, priceIndex)
                                }}
                            /></td>
                        </tr>):<tr><td colSpan="4">{ __( 'No Group Price.', 'wp-travel' ) }</td></tr>}
                        
                    </tbody>
                </table>:<Notice isDismissible={false} actions={[{
                    'label': __( 'Add Group Price', 'wp-travel' ),
                    onClick:()=>{
                        addGroupPrice()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{ __( 'No Group Price found.', 'wp-travel' ) }</Notice>}
        </PanelRow>
        {group_prices.length > 0 &&<PanelRow className="wp-travel-action-section has-right-padding">
            <span></span>
                            <Button isDefault onClick={()=>addGroupPrice()}>{ __( '+ Add Group Price', 'wp-travel' ) }+ Add Group Price</Button>
                            
            </PanelRow>} </>}
        </>
}
addFilter('WPTravelAfterPricingsFields', 'WPTravel', ( content, priceIndex )=><PricingGroupPrice priceIndex={priceIndex} />);