import changeLog from '../../change-log'
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

const FinishedTab = () => {

	function openCity( tabName ) {

		if ( tabName == 'plugin-features' ) {
			document.getElementById("plugin-features-tab").classList.add( 'active' );
			document.getElementById("change-log-tab").classList.remove( 'active' );
			document.getElementById("Comparision-tab").classList.remove( 'active' );

			document.getElementById("plugin-features").classList.add( 'active' );
			document.getElementById("change-log").classList.remove( 'active' );
			document.getElementById("Comparision").classList.remove( 'active' );
		}

		if ( tabName == 'Comparision' ) {
			document.getElementById("plugin-features-tab").classList.remove( 'active' );
			document.getElementById("change-log-tab").classList.remove( 'active' );
			document.getElementById("Comparision-tab").classList.add( 'active' );

			document.getElementById("plugin-features").classList.remove( 'active' );
			document.getElementById("change-log").classList.remove( 'active' );
			document.getElementById("Comparision").classList.add( 'active' );
		}

		if ( tabName == 'change-log' ) {
			document.getElementById("plugin-features-tab").classList.remove( 'active' );
			document.getElementById("change-log-tab").classList.add( 'active' );
			document.getElementById("Comparision-tab").classList.remove( 'active' );

			document.getElementById("plugin-features").classList.remove( 'active' );
			document.getElementById("change-log").classList.add( 'active' );
			document.getElementById("Comparision").classList.remove( 'active' );
		}
	   
	}

	const importTrip = () => {

		document.getElementById("trip-import-loader").classList.add( 'active' );
    	document.getElementById("finished-tab-content").classList.add( 'inactive' );

    	apiFetch( { path: '/wp-travel/v1/trip-import/', method: 'POST' } ).then( ( response ) => {
    		
		    location.replace( _wp_travel.admin_url + 'edit.php?post_type=itineraries' );
		} );
    }

	return (

		<div id="finished-tab-content">
			
		    <section className="wptravel-tabs">
			    <div className="wptravel-wrapper">

			        <div className="wptravel-tab">
			            <button id="plugin-features-tab" className="tablinks active" onClick={ () => { openCity( 'plugin-features')} } >{__('Plugin Features', 'wp-travel')}</button>
			            <button id="Comparision-tab" className="tablinks" onClick={ () => { openCity( 'Comparision') } }>{__('Compare Free & Pro', 'wp-travel')}</button>
			            <button id="change-log-tab" className="tablinks" onClick={ () => { openCity( 'change-log')} }>{__('Change Log', 'wp-travel')}</button>
			        </div>

			        <div id="plugin-features" className="tabcontent active">
			            <section className="wtravel-features">
			                <div className="wptravel-wrapper">
			                    <h2 id="wptravel-h2-title"><strong>{__('WP Travel', 'wp-travel')}</strong> {__('Features', 'wp-travel')}</h2>
			                    <div className="grid-container">
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/travel-site-ready.png' }/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Travel Site Ready', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Get your travel site ready just on few clicks. With our user-friendly system & complete documentation, you wont have any trouble while using the system.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/booking.png' } alt="booking"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Booking', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('WP Travel includes in-build booking system for your travel site. Users can easily book itineraries from your site and you can track all bookings from the backend.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/full-data.png' } alt="full-data"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Full Data Reporting', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Data are very important for all business. WP Travel has in-build booking stat that helps you to generate the report from different date range, types and locations.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/payment-process.png' } alt="payment-process"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Payment Processing', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('With our payment processing features, you can get partial or full payment for each booking. All that payment will be tracked in the backend and also you can view stat of payments.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/translate.png' } alt="translate"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Translation Ready', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('WP travel plugin is translation ready in order to fulfill customers needs from all around the world. You can translate WP Travel to any language with the help of WPML Translation Plugin and for the translation of the string. you can use Loco Translate.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/faq.png' } alt="faq"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('FAQs and Itinerary Timeline', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Data are very important for all business. WP Travel has in-build booking stat that helps you to generate the report from different date range, types and locations.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/multiple-pricing.png' } alt="multiple-pricing"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Multiple Pricing and Multiple dates', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Multiple pricing features add a new level to the plugin where trips can be offered at multiple prices and dates for different groups of customer.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/tax.png' } alt="tax"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Tax option', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('WP Travel plugin now is available with the tax option where the user will be able to allocate the tax to the trip both inclusively and exclusively.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/trip-fact.png' } alt="trip-fact"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Trip Fact', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Trip facts will provide a practical travel information which helps you choose the perfect tour and helps you planning ahead. ', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/login-dashboard.png' } alt="wptravel login dashboard"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('WP Travel Login Dashboard', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('WP Travel Login Dashboard', 'wp-travel')}WP travel login dashboard feature introduced with the version (1.3.7) allows the user to create their own dashboard. The dashboard displays the list of booked tours along with other informations.</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/coupon.png' } alt="coupon"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Coupon', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Coupons are a great way to offer discounts which help in implementing a promotion for your Trips. With WP Travel coupon, a percentage or fixed amount of coupons are applied to the trips.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/google-map.png' } alt="google-map"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Google Maps zoom level setting', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('This is minimal but a special feature which is added with the WP Travel version(1.4.2). This feature helps you to manage the zooming level for the map.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/tour-extras.png' } alt="tour-extras"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('WP Travel Tour Extras', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('WP travel Tour Extras allows you to add additional services that the particular trips offer. It helps you add services like bottles, Air tickets etc.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/development.png' } alt="continues development"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Continues Development', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('Our team is dedicated to continuous development of the plugin. We will be continuously adding new features to the plugin.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                        <div className="grid-item wptravel-col33">
			                            <div className="wptravel-inside-wrapper">
			                                <div className="box-wrapper">
			                                    <figure className="logo">
			                                        <img src={ _wp_travel.plugin_url + 'assets/images/support.png' } alt="support"/>
			                                    </figure>
			                                    <div className="wp-image-box-content">
			                                        <h3 className="wp-image-box-title">{__('Support', 'wp-travel')}</h3>
			                                        <p className="wp-image-box-description">{__('If you found any issues in the plugin, you can directly contact us or add your issues or problems on support forum.', 'wp-travel')}</p>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>

			                    <div className="wptravel-section-title">
			                        <h2 id="wptravel-h2-title"><strong>{__('Advanced Modules', 'wp-travel')}</strong></h2>
			                        <p>{__('WP Travel Pro is packed with advanced modules that makes your travel site even more powerful. You can easily get the best out of your business with these amazing features.', 'wp-travel')}</p>
			                    </div>
			                    <div className="wp-pro-modules">
			                        <div className="grid-container">
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('WP Travel Zapier', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Create zapier automation for WP Travel bookings and enquiries.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('WP Travel Advanced Gallery', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Add more informative gallery items - the audio, video and images.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Group Discount', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Give discounts on groups i.e. price based on number of people.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Field Editor', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Create new custom fields in your default forms like enquiry forms.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Utilities', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Enhance the features of your travel booking site even further.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Tour Extras', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Add additional services to your trip and tour packages.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Partial Payment', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Partial Payment', 'wp-travel')}Allow your customers to pay partial fees of their trips or tours.</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Downloads', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Allow your visitors to download trip materials with just one click.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Custom Filters', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Allow users to create custom filters with various search variables.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                            <h3 className="wp-image-box-title">{__('MailChimp', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Integrate your travel site with MailChimp’s powerful email marketing.', 'wp-travel')}</p>				                                        
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Wishlist', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Allow your users to bookmark their favorite trips for future purchase.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Rest API', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Integrate your travel site with many services with Rest API', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Google Calendar', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Integrate your travel site with Google Calendar.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>

			                    <div className="wptravel-section-title">
			                        <h2 id="wptravel-h2-title"><strong>{__('Payment Modules', 'wp-travel')}</strong></h2>
			                    </div>
			                    <div className="wp-pro-modules">
			                        <div className="grid-container">
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Paypal Express Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Paypal Express Checkout', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Stripe Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Stripe Checkout.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Authorize.Net Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Authorize.Net', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Paystack Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Paystack.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('PayFast Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using PayFast', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('PayU Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using PayU', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Instamojo Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Instamojo', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Razorpay Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Razorpay', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Khalti Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Khati Checkout', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('PayU Latam Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using PayU Latam', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('PayHere Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using PayHere.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Squareup Checkout', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Let your users pay for their trips using Squareup.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>

			                    <div className="wptravel-section-title">
			                        <h2 id="wptravel-h2-title"><strong>{__('Maps', 'wp-travel')}</strong></h2>
			                    </div>
			                    <div className="wp-maps">
			                        <div className="grid-container">
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Here Map', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Provide beautiful and interactive Map on your travel site', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <h3 className="wp-image-box-title">{__('Mapquest', 'wp-travel')}</h3>
			                                            <p className="wp-image-box-description">{__('Show your business location or help users to get travel directions.', 'wp-travel')}</p>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>

			                    <div className="wptravel-section-title">
			                        <h2 id="wptravel-h2-title"><strong>{__('Others Features', 'wp-travel')}</strong></h2>
			                        <p>{__(' WP Travel Pro has many other features that would help you to build a world class travel booking site in minutes without any additional custom development!', 'wp-travel')}
			                           
			                        </p>
			                    </div>
			                    <div className="wptravel-other-feat">
			                        <div className="grid-container">

			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Trip Search / Filter Shortcode', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Trip Search / Filter Widget', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Sale widget added.', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/purple.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Pricing per person & group', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('New Cart Page', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/darkgreen.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('New Checkout Page', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Itineraries filters', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/teal.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Enquiry Form added', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green1.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Frontend tab level and sorting', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue1.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Payment field added globally', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Google Map', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Trip Facts', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/darkgreen.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Multiple Currency', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Weather forecast', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/purple.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Additional field', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Import export', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Inventory options', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/pink.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Multiple email recipients', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Multiple pricing checkout', 'wp-travel')}​</h3>
			                                                </div>
			                                            </div>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>

			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red1.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Recurring Dates', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/darkgreen.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Image Gallery', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Sale price', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/pink.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Trip details & outlines', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Trips includes and excludes', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/darkgreen.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Set fix departures', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Rating & Reviews', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Trip Types', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/purple.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Trip Locations', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/darkgreen.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Keywords', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('search', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/pink.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Coupon', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Geo-Location', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/teal.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Downloads option', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Additional service', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue1.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Wishlist', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green1.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('FAQs', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Zapier automation', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/purple.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Google Calendar', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>

			                            <div className="grid-item wptravel-col33">
			                                <div className="wptravel-inside-wrapper">
			                                    <div className="box-wrapper">
			                                        <div className="wp-image-box-content">
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/pink.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Cut off time', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Related Itineraries', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Group size', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Additional Widgets', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/purple.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Trip Code', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Compare Price on Stat', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Fluid Layout, Responsive', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Customizable', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/darkgreen.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Complete documentation', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/pink.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Compatible Themes', 'wp-travel')}
			                                                    </h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Activity taxonomy Added', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/red.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Multiple travelers form', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Invoice', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/purple.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Email Option', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/pink.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Custom tabs', 'wp-travel')}​</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/blue.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">{__('Custom filters', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/green.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Multiple Cart', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                            <div className="wptravel-image-box-wrapper">
			                                                <figure className="wp-image-box-rectangle">
			                                                    <img className="wptravel-rectange-png" src={ _wp_travel.plugin_url + 'assets/images/yellow.png' } width="31" height="31"/>
			                                                </figure>
			                                                <div className="wptravel-box-content">
			                                                    <h3 className="wp-rect-title">
			                                                        {__('Itinerary Downloader', 'wp-travel')}</h3>
			                                                </div>
			                                            </div>
			                                        </div>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </section>
			        </div>

			        <div id="Comparision" className="tabcontent">
			            <section className="wptravel-compare-free-pro">
			                <div className="wptravel-wrapper">
			                    <div className="compare-table">
			                        <h3 id="wptravel-h3-title">{__('Comparision between', 'wp-travel')} <strong>{__('WP Travel free', 'wp-travel')}</strong> {__('and', 'wp-travel')} <strong>{__('WP Travel Pro', 'wp-travel')}</strong> {__('version', 'wp-travel')}</h3>
			                        <div className="wp-tab-content">
			                            <div className="wp-compare-table">
			                                <div className="wp-compare">
			                                    <article id="free-pro" className="wptravel-free-pro">		     
			                                        <h4>{__('PRO FEATURES', 'wp-travel')}</h4>
			                                    </article>
			                                    <article id="free-pro" className="wptravel-free-pro">
			                                        <h4>{__('FREE FEATURES', 'wp-travel')}</h4>
			                                    </article>
			                                    <span className="wp-compare-logo">
			                                        {__('VS', 'wp-travel')}
			                                    </span>
			                                </div>
			                            </div>
			                        
			                            <table>
			                                <tbody>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Trip Booking', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Multiple Pricing & Dates', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Advance Gallery', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Advance Tour Extras', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Advance Partial Payment', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Google Map', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Standard PayPal', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Basic options', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Group Discount', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Downloads', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Import Export', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Field Editor', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Custom Filters', 'wp-travel')} 
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Wishlists', 'wp-travel')} 
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Currency Exchange Rates', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Trip Weather', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Zapier', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('PayPal Express Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Stripe Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Authorize.Net Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Paystack Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('PayHere Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('PauY Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Instamojo Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Razorpay Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Squareup Checkout', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Here Map', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('MailChimp', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Itinerary PDF Downloads', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Export to Google Calender', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Utilities', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                    <tr>
			                                        <td>
			                                            <i class="fa fa-check" aria-hidden="true"></i>
			                                        </td>
			                                        <td>
			                                            {__('Support', 'wp-travel')}
			                                        </td>
			                                        <td>
			                                            <i class="fa fa-times" aria-hidden="true"></i>
			                                        </td>
			                                    </tr>
			                                </tbody>
			                            </table>
			                        </div>
			                    </div>
			                </div>
			            </section>
			        </div>
			        <div id="change-log" className="tabcontent">
			            <div className="wptravel-wrapper">
			                <div className="wptravel-section-title">
			                    <h2 id="wptravel-h2-title"><strong>{__('General Info', 'wp-travel')}</strong></h2>
			                </div>
			                <div className="wptravel-general-info">
			                	<div className="grid-container">
				                    {  changeLog.map( ( { version, log } ) => {
					                    return <div className="grid-item wptravel-col33">
				                                <div className="wptravel-inside-wrapper">
				                                    <div className="box-wrapper">
				                                        <div className="wp-image-box-content">
				                                            <div className="wptravel-image-box-wrapper">
				                                                <div className="wptravel-box-content">
				                                                    <h3 className="wp-rect-title">
				                                                        <strong>{version}</strong>
				                                                    </h3>
				                                                    {log}
				                                                </div>
				                                            </div>
				                                        </div>
				                                    </div>
				                                </div>
				                            </div>

							                    
							        } ) }
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			</section>
		</div>						
	);

}

export default FinishedTab