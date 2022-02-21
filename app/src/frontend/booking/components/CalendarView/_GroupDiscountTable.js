import { __ } from '@wordpress/i18n'
import { wpTravelFormat } from '../../_wptravelFunctions'

const _ = lodash
const __i18n = {
	..._wp_travel.strings
}
const DiscountTable = ({ groupPricings }) => {
	return <div className="discount-table">
		<table>
			<thead className="discount-thead">
				<tr>
					<th colSpan="2">{__i18n.bookings.pax}</th>
					<th rowSpan="2">{__i18n.bookings.price}</th>
				</tr>
				<tr>
					<th>{__i18n.from}</th>
					<th>{__i18n.to}</th>
				</tr>
			</thead>
			<tbody className="discount-tbody">
				{
					groupPricings.map((gp, index) => {
						return <tr key={index}>
							<td>{gp.min_pax}</td>
							<td>{gp.max_pax}</td>
							<td dangerouslySetInnerHTML={{ __html: wpTravelFormat(gp.price) }}></td>
						</tr>
					})
				}
			</tbody>
		</table>
	</div>
}
export default DiscountTable;
