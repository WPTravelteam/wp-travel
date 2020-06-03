wp_travel.format = (_num, style = 'currency') => {
    const {
        currency,
        currency_symbol: _currencySymbol,
        currency_position: currencyPosition,
        decimal_separator: decimalSeparator,
        number_of_decimals: _toFixed,
        thousand_separator: kiloSeparator
    } = wp_travel

    let regEx = new RegExp(`\\d(?=(\\d{3})+\\${decimalSeparator})`, 'gi')
    let replaceWith = `$&${kiloSeparator}`

    let _formattedNum = parseFloat(_num).toFixed(_toFixed).replace(/\./, decimalSeparator).replace(regEx, replaceWith)
    // _formattedNum = String(_formattedNum).replace(/\./, ',')
    if (style == 'decimal') {
        return _formattedNum
    }
    let positions = {
        'left': `${_currencySymbol}${_formattedNum}`,
        'left_with_space': `${_currencySymbol} ${_formattedNum}`,
        'right': `${_formattedNum}${_currencySymbol}`,
        'right_with_space': `${_formattedNum} ${_currencySymbol}`,
    }
    return positions[currencyPosition]
}

wp_travel.timeout = (promise, ms) => {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            reject(new Error("request timeout"))
        }, ms)
        resolve(promise.then(resolve, reject))
    })
}

let wp_travel_cart = {}
const wptravelcheckout = () => {
    const shoppingCart = document.getElementById('shopping-cart')
    const cartItems = shoppingCart && shoppingCart.querySelectorAll('[data-cart-id]')
    const toggleCartLoader = ( on ) => {
        if(on) {
            cartLoader.removeAttribute('style')
        } else {
            cartLoader.style.display = 'none'
        }
    }
    // let cart = {}
    let cartLoader = shoppingCart.querySelector('.wp-travel-cart-loader')
    cartLoader && toggleCartLoader(true)
    wp_travel && wp_travel.timeout(fetch(`${wp_travel.ajaxUrl}?action=wp_travel_get_cart&_nonce=${wp_travel._nonce}`)
        .then(res => {
            res.json()
                .then(result => {
                    toggleCartLoader()
                    if (result.success && result.data.code === 'WP_TRAVEL_CART') {
                        if (result.data.cart) {
                            wp_travel_cart = result.data.cart
                            Object.freeze(wp_travel_cart)
                        }
                    }
                })
        })
        , 10000)
        .catch(error => {
            alert('[X] Request Timeout!')
            toggleCartLoader()
        })

    // let cartItems = shoppingCart.querySelectorAll('[data-cart-id]')

    const updateItem = id => {
        let tripTotal = 0
        let item = wp_travel_cart && wp_travel_cart.cart_items && wp_travel_cart.cart_items[id]
        let itemNode = shoppingCart.querySelector(`[data-cart-id="${id}"]`)
        let pricing = item.trip_data.pricings.find(p => p.id == parseInt(item.pricing_id))
        let categories = pricing.categories
        let _tripExtras = pricing.trip_extras
        let wptCTotals = itemNode.querySelectorAll('[data-wpt-category-count]')

        // Categories.
        let formGroupsCategory = itemNode.querySelectorAll('[data-wpt-category]')
        formGroupsCategory.forEach(fg => {
            let categoryTotalContainer = fg.querySelector('[data-wpt-category-total]')
            let dataCategoryCount = fg.querySelector('[data-wpt-category-count-input]')
            let dataCategoryPrice = fg.querySelector('[data-wpt-category-price]')

            let _category = categories.find(c => c.id == parseInt(fg.dataset.wptCategory))

            let _price = _category && _category.is_sale ? parseFloat(_category['sale_price']) : parseFloat(_category['regular_price'])
            let _count = dataCategoryCount && dataCategoryCount.value || 0
            let categoryTotal = _category.price_per == 'group' ? _count > 0 && _price || 0 : _price * _count
            wptCTotals && wptCTotals.forEach(wpct => {
                if (wpct.dataset.wptCategoryCount == fg.dataset.wptCategory)
                    wpct.textContent = _count
            })

            if (categoryTotalContainer)
                categoryTotalContainer.textContent = wp_travel.format(categoryTotal, 'decimal')
            tripTotal += categoryTotal
        })
        // Extras.

        let formGroupsTx = itemNode.querySelectorAll('[data-wpt-tx]')
        formGroupsTx && formGroupsTx.forEach(tx => {
            let _extra = _tripExtras.find(c => c.id == parseInt(tx.dataset.wptTx))
            if (!_extra.tour_extras_metas) {
                return
            }
            let txTotalContainer = tx.querySelector('[data-wpt-tx-total]')
            let datatxCount = tx.querySelector('[data-wpt-tx-count-input]')
            let _price = _extra.is_sale && _extra.tour_extras_metas.extras_item_sale_price || _extra.tour_extras_metas.extras_item_price
            let _count = datatxCount && datatxCount.value || 0
            let itemTotal = parseFloat(_price) * parseInt(_count)
            if (txTotalContainer)
                txTotalContainer.textContent = wp_travel.format(itemTotal, 'decimal')
            tripTotal += itemTotal
        })

        itemNode.querySelector('[data-wpt-item-total]').textContent = wp_travel.format(tripTotal, 'decimal')
        return tripTotal
    }

    shoppingCart && shoppingCart.addEventListener('wptcartchange', e => {
        let cartTotal = 0
        let cartTotalContainer = e.target.querySelector('[data-wpt-cart-total]')
        let cartSubtotalContainer = e.target.querySelector('[data-wpt-cart-subtotal]')
        let cartDiscountContainer = e.target.querySelector('[data-wpt-cart-discount]')
        let cartTaxContainer = e.target.querySelector('[data-wpt-cart-tax]')
        let _cartItems = e.target.querySelectorAll('[data-cart-id]')
        _cartItems && _cartItems.forEach(ci => {
            cartTotal += updateItem(ci.dataset.cartId)
        })

        if (cartSubtotalContainer)
            cartSubtotalContainer.textContent = wp_travel.format(cartTotal)

        if (e.detail && e.detail.coupon || wp_travel_cart.coupon && wp_travel_cart.coupon.coupon_id) {
            let coupon = e.detail && e.detail.coupon || wp_travel_cart.coupon
            let _cValue = coupon.value && parseInt(coupon.value) || 0
            let fullTotalContainer = e.target.querySelector('[data-wpt-cart-full-total]')
            fullTotalContainer.textContent = cartTotal
            if (cartDiscountContainer) {
                cartDiscountContainer.textContent = coupon.type == 'fixed' ? '- ' + wp_travel.format(_cValue) : '- ' + wp_travel.format(cartTotal * _cValue / 100)
                cartDiscountContainer.closest('[data-wpt-extra-field]').removeAttribute('style')
            }
            cartTotal = coupon.type == 'fixed' ? cartTotal - _cValue : cartTotal * (100 - _cValue) / 100
        }

        if (wp_travel_cart.tax) {
            if (cartTaxContainer)
                cartTaxContainer.textContent = '+ ' + wp_travel.format(cartTotal * parseInt(wp_travel_cart.tax) / 100)
            cartTotal = cartTotal * (100 + parseInt(wp_travel_cart.tax)) / 100
        }

        if (cartTotalContainer)
            cartTotalContainer.textContent = wp_travel.format(cartTotal, 'decimal')
        let cartItemsCountContainer = e.target.querySelector('[data-wpt-cart-item-count]')
        if (cartItemsCountContainer)
            cartItemsCountContainer.textContent = _cartItems.length
    })

    cartItems && cartItems.forEach(ci => {
        let edit = ci.querySelector('a.edit')
        let collapse = ci.querySelector('.update-fields-collapse')
        let _deleteBtn = ci.querySelector('.del-btn')
        let loader = ci.querySelector('.wp-travel-cart-loader')
        _deleteBtn && _deleteBtn.addEventListener('click', e => {
            e.preventDefault()
            if (confirm(_deleteBtn.dataset.l10n)) {
                toggleCartLoader(true)
                wp_travel.timeout(
                    fetch(`${wp_travel.ajaxUrl}?action=wp_travel_remove_cart_item&_nonce=${wp_travel._nonce}&cart_id=${ci.dataset.cartId}`)
                        .then(res => res.json())
                        .then(result => {
                            if (result.success && result.data.code == 'WP_TRAVEL_REMOVED_CART_ITEM') {
                                if (result.data.cart && result.data.cart.length <= 0) {
                                    window.location.reload()
                                }
                                ci.remove()
                                shoppingCart.dispatchEvent(new Event('wptcartchange'))
                                toggleCartLoader()
                            }
                        }), 10000)
                    .catch(error => {
                        alert('[X] Request Timeout!')
                        toggleCartLoader()
                    })
            }
        })
        edit && edit.addEventListener('click', e => {
            if (collapse.className.indexOf('active') < 0) {
                collapse.style.display = 'block'
                collapse.classList.add('active')
            } else {
                collapse.style.display = 'none'
                collapse.classList.remove('active')
            }
            if (collapse.className.indexOf('active') < 0) {
                return
            }
            let cart_id = e.target.dataset.wptTargetCartId
            let cart = wp_travel_cart.cart_items && wp_travel_cart.cart_items[cart_id] || {}
            if (cart.trip_data.inventory && cart.trip_data.inventory.enable_trip_inventory === 'yes') {
                let qs = ''

                let pricing_id = cart.pricing_id || 0
                qs += pricing_id && `pricing_id=${pricing_id}` || ''

                let trip_id = cart.trip_data && cart.trip_data.id || 0
                qs += trip_id && `&trip_id=${trip_id}` || ''

                let trip_time = cart.trip_time
                qs += trip_time && `&trip_time=${trip_time}` || ''

                if (cart.arrival_date && new Date(cart.arrival_date).toString().toLowerCase() != 'invalid date') {
                    let _date = new Date(cart.arrival_date)
                    let _year = _date.getFullYear()
                    let _month = _date.getMonth() + 1
                    _month = String(_month).padStart(2, '0')
                    let _day = String(_date.getDate()).padStart(2, '0')
                    _date = `${_year}-${_month}-${_day}`
                    qs += _date && `&selected_date=${_date}` || ''
                }
                loader.removeAttribute('style')
                wp_travel.timeout(
                    fetch(`${wp_travel.ajaxUrl}?${qs}&action=wp_travel_get_inventory&_nonce=${wp_travel._nonce}`)
                        .then(res => res.json().then(result => {
                            loader.style.display = 'none'
                            if (result.success && result.data.code === 'WP_TRAVEL_INVENTORY_INFO') {
                                if (result.data.inventory.length > 0) {
                                    let inventory = result.data.inventory[0]
                                    ci.querySelectorAll('[data-wpt-category-count-input]').forEach(_ci => _ci.max = inventory.pax_limit)
                                }
                            }
                        }))
                ).catch(error => {
                    alert('[X] Request Timeout!')
                    loader.style.display = 'none'
                })
            }
        })

        const wptCategories = ci.querySelectorAll('[data-wpt-category], [data-wpt-tx]')
        wptCategories && wptCategories.forEach(wc => {
            let _input = wc.querySelector('[data-wpt-category-count-input], [data-wpt-tx-count-input]')

            let spinners = wc.querySelectorAll('[data-wpt-count-up],[data-wpt-count-down]')
            spinners && spinners.forEach(sp => {
                sp.addEventListener('click', e => {
                    e.preventDefault()
                    let paxSum = 0
                    ci.querySelectorAll('[data-wpt-category-count-input]').forEach(input => {
                        paxSum += parseInt(input.value)
                    })

                    if (typeof sp.dataset.wptCountUp != 'undefined') {
                        if (_input && _input.dataset.wptCategoryCountInput) {
                            let _inputvalue = parseInt(_input.value) + 1 < 0 ? 0 : parseInt(_input.value) + 1
                            if (paxSum + 1 <= parseInt(_input.max) && _inputvalue >= parseInt(_input.min)) {
                                _input.value = _inputvalue
                            }
                        } else {
                            _input.value = parseInt(_input.value) + 1
                        }
                    }
                    if (typeof sp.dataset.wptCountDown != 'undefined') {
                        if (_input && _input.dataset.wptCategoryCountInput) {
                            let _inputvalue = parseInt(_input.value) - 1 < 0 ? 0 : parseInt(_input.value) - 1
                            if (paxSum - 1 <= parseInt(_input.max) && _inputvalue >= parseInt(_input.min)) {
                                _input.value = _inputvalue
                            }
                        } else {
                            _input.value = parseInt(_input.value) - 1 < parseInt(_input.min) ? _input.min : parseInt(_input.value) - 1
                        }
                    }
                    shoppingCart.dispatchEvent(new Event('wptcartchange'))
                    ci.querySelector('form [type="submit"]').disabled = false
                    ci.querySelector('h5 a').style.color = 'orange'
                })
            })
        })
    })

    cartItems && cartItems.forEach(ci => {
        let loader = ci.querySelector('.wp-travel-cart-loader')
        const categories = ci.querySelectorAll('[data-wpt-category]')
        const tripExtras = ci.querySelectorAll('[data-wpt-tx]')
        const _form = ci.querySelector('form')
        _form.addEventListener('submit', e => {
            e.preventDefault()
            let _btn = _form.querySelector('[type="submit"]')
            _btn.disabled = true
            loader.removeAttribute('style')
            const cartId = ci.dataset.cartId
            let pax = {}
            categories && categories.forEach(cf => {
                let _input = cf.querySelector('[data-wpt-category-count-input]')
                const categoryId = cf.dataset.wptCategory
                const value = _input && _input.value
                pax = { ...pax, [categoryId]: value }
            })

            let txCounts = {}
            tripExtras && tripExtras.forEach(tx => {
                let _input = tx.querySelector('[data-wpt-tx-count-input]')
                const txId = tx.dataset.wptTx
                const value = _input && _input.value
                txCounts = { ...txCounts, [txId]: value }
            })

            const _data = {
                pax,
                wp_travel_trip_extras: {
                    id: Object.keys(txCounts),
                    qty: Object.values(txCounts)
                }
            }

            wp_travel.timeout(
                fetch(`${wp_travel.ajaxUrl}?action=wp_travel_update_cart_item&cart_id=${cartId}&_nonce=${wp_travel._nonce}`, {
                    method: 'POST',
                    body: JSON.stringify(_data)
                }).then(res => res.json())
                    .then(result => {
                        loader.style.display = 'none'
                        if (result.success) {
                            wp_travel_cart = result.data.cart
                            ci.querySelector('h5 a').removeAttribute('style')
                        } else {
                            _btn.disabled = false
                        }
                    }), 10000)
                .catch(error => {
                    alert('[X] Request Timeout!')
                    loader.style.display = 'none'
                    _btn.disabled = false
                })

        })
    })

    // Coupon
    const couponForm = document.getElementById('wp-travel-coupon-form')
    const couponBtn = couponForm && couponForm.querySelector('button')
    const couponField = couponForm && couponForm.querySelector('.coupon-input-field')

    couponField && couponField.addEventListener('keyup', e => {
        toggleError(e.target)
        e.target.value.length > 0 && e.target.removeAttribute('style')
    })

    const toggleError = (el, message) => {
        if (message) {
            let p = document.createElement('p')
            p.classList.add('error')
            p.textContent = message
            el.after(p)
        } else {
            let error = el.parentElement.querySelector('.error')
            error && error.remove()
        }
    }

    couponBtn && couponField && couponBtn.addEventListener('click', e => {
        e.preventDefault()
        if (couponField.value.length <= 0) {
            couponField.style.borderColor = 'red'
            couponField.focus()
        } else {
            toggleCartLoader(true)
            e.target.disabled = true
            wp_travel.timeout(
                fetch(`${wp_travel.ajaxUrl}?action=wp_travel_apply_coupon&_nonce=${wp_travel._nonce}`, {
                    method: 'POST',
                    body: JSON.stringify({ couponCode: couponField.value })
                }).then(res => res.json())
                    .then(result => {
                        toggleCartLoader()
                        if (result.success) {
                            wp_travel_cart = result.data.cart
                            couponField.toggleAttribute('readonly')
                            e.target.textContent = e.target.dataset.successL10n
                            e.target.style.backgroundColor = 'green'
                            shoppingCart.dispatchEvent(new CustomEvent('wptcartchange', { detail: { coupon: result.data.cart.coupon } }))
                        } else {
                            couponField.focus()
                            toggleError(couponField, result.data[0].message)
                            e.target.disabled = false
                        }
                    }), 10000)
                    .catch(error => {
                        alert('[X] Request Timeout!')
                        toggleCartLoader()
                    })
        }
    })

}
wptravelcheckout()