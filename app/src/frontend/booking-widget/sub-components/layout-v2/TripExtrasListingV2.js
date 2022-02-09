import { __ } from '@wordpress/i18n';
import { wpTravelFormat } from '../../functions'

const { currency_symbol: currencySymbol } = _wp_travel
const __i18n = {
	..._wp_travel.strings
}
const TripExtrasListingV2 = ({ options, onChange, counts, toggler }) => {
	const handleClick = (index, inc) => () => {
		let id = options[index].id
		let _xcount = counts[id] + inc < 0 ? 0 : counts[id] + inc
		if (options[index].is_required && _xcount <= 0)
			_xcount = 1
		onChange(id, _xcount)()
	}
	function bookingSelectorExtrasToggle(){
		jQuery('.wti__selector-heading.extras').parents('.wti__selector-item.wti__trip-extras ').toggleClass('active');
		jQuery('.wti__selector-heading.extras').siblings('.wti__selector-content-wrapper').stop().slideToggle();
	}

	return <div className="wti__selector-item wti__trip-extras active">
		{
			options.length > 0 && <>
				<h5 className="wti__selector-heading extras" onClick={bookingSelectorExtrasToggle}>
					{__i18n.bookings.trip_extras_list_label}
					<div className="buttons">
						<span className="toggler-icon"><i className="fas fa-chevron-down"></i></span>
					</div>
				</h5>
				<div className="wti__selector-content-wrapper">
					<div className="wti__selector-inner">
					{
						options.map((tx, i) => {
							if (typeof tx.tour_extras_metas == 'undefined') {
								return <div className="wti__selector-option" key={i}>
									<span className={`checkbox checked`}>
										<svg className="tick" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style={{ enableBackground: 'new 0 0 512 512' }}>
											<path d="M504.502,75.496c-9.997-9.998-26.205-9.998-36.204,0L161.594,382.203L43.702,264.311c-9.997-9.998-26.205-9.997-36.204,0
                                c-9.998,9.997-9.998,26.205,0,36.203l135.994,135.992c9.994,9.997,26.214,9.99,36.204,0L504.502,111.7
                                C514.5,101.703,514.499,85.494,504.502,75.496z"></path>
										</svg>
									</span>
									<div className="wti__trip_extras_info">
										<h6 className="wti__selector-option-title">
											{tx.title}
										</h6>
									</div>
									{tx.content && <div className="wti__trip_extra_content">
										<p dangerouslySetInnerHTML={{ __html: tx.content }}></p>
									</div>}
								</div>
							}
							let price = tx.is_sale && tx.tour_extras_metas.extras_item_sale_price || tx.tour_extras_metas.extras_item_price
							price = parseFloat(price)
							let _count = counts[tx.id]
							return <div key={i} className={tx.is_required ? 'wti__selector-option wti__required-extra' : 'wti__selector-option'}>
								<span className={`checkbox${_count > 0 ? ' checked' : ''}`}>
									<svg className="tick" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style={{ enableBackground: 'new 0 0 512 512' }}>
										<path d="M504.502,75.496c-9.997-9.998-26.205-9.998-36.204,0L161.594,382.203L43.702,264.311c-9.997-9.998-26.205-9.997-36.204,0
                                c-9.998,9.997-9.998,26.205,0,36.203l135.994,135.992c9.994,9.997,26.214,9.99,36.204,0L504.502,111.7
                                C514.5,101.703,514.499,85.494,504.502,75.496z"></path>
									</svg>
								</span>
								<div className="wti__trip_extras_info">
									<h6 className="wti__selector-option-title">{tx.title}</h6>
									<span className="wti_item-price">{tx.is_sale && <del dangerouslySetInnerHTML={{__html: wpTravelFormat(tx.tour_extras_metas.extras_item_price)}}></del>} <span dangerouslySetInnerHTML={{__html: wpTravelFormat(price)}}></span> /unit</span>
								</div>
								{tx.excerpt && <div className="wti__trip_extra_content">
									<p dangerouslySetInnerHTML={{__html: `${tx.excerpt}${tx.link && `<a class="wti_excerpt" target="new" href="${tx.link}">${__i18n.bookings.trip_extras_link_label}</a>`}`}}>
									</p>
								</div>}
								<div className="wti__selector-people-input">
									<div className="input-field">
										<button className="decrease_val" onClick={handleClick(i, -1)}>-</button>
										<input type="number" value={_count} readOnly/>
										<button className="increase_val" onClick={handleClick(i, 1)}>+</button>
									</div>
									<div className="wti__input-display-figure"> 
										<h6 dangerouslySetInnerHTML={{__html: wpTravelFormat(_count * price)}}></h6>
									</div>
								</div>
							</div>
						})
					}

					</div>
				</div>
			</>
		}
	</div >
}

export default TripExtrasListingV2