import { render } from "@wordpress/element";
import domReady from "@wordpress/dom-ready";
import Header from './template/header';
import Body from './template/body';


const WPTravelSetupPage = () => {
       
    return  (
        <div id="wp-travel-setup-page" >
            <Header />
            <Body />
        </div>
    )

}

domReady( () => {
    if (
        "undefined" !== typeof document.getElementById( "wp_travel_setup_page" ) &&
        null !== document.getElementById( "wp_travel_setup_page" )
    ) {
        render( <WPTravelSetupPage />, document.getElementById( "wp_travel_setup_page" ) );
    }
} );
