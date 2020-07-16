/*WP Travel Cart and Chekcout JS.*/
jQuery(document).ready(function ($) {

    if (typeof parsley !== "undefined") {
        $('.wp-travel-add-to-cart-form').parsley();
    }
    $('.wp-travel-add-to-cart-form').submit(function (event) {
        event.preventDefault();

        // Validate all input fields.
        var parent = '#' + $(this).attr('id');

        var cart_fields = {};
        $(parent + ' input, ' + parent + ' select').each(function (index) {
            var filterby = $(this).attr('name');
            var filterby_val = $(this).val();

            if ($(this).data('multiple') == true) {
                if ('undefined' == typeof (cart_fields[filterby])) {
                    cart_fields[filterby] = [];
                }
                if ($(this).attr('type') == 'checkbox') {
                    if ($(this).is(':checked')) {
                        cart_fields[filterby].push(filterby_val);
                    }
                }
                if ($(this).data('dependent') == true) {
                    var pare = $(this).data('parent');
                    if ($('#' + pare).is(':checked')) {
                        cart_fields[filterby].push(filterby_val);
                    }
                }
            }
            else {
                cart_fields[filterby] = filterby_val;
            }
        });
        cart_fields['action'] = 'wt_add_to_cart';
        // cart_fields['nonce'] =  'wt_add_to_cart_nonce';

        $.ajax({
            type: "POST",
            url: wp_travel.ajaxUrl,
            data: cart_fields,
            beforeSend: function () { },
            success: function (data) {
                location.href = wp_travel.cartUrl;
            }
        });

    });
    // wt_remove_from_cart
    $('.wp-travel-cart-remove').click(function (e) {
        e.preventDefault();

        if (confirm(wp_travel.strings.confirm)) {
            var cart_id = $(this).data('cart-id');

            $.ajax({
                type: "POST",
                url: wp_travel.ajaxUrl,
                data: { 'action': 'wt_remove_from_cart', 'cart_id': cart_id },
                beforeSend: function () { },
                success: function (data) {
                    location.href = wp_travel.cartUrl;
                }
            });

        }
    });

    // Update Cart
    $('.wp-travel-update-cart-btn').click(function (e) {
        e.preventDefault();
        var update_cart_fields = {};
        $('.ws-theme-cart-page tr.responsive-cart').each(function (i) {
            // pax = $(this).find('input[name="pax^"]').val();
            var cart_id = $(this).find('input[name="cart_id"]').val();
            var pricing_id = $(this).find('input[name="pricing_id"]').val();
            var extra_id = false;
            var extra_qty = false;

            var pax = {};

            $(this).find('input.wp-travel-trip-pax').each(function () {
                pax[$(this).data('category-id')] = this.value;
            });

            // console.log(pax);

            var update_cart_field = {};
            update_cart_field['extras'] = {};
            update_cart_field['extras']['id'] = {};
            update_cart_field['extras']['qty'] = {};
            update_cart_field['pax'] = pax; // Pax includes category id as pax key.
            update_cart_field['pricing_id'] = pricing_id;
            update_cart_field['cart_id'] = cart_id;



            if ($(this).next('.child_products').find('input[name="extra_id"]').length > 0) {


                $(this).next('.child_products').find('input[name="extra_id"]').each(function (j) {
                    extra_id = $(this).val();
                    update_cart_field['extras']['id'][j] = extra_id;
                });

            }
            if ($(this).next('.child_products').find('input[name="extra_qty"]').length > 0) {

                $(this).next('.child_products').find('input[name="extra_qty"]').each(function (j) {
                    extra_qty = $(this).val();
                    update_cart_field['extras']['qty'][j] = extra_qty;
                });

            }

            update_cart_fields[i] = update_cart_field;
        });
        // console.log( update_cart_fields );

        $.ajax({
            type: "POST",
            url: wp_travel.ajaxUrl,
            data: { update_cart_fields, 'action': 'wt_update_cart' },
            beforeSend: function () { },
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    });

    // Apply Coupon
    $('.wp-travel-apply-coupon-btn').click(function (e) {
        e.preventDefault();
        var trip_ids = {};
        $('.ws-theme-cart-page tr.responsive-cart').each(function (i) {
            trip_id = $(this).find('input[name="trip_id"]').val();
            trip_ids[i] = trip_id;
        });

        var CouponCode = $('input[name="wp_travel_coupon_code_input"]').val();

        $.ajax({
            type: "POST",
            url: wp_travel.ajaxUrl,
            data: { trip_ids, CouponCode, 'action': 'wt_cart_apply_coupon' },
            beforeSend: function () { },
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    });

    $('.wp-travel-pax, .wp-travel-tour-extras-qty').on('change', function () {
        $('.wp-travel-update-cart-btn').removeAttr('disabled', 'disabled');
        $('.book-now-btn').attr('disabled', 'disabled');
    });


    // Checkout
    // add Traveller.
    $(document).on('click', '.wp-travel-add-traveller', function (e) {
        e.preventDefault();
        var index = $(this).parent('.text-center').siblings('.payment-content').find('.payment-traveller').length;
        var unique_index = $('.payment-content .payment-traveller:last').data('unique-index');
        if (!unique_index) {
            unique_index = index;
        } else {
            unique_index += 1;
        }
        var cart_id = $(this).data('cart-id');
        var template = wp.template('traveller-info');
        $(this).closest('.text-center').siblings('.payment-content').append(JSON.parse(template({ index: index, cart_id: cart_id, unique_index: unique_index })));
    });

    // Remove Traveller.
    $(document).on('click', '.traveller-remove', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to traveler?')) {
            $(this).closest('.payment-traveller').remove();
            $('.payment-traveller.added').each(function (i) {
                $(this).find('.traveller-index').html(i + 1);
            });
        }
    });

    $(document).on('click, change', '.wp-travel-pax', function () {
        var $this = $(this);
        var productPrice = $this.closest('.product-price');
        var availablePax = productPrice.data('maxPax');
        var minPax = productPrice.data('minPax');
        var selectedPax = 0;
        productPrice.find('.wp-travel-pax').each(function (index) {
            selectedPax += parseInt(this.value);
        })
        if (selectedPax > availablePax) {
            alert(wp_travel.strings.alert.max_pax_alert.replace('{max_pax}', availablePax));
            $this.val(parseInt(availablePax) + parseInt($this.val()) - parseInt(selectedPax));
            $('.wp-travel-update-cart-btn').removeAttr('disabled');
        } else if (selectedPax < minPax) {
            alert(wp_travel.strings.alert.min_pax_alert.replace('{min_pax}', minPax));
            $('.wp-travel-update-cart-btn').attr('disabled', 'disabled');
        } else {
            $('.wp-travel-update-cart-btn').removeAttr('disabled');
        }
    })

});

let wp_travel_cart = {}

wp_travel_cart.format = (_num, style = 'currency') => {
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
        'left': `${_currencySymbol}<span>${_formattedNum}</span>`,
        'left_with_space': `${_currencySymbol} <span>${_formattedNum}</span>`,
        'right': `<span>${_formattedNum}</span>${_currencySymbol}`,
        'right_with_space': `<span>${_formattedNum}</span> ${_currencySymbol}`,
    }
    return positions[currencyPosition]
}

wp_travel_cart.timeout = (promise, ms) => {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            reject(new Error("request timeout"))
        }, ms)
        resolve(promise.then(resolve, reject))
    })
}

const wptravelcheckout = (shoppingCart) => {
    const bookNowBtn = document.getElementById('wp-travel-book-now')
    bookNowBtn && bookNowBtn.addEventListener( 'wptcartchange', e => {
        e.target.disabled = true
    } )
    bookNowBtn && bookNowBtn.addEventListener('click', e => {
        
    })
    if (!shoppingCart) {
        return
    }
    const cartItems = shoppingCart && shoppingCart.querySelectorAll('[data-cart-id]')
    if (cartItems && cartItems.length <= 0) {
        return
    }

    const toggleBookNowBtn = () => {
        const dirtyItems = shoppingCart.querySelectorAll('[data-dirty]')
        if(!bookNowBtn) {
            return
        }
        if(!!dirtyItems && dirtyItems.length > 0 ) {
            bookNowBtn.disabled = true
        } else {
            bookNowBtn.disabled = false
        }
    }
    const toggleCartLoader = (on) => {
        if (on) {
            cartLoader.removeAttribute('style')
        } else {
            cartLoader.style.display = 'none'
        }
    }
    // let cart = {}
    let cartLoader = shoppingCart.querySelector('.wp-travel-cart-loader')
    cartLoader && toggleCartLoader(true)
    wp_travel && wp_travel_cart.timeout(fetch(`${wp_travel.ajaxUrl}?action=wp_travel_get_cart&_nonce=${wp_travel._nonce}`)
        .then(res => {
            res.json()
                .then(result => {
                    toggleCartLoader()
                    if (result.success && result.data.code === 'WP_TRAVEL_CART') {
                        if (result.data.cart) {
                            wp_travel_cart.cart = result.data.cart
                            Object.freeze(wp_travel_cart.cart)
                        }
                    }
                })
        })
        , 10000)
        .catch(error => {
            alert('[X] Request Timeout!')
            toggleCartLoader()
        })

    const updateItem = id => {
        let _data = {}
        let tripTotalWOExtras = 0, tripTotalPartialWOExtras = 0, extrasTotal = 0

        let tripTotal = 0
        let item = wp_travel_cart.cart && wp_travel_cart.cart.cart_items && wp_travel_cart.cart.cart_items[id]
        let itemNode = shoppingCart.querySelector(`[data-cart-id="${id}"]`)
        let pricing = item.trip_data.pricings.find(p => p.id == parseInt(item.pricing_id))
        let categories = pricing.categories
        let _tripExtras = pricing.trip_extras
        let wptCTotals = itemNode.querySelectorAll('[data-wpt-category-count]')

        let payoutPercentage = item.trip_data && item.trip_data.minimum_partial_payout_percent

        // Categories.
        let formGroupsCategory = itemNode.querySelectorAll('[data-wpt-category]')
        formGroupsCategory.forEach(fg => {
            let categoryTotalContainer = fg.querySelector('[data-wpt-category-total]')
            let dataCategoryCount = fg.querySelector('[data-wpt-category-count-input]')
            let dataCategoryPrice = fg.querySelector('[data-wpt-category-price]')

            let _category = categories.find(c => c.id == parseInt(fg.dataset.wptCategory))

            let _price = _category && _category.is_sale ? parseFloat(_category['sale_price']) : parseFloat(_category['regular_price'])
            let _count = dataCategoryCount && parseInt(dataCategoryCount.value) || 0
            if (_category.has_group_price) {
                let _groupPrice = _category.group_prices.find(gp => _count >= parseInt(gp.min_pax) && _count <= parseInt(gp.max_pax))
                _price = _groupPrice && _groupPrice.price || _price
                if (dataCategoryPrice)
                    dataCategoryPrice.innerHTML = wp_travel_cart.format(_price)
            }
            let categoryTotal = _category.price_per == 'group' ? _count > 0 && _price || 0 : _price * _count
            wptCTotals && wptCTotals.forEach(wpct => {
                if (wpct.dataset.wptCategoryCount == fg.dataset.wptCategory)
                    wpct.innerHTML = _count
            })

            if (categoryTotalContainer)
                categoryTotalContainer.innerHTML = wp_travel_cart.format(categoryTotal)
            tripTotal += parseFloat(categoryTotal)
            tripTotalWOExtras += parseFloat(categoryTotal)
            tripTotalPartialWOExtras += parseFloat(categoryTotal) * parseFloat(payoutPercentage) / 100
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
                txTotalContainer.innerHTML = wp_travel_cart.format(itemTotal)
            tripTotal += itemTotal
            extrasTotal += itemTotal
        })

        _data = {
            tripTotalWOExtras,
            tripTotalPartialWOExtras,
            extrasTotal,
            tripTotal
        }

        itemNode.querySelector('[data-wpt-item-total]').innerHTML = wp_travel_cart.format(tripTotal)
        return _data
    }

    shoppingCart && shoppingCart.addEventListener('wptcartchange', e => {
        let cartTotal = 0, tripTotalWOExtras = 0, txTotal = 0, tripTotalPartialWOExtras = 0;
        let cartTotalContainers = document.querySelectorAll('[data-wpt-cart-total]')
        let cartTotalPartialContainers = document.querySelectorAll('[data-wpt-cart-partial-total]')
        let cartSubtotalContainer = e.target.querySelector('[data-wpt-cart-subtotal]')
        let cartDiscountContainer = e.target.querySelector('[data-wpt-cart-discount]')
        let cartTaxContainer = e.target.querySelector('[data-wpt-cart-tax]')
        let _cartItems = e.target.querySelectorAll('[data-cart-id]')
        _cartItems && _cartItems.forEach(ci => {
            let totals = updateItem(ci.dataset.cartId)
            cartTotal += totals.tripTotal
            tripTotalWOExtras += totals.tripTotalWOExtras
            tripTotalPartialWOExtras += totals.tripTotalPartialWOExtras
            txTotal += totals.extrasTotal
        })

        if (cartSubtotalContainer)
            cartSubtotalContainer.innerHTML = wp_travel_cart.format(cartTotal)

        let fullTotalContainer = e.target.querySelector('[data-wpt-cart-full-total]')
        if (e.detail && e.detail.coupon || wp_travel_cart.cart.coupon && wp_travel_cart.cart.coupon.coupon_id) {
            let coupon = e.detail && e.detail.coupon || wp_travel_cart.cart.coupon
            let _cValue = coupon.value && parseInt(coupon.value) || 0
            fullTotalContainer.innerHTML = wp_travel_cart.format(cartTotal)
            if (cartDiscountContainer) {
                cartDiscountContainer.innerHTML = coupon.type == 'fixed' ? '- ' + wp_travel_cart.format(_cValue) : '- ' + wp_travel_cart.format(cartTotal * _cValue / 100)
                cartDiscountContainer.closest('[data-wpt-extra-field]').removeAttribute('style')
            }
            cartTotal = coupon.type == 'fixed' ? cartTotal - _cValue : cartTotal * (100 - _cValue) / 100
        }

        if( wp_travel_cart.cart.total.discount <= 0 ) {
            fullTotalContainer.innerHTML = ''
            cartDiscountContainer.closest('[data-wpt-extra-field]').style.display = 'none'
        }

        if (wp_travel_cart.cart.tax) {
            if (cartTaxContainer)
                cartTaxContainer.innerHTML = '+ ' + wp_travel_cart.format(cartTotal * parseInt(wp_travel_cart.cart.tax) / 100)
            cartTotal = cartTotal * (100 + parseInt(wp_travel_cart.cart.tax)) / 100
        }

        if (cartTotalContainers) {
            cartTotalContainers.forEach(ctt => ctt.innerHTML = wp_travel_cart.format(cartTotal))
        }

        if (cartTotalPartialContainers) {
            cartTotalPartialContainers.forEach(ctpc => {
                let _partialTotal = (tripTotalPartialWOExtras + txTotal)
                if(wp_travel_cart.cart.tax) {
                    _partialTotal = _partialTotal * (100 + parseFloat(wp_travel_cart.cart.tax)) / 100
                }
                ctpc.innerHTML = wp_travel_cart.format(_partialTotal)
            })
        }

        // cartTotalContainer.innerHTML = wp_travel_cart.format(cartTotal)
        let cartItemsCountContainer = e.target.querySelector('[data-wpt-cart-item-count]')
        if (cartItemsCountContainer)
            cartItemsCountContainer.innerHTML = _cartItems.length
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
                wp_travel_cart.timeout(
                    fetch(`${wp_travel.ajaxUrl}?action=wp_travel_remove_cart_item&_nonce=${wp_travel._nonce}&cart_id=${ci.dataset.cartId}`)
                        .then(res => res.json())
                        .then(result => {
                            if (result.success && result.data.code == 'WP_TRAVEL_REMOVED_CART_ITEM') {
                                if (result.data.cart && result.data.cart.length <= 0) {
                                    window.location.reload()
                                }
                                wp_travel_cart.cart = result.data.cart
                                let total = result.data.cart.total
                                if (wp_travel.payment) {
                                    wp_travel.payment.trip_price = parseFloat(total.total)
                                    wp_travel.payment.payment_amount = parseFloat(total.total_partial)
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
            let cart = wp_travel_cart.cart.cart_items && wp_travel_cart.cart.cart_items[cart_id] || {}
            if (cart.trip_data && cart.trip_data.inventory && cart.trip_data.inventory.enable_trip_inventory === 'yes') {
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
                wp_travel_cart.timeout(
                    fetch(`${wp_travel.ajaxUrl}?${qs}&action=wp_travel_get_inventory&_nonce=${wp_travel._nonce}`)
                        .then(res => res.json().then(result => {
                            loader.style.display = 'none'
                            if (result.success && result.data.code === 'WP_TRAVEL_INVENTORY_INFO') {
                                if (result.data.inventory.length > 0) {
                                    let inventory = result.data.inventory[0]
                                    ci.querySelectorAll('[data-wpt-category-count-input]').forEach(_ci => _ci.max = inventory.pax_available)
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
                    bookNowBtn && bookNowBtn.dispatchEvent(new Event('wptcartchange'))

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

            wp_travel_cart.timeout(
                fetch(`${wp_travel.ajaxUrl}?action=wp_travel_update_cart_item&cart_id=${cartId}&_nonce=${wp_travel._nonce}`, {
                    method: 'POST',
                    body: JSON.stringify(_data)
                }).then(res => res.json())
                    .then(result => {
                        loader.style.display = 'none'
                        if (result.success) {
                            wp_travel_cart.cart = result.data.cart
                            let total = result.data.cart.total
                            if (wp_travel.payment) {
                                wp_travel.payment.trip_price = parseFloat(total.total)
                                wp_travel.payment.payment_amount = parseFloat(total.total_partial)
                            }
                            toggleBookNowBtn()
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

    const paymentModeInput = document.getElementById('wp-travel-payment-mode')
    paymentModeInput && paymentModeInput.addEventListener('change', e => {
        let basket = document.querySelector('#shopping-cart')
        let container = basket && basket.querySelector('[data-wpt-cart-partial-total]') && basket.querySelector('[data-wpt-cart-partial-total]').closest('p')
        if ('partial' === e.target.value) {
            if (container && container.style.display == 'none') {
                container.removeAttribute('style')
            }
        } else {
            container.style.display = 'none'
        }
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
            p.innerHTML = message
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
            wp_travel_cart.timeout(
                fetch(`${wp_travel.ajaxUrl}?action=wp_travel_apply_coupon&_nonce=${wp_travel._nonce}`, {
                    method: 'POST',
                    body: JSON.stringify({ couponCode: couponField.value })
                }).then(res => res.json())
                    .then(result => {
                        toggleCartLoader()
                        if (result.success) {
                            wp_travel_cart.cart = result.data.cart
                            couponField.toggleAttribute('readonly')
                            e.target.innerHTML = e.target.dataset.successL10n
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
document.getElementById('shopping-cart') && wptravelcheckout(document.getElementById('shopping-cart'))
