import { __ } from '@wordpress/i18n';
import { wpTravelFormat } from '../functions'

const { currency_symbol: currencySymbol } = _wp_travel
const __i18n = {
	..._wp_travel.strings
}
const TripExtrasListing = ({ options, onChange, counts }) => {
	const handleClick = (index, inc) => () => {
		let id = options[index].id
		let _xcount = counts[id] + inc < 0 ? 0 : counts[id] + inc
		if (options[index].is_required && _xcount <= 0)
			_xcount = 1
		onChange(id, _xcount)()
	}

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
										<strong>{tx.title}</strong>
										{tx.content && <div>
											<p dangerouslySetInnerHTML={{ __html: tx.content }}></p>
										</div>}
									</div>
								</li>
							}
							let price = tx.is_sale && tx.tour_extras_metas.extras_item_sale_price || tx.tour_extras_metas.extras_item_price
							price = parseInt(price)
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
									<strong>{tx.title}</strong>
									{tx.content && <div>
										<p dangerouslySetInnerHTML={{
											__html: `${tx.content}${tx.link && `<a target="new" href="${tx.link}">${__i18n.bookings.trip_extras_link_label}</a>`}`
										}}>
										</p>
									</div>
									}
								</div>
								<div className="text-right">
									<span className="item-price">{tx.is_sale && <del dangerouslySetInnerHTML={{__html: wpTravelFormat(tx.tour_extras_metas.extras_item_price)}}></del>} <span dangerouslySetInnerHTML={{__html: wpTravelFormat(price)}}></span> /unit</span>
									<div className="pricing-area">
										<div className="qty-spinner">
											<button onClick={handleClick(i, -1)}>-</button>
											<span>{_count}</span>
											<button onClick={handleClick(i, 1)}>+</button>
										</div>
										<div className="price" dangerouslySetInnerHTML={{__html: wpTravelFormat(_count * price)}}></div>
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