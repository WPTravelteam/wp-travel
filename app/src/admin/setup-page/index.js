import { render } from "@wordpress/element";
import domReady from "@wordpress/dom-ready";
import Header from './template/header';
import Body from './template/body';
import { useSelect, select, dispatch } from '@wordpress/data'; 


const WPTravelSetupPage = () => {

    const settingsData = useSelect((select) => {
        return select('WPTravel/Admin').getSettings()
    }, []);
    
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    
    const {  sorted_gateways, options} = allData
   
    return  (
        <div id="wp-travel-setup-page" >
            <Header />
            <Body />
        </div>
    )

}


{/* 
    rendering template to target id
*/} 
domReady( () => {
    if (
        "undefined" !== typeof document.getElementById( "wp_travel_setup_page" ) &&
        null !== document.getElementById( "wp_travel_setup_page" )
    ) {
        render( <WPTravelSetupPage />, document.getElementById( "wp_travel_setup_page" ) );
    }
} );
