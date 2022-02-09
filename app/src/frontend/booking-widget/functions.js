const {
    currency,
    currency_symbol: _currencySymbol,
    currency_position: currencyPosition,
    decimal_separator: decimalSeparator,
    number_of_decimals: _toFixed,
    thousand_separator: kiloSeparator
} = _wp_travel

const wpTravelFormat = (_num, style = 'currency') => {
    let regEx = new RegExp(`\\d(?=(\\d{3})+\\${decimalSeparator})`, 'gi')
    let replaceWith = `$&${kiloSeparator}`

    let _formattedNum = parseFloat(_num).toFixed(_toFixed).replace(/\./,decimalSeparator).replace(regEx, replaceWith)
    // _formattedNum = String(_formattedNum).replace(/\./, ',')
    if(style == 'decimal') {
        return _formattedNum
    }
    let positions = {
        'left': `${_currencySymbol}<span>${_formattedNum}</span>`,
        'left_with_space': `${_currencySymbol} <span>${_formattedNum}</span>`,
        'right': `<span>${_formattedNum}</span>${_currencySymbol}`,
        'right_with_space': `<span>${_formattedNum}</span> ${_currencySymbol}`,
    }
    return positions[currencyPosition]
}

// For group discount price display on frontend.
const wpTravelFormatV2 = (_num, style = 'currency') => {
    let regEx = new RegExp(`\\d(?=(\\d{3})+\\${decimalSeparator})`, 'gi')
    let replaceWith = `$&${kiloSeparator}`

    let _formattedNum = parseFloat(_num).toFixed(_toFixed).replace(/\./,decimalSeparator).replace(regEx, replaceWith)
    // _formattedNum = String(_formattedNum).replace(/\./, ',')
    if(style == 'decimal') {
        return _formattedNum
    }
    let positions = {
        'left': `${_currencySymbol}${_formattedNum}`,
        'left_with_space': `${_currencySymbol} ${_formattedNum}`,
        'right': `${_formattedNum}${_currencySymbol}`,
        'right_with_space': `${_formattedNum}${_currencySymbol}`,
    }
    return positions[currencyPosition]
}

const wpTravelTimeout = (promise, ms = 10000) => {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            reject(new Error('[X] Timeout!'))
        }, 1000)
        resolve(promise.then())
    })
}

const wpTravelPHPtoMomentDateFormat = (dateFormat) => {
    const formats = {
        
    }
}
export { wpTravelFormat, wpTravelFormatV2, wpTravelTimeout }
