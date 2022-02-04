import { __ } from '@wordpress/i18n';
import { wpTravelFormat } from '../functions'
import apiFetch from '@wordpress/api-fetch';
import { useState } from '@wordpress/element'

const { currency_symbol: currencySymbol } = _wp_travel
const __i18n = {
	..._wp_travel.strings
}
const TripExtrasListing = ({ options, onChange, counts }) => {
console.log( 'options', options );

	// const [allTripExtras, setAllTripExtras] = useState([])

	const handleClick = (index, inc) => () => {
		let id = options[index].id
		let _xcount = counts[id] + inc < 0 ? 0 : counts[id] + inc
		if (options[index].is_required && _xcount <= 0)
			_xcount = 1
		onChange(id, _xcount)()
	}
	// if ( options.length > 0 && ! allTripExtras.length ) {
	// 	const url = `${wp_travel.ajaxUrl}?action=wp_travel_get_trip_extras&_nonce=${_wp_travel._nonce}`;
	// 	apiFetch( { url: url, data: {trip_ids:options}, method:'post' } ).then( ( result ) => {
	// 		if ( result.success ) {
	// 			setAllTripExtras( result.data.trip_extras );
	// 		}
	// 	} )
	// }
	return <div className="wp-travel-booking__trip-extras-wrapper">
		{
			options.length > 0 && <>
				<h4>{__i18n.bookings.trip_extras_list_label}</h4>
				<ul className="wp-travel-booking__trip-option-list">
					{
						options.map((tx, i) => {
							if (typeof tx.tour_extras_metas == 'undefined') {
								return <li key={i}>
									<span className={`checkbox checked`}>
										<svg className="tick" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style={{ enableBackground: 'new 0 0 512 512' }}>
											<path d="M504.502,75.496c-9.997-9.998-26.205-9.998-36.204,0L161.594,382.203L43.702,264.311c-9.997-9.998-26.205-9.997-36.204,0
                                c-9.998,9.997-9.998,26.205,0,36.203l135.994,135.992c9.994,9.997,26.214,9.99,36.204,0L504.502,111.7
                                C514.5,101.703,514.499,85.494,504.502,75.496z"></path>
										</svg>
									</span>
									<div className="text-left">
										<strong>{tx.title}
										</strong>
										{tx.content && <div>
											<p dangerouslySetInnerHTML={{ __html: tx.content }}></p>
										</div>}
									</div>
								</li>
							}
							let price = tx.is_sale && tx.tour_extras_metas.extras_item_sale_price || tx.tour_extras_metas.extras_item_price
							price = parseFloat(price)
							let _count = counts[tx.id]
							return <li key={i} className={tx.is_required ? 'wp-travel__required-extra' : ''}>
								<span className={`checkbox${_count > 0 ? ' checked' : ''}`}>
									<svg className="tick" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style={{ enableBackground: 'new 0 0 512 512' }}>
										<path d="M504.502,75.496c-9.997-9.998-26.205-9.998-36.204,0L161.594,382.203L43.702,264.311c-9.997-9.998-26.205-9.997-36.204,0
                                c-9.998,9.997-9.998,26.205,0,36.203l135.994,135.992c9.994,9.997,26.214,9.99,36.204,0L504.502,111.7
                                C514.5,101.703,514.499,85.494,504.502,75.496z"></path>
									</svg>
								</span>
								<div className="text-left">
									<div className="title">
										<strong>{tx.title}
										</strong>
									</div>
									<div className="info-container">
										<a className="info">
											{tx.content && 
											<>
											<div className="info-icon">
												<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" className="svg-inline--fa fa-info-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path></svg>
											</div>
											<div className="infoBox">
												<p className="desc" dangerouslySetInnerHTML={{
													__html: `${tx.content}${tx.link && `<a target="new" href="${tx.link}">${__i18n.bookings.trip_extras_link_label}</a>`}`
												}}>
												</p>
											</div>
											</>
											}
										</a>
									</div>
								</div>
								<div className="text-right">
									<span className="item-price">{tx.is_sale && <del dangerouslySetInnerHTML={{__html: wpTravelFormat(tx.tour_extras_metas.extras_item_price)}}></del>} <span dangerouslySetInnerHTML={{__html: wpTravelFormat(price)}}></span> /{__i18n.unit}</span>
									<div className="pricing-area">
										<div className="qty-spinner">
											<button onClick={handleClick(i, -1)}>-</button>
											<span>{_count}</span>
											<button onClick={handleClick(i, 1)}>+</button>
										</div>
										{/* <div className="price" dangerouslySetInnerHTML={{__html: wpTravelFormat(_count * price)}}></div> */}
									</div>
								</div>
							</li>
						})
					}
				</ul>
			</>
		}
	</div >
}

export default TripExtrasListing