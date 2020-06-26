import moment from 'moment'

const DatesListing = () => {
    return <button>{moment().format('MMM DD, YYYY')}</button>
}

export default DatesListing