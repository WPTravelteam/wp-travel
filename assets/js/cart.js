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
            filterby = $(this).attr('name');
            filterby_val = $(this).val();

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
            cart_id = $(this).find('input[name="cart_id"]').val();
            pricing_id = $(this).find('input[name="pricing_id"]').val();
            extra_id = false;
            extra_qty = false;

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
        $this = $(this);
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

(function () {
    const shoppingCart = document.getElementById('shopping-cart')
    const cartItems = shoppingCart && shoppingCart.querySelectorAll('[data-cart-id]')
    const cartItemForms = document.querySelectorAll('.wp-travel__cart-item')
    const couponForm = document.getElementById('wp-travel-coupon-form')
    const couponBtn = couponForm && couponForm.querySelector('button')
    const couponField = couponForm && couponForm.querySelector('.coupon-input-field')

    shoppingCart && shoppingCart.addEventListener('wptcartchange', e => {
        let cartTotal = 0
        let cartTotalContainer = e.target.querySelector('[data-wpt-cart-total]')
        let _cartItems = e.target.querySelectorAll('[data-cart-id]')
        _cartItems && _cartItems.forEach(ci => {
            let tripTotal = 0
            let tripTotalContainer = ci.querySelector('[data-wpt-item-total]')
            let formGroupsCategory = ci.querySelectorAll('[data-wpt-category]')
            let formGroupsTx = ci.querySelectorAll('[data-wpt-tx]')
            formGroupsCategory && formGroupsCategory.forEach(fg => {
                let itemTotalContainer = fg.querySelector('[data-wpt-category-total]')
                let dataCategoryCount = fg.querySelector('[data-wpt-category-count-input]')
                let dataCategoryPrice = fg.querySelector('[data-wpt-category-price]')
                let _price = dataCategoryPrice && parseFloat(dataCategoryPrice.textContent) || 0
                let _count = dataCategoryCount && dataCategoryCount.value || 0
                let itemTotal = _price * _count
                if (itemTotalContainer)
                    itemTotalContainer.textContent = itemTotal
                tripTotal += itemTotal
                cartTotal += itemTotal
                console.debug(_price, _count, tripTotal)
            })
            formGroupsTx && formGroupsTx.forEach(tx => {
                let txTotalContainer = tx.querySelector('[data-wpt-tx-total]')
                let datatxCount = tx.querySelector('[data-wpt-tx-count-input]')
                let datatxPrice = tx.querySelector('[data-wpt-tx-price]')
                let _price = datatxPrice && parseFloat(datatxPrice.textContent) || 0
                let _count = datatxCount && datatxCount.value || 0
                let itemTotal = _price * _count
                if (txTotalContainer)
                    txTotalContainer.textContent = itemTotal
                tripTotal += itemTotal
                cartTotal += itemTotal
                console.debug(tripTotal)

            })
            if (tripTotalContainer)
                tripTotalContainer.textContent = tripTotal
        })
        if (cartTotalContainer)
            cartTotalContainer.textContent = cartTotal
        let cartItemsCountContainer = e.target.querySelector('[data-wpt-cart-item-count]')
        if( cartItemsCountContainer )
            cartItemsCountContainer.textContent = _cartItems.length

        console.log(cartTotal)
    })


    cartItems && cartItems.forEach(ci => {
        let edit = ci.querySelector('a.edit')
        let collapse = ci.querySelector('.update-fields-collapse')
        let _deleteBtn = ci.querySelector('.del-btn')
        _deleteBtn && _deleteBtn.addEventListener('click', e => {
            e.preventDefault()
            if (confirm(_deleteBtn.dataset.l10n)) {
                fetch(`${wp_travel.ajaxUrl}?action=wp_travel_remove_cart_item&_nonce=${wp_travel._nonce}&cart_id=${ci.dataset.cartId}`)
                    .then(res => res.json())
                    .then(result => {
                        if(result.success && result.data.code == 'WP_TRAVEL_REMOVED_CART_ITEM') {
                            ci.remove()
                            shoppingCart.dispatchEvent(new Event('wptcartchange'))
                        }
                    })
            }
        })
        edit && edit.addEventListener('click', () => {
            if (collapse.className.indexOf('active') < 0) {
                collapse.style.display = 'block'
                collapse.classList.add('active')
            } else {
                collapse.style.display = 'none'
                collapse.classList.remove('active')
            }
        })

        const wptCategories = ci.querySelectorAll('[data-wpt-category], [data-wpt-tx]')
        wptCategories && wptCategories.forEach(wc => {
            let _input = wc.querySelector('[data-wpt-category-count-input], [data-wpt-tx-count-input]')
            let spinners = wc.querySelectorAll('[data-wpt-count-up],[data-wpt-count-down]')
            spinners && spinners.forEach(sp => {
                sp.addEventListener('click', e => {
                    e.preventDefault()
                    if (typeof sp.dataset.wptCountUp != 'undefined') {
                        if (_input)
                            _input.value = parseInt(_input.value) + 1 < 0 ? 0 : parseInt(_input.value) + 1
                    }
                    if (typeof sp.dataset.wptCountDown != 'undefined') {
                        if (_input)
                            _input.value = parseInt(_input.value) - 1 < 0 ? 0 : parseInt(_input.value) - 1
                    }
                    shoppingCart.dispatchEvent(new Event('wptcartchange'))
                })
            })
        })
    })

    cartItemForms && cartItemForms.forEach(form => {
        const categoriesFields = form.querySelectorAll('.wp-travel-cart-category-qty')
        const tripExtrasFields = form.querySelectorAll('.wp-travel-cart-extras-qty')
        categoriesFields.forEach(cf => {
            cf.addEventListener('wptcartcategoryupdate', e => {
                console.log(e.detail)
            })
        })
        tripExtrasFields.forEach(txf => {
            txf.addEventListener('wptcarttxupdate', e => {
                console.log(e.detail)
            })
        })
        form.addEventListener('submit', e => {
            e.preventDefault()
            const cartId = form.dataset.cartId
            let pax = {}
            categoriesFields && categoriesFields.forEach(cf => {
                const categoryId = cf.dataset.categoryId
                const value = cf.value
                pax = { ...pax, [categoryId]: value }
                // return { categoryId: value }
            })

            let tripExtras = {}
            tripExtrasFields && tripExtrasFields.forEach(tx => {
                const txId = tx.dataset.tripExtraId
                const value = tx.value
                tripExtras = { ...tripExtras, [txId]: value }
            })

            const _data = {
                pax,
                wp_travel_trip_extras: {
                    id: Object.keys(tripExtras),
                    qty: Object.values(tripExtras)
                }
            }

            console.debug(_data)

            fetch(`${wp_travel.ajaxUrl}?action=wp_travel_update_cart_item&cart_id=${cartId}&_nonce=${wp_travel._nonce}`, {
                method: 'POST',
                body: JSON.stringify(_data)
            }).then(res => res.json())
                .then(result => {
                    if (result.success) {
                        let cartItem = result.data.cart.cart_items[cartId]
                        let tripData = cartItem.trip_data
                        let pricingId = cartItem.pricing_id
                        let pricing = tripData.pricings.find(p => p.id == pricingId)
                        let categories = pricing.categories

                        categoriesFields.forEach(cf => {
                            let category = categories.find(c => c.id == cf.dataset.categoryId)
                            let _event = new CustomEvent('wptcartcategoryupdate', { bubbles: true, detail: { cart: cartItem.trip, category } })
                            cf.dispatchEvent(_event)
                        })
                        tripExtrasFields.forEach(txf => {
                            let extras = pricing.trip_extras.find(tx => tx.id == txf.dataset.tripExtraId)
                            let _event = new CustomEvent('wptcarttxupdate', { bubbles: true, detail: { cart: cartItem.extras, extras } })
                            txf.dispatchEvent(_event)
                        })

                    }
                })
        })
    })


    // Coupon 
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
            e.target.disabled = true
            fetch(`${wp_travel.ajaxUrl}?action=wp_travel_apply_coupon&_nonce=${wp_travel._nonce}`, {
                method: 'POST',
                body: JSON.stringify({ couponCode: couponField.value })
            }).then(res => res.json())
                .then(result => {
                    if (result.success) {
                        couponField.toggleAttribute('readonly')
                        e.target.textContent = e.target.dataset.successL10n
                        e.target.style.backgroundColor = 'green'
                    } else {
                        couponField.focus()
                        toggleError(couponField, result.data[0].message)
                        e.target.disabled = false
                    }
                })
        }
    })

    // shoppingCart.dispatchEvent(new Event('wptcartchange'))
})()