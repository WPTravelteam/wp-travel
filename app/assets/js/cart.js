function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
/*WP Travel Cart and Chekcout JS.*/

function GetConvertedPrice(price) {
  var conversionRate = 'undefined' !== typeof _wp_travel && 'undefined' !== typeof _wp_travel.conversion_rate ? _wp_travel.conversion_rate : 1;
  var _toFixed = 'undefined' !== typeof _wp_travel && 'undefined' !== typeof _wp_travel.number_of_decimals ? _wp_travel.number_of_decimals : 2;
  conversionRate = parseFloat(conversionRate).toFixed(2);
  return parseFloat(price * conversionRate).toFixed(_toFixed);
}
var wp_travel_cart = {};
wp_travel_cart.format = function (_num) {
  var style = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'currency';
  var _wp_travel2 = wp_travel,
    currency = _wp_travel2.currency,
    _currencySymbol = _wp_travel2.currency_symbol,
    currencyPosition = _wp_travel2.currency_position,
    decimalSeparator = _wp_travel2.decimal_separator,
    _toFixed = _wp_travel2.number_of_decimals,
    kiloSeparator = _wp_travel2.thousand_separator;
  var regEx = new RegExp("\\d(?=(\\d{3})+\\".concat(decimalSeparator, ")"), 'gi');
  var replaceWith = "$&".concat(kiloSeparator);
  var _formattedNum = parseFloat(_num).toFixed(_toFixed).replace(/\./, decimalSeparator).replace(regEx, replaceWith);
  // _formattedNum = String(_formattedNum).replace(/\./, ',')
  if (style == 'decimal') {
    return _formattedNum;
  }
  var positions = {
    'left': "".concat(_currencySymbol, "<span>").concat(_formattedNum, "</span>"),
    'left_with_space': "".concat(_currencySymbol, " <span>").concat(_formattedNum, "</span>"),
    'right': "<span>".concat(_formattedNum, "</span>").concat(_currencySymbol),
    'right_with_space': "<span>".concat(_formattedNum, "</span> ").concat(_currencySymbol)
  };
  return positions[currencyPosition];
};
wp_travel_cart.timeout = function (promise, ms) {
  return new Promise(function (resolve, reject) {
    setTimeout(function () {
      reject(new Error("request timeout"));
    }, ms);
    resolve(promise.then(resolve, reject));
  });
};
var wptravelcheckout = function wptravelcheckout(shoppingCart) {
  var bookNowBtn = document.getElementById('wp-travel-book-now');
  bookNowBtn && bookNowBtn.addEventListener('wptcartchange', function (e) {
    e.target.disabled = true;
  });
  bookNowBtn && bookNowBtn.addEventListener('click', function (e) {});
  if (!shoppingCart) {
    return;
  }
  var cartItems = shoppingCart && shoppingCart.querySelectorAll('[data-cart-id]');
  if (cartItems && cartItems.length <= 0) {
    return;
  }
  var toggleBookNowBtn = function toggleBookNowBtn() {
    var dirtyItems = shoppingCart.querySelectorAll('[data-dirty]');
    if (!bookNowBtn) {
      return;
    }
    if (!!dirtyItems && dirtyItems.length > 0) {
      bookNowBtn.disabled = true;
    } else {
      bookNowBtn.disabled = false;
    }
  };
  var toggleCartLoader = function toggleCartLoader(on) {
    if (on) {
      cartLoader.removeAttribute('style');
    } else {
      cartLoader.style.display = 'none';
    }
  };
  // let cart = {}
  var cartLoader = shoppingCart.querySelector('.wp-travel-cart-loader');
  cartLoader && toggleCartLoader(true);
  wp_travel && wp_travel_cart.timeout(fetch("".concat(wp_travel.ajaxUrl, "?action=wp_travel_get_cart&_nonce=").concat(wp_travel._nonce)).then(function (res) {
    res.json().then(function (result) {
      toggleCartLoader();
      if (result.success && result.data.code === 'WP_TRAVEL_CART') {
        if (result.data.cart) {
          wp_travel_cart.cart = result.data.cart;
          Object.freeze(wp_travel_cart.cart);
        }
      }
    });
  }), 10000)["catch"](function (error) {
    alert('[X] Request Timeout!');
    toggleCartLoader();
  });
  function dynamicSort(property) {
    var sortOrder = 1;
    if (property[0] === "-") {
      sortOrder = -1;
      property = property.substr(1);
    }
    return function (a, b) {
      /* next line works with strings and numbers, 
       * and you may want to customize it to your needs
       */
      var result = a[property] < b[property] ? -1 : a[property] > b[property] ? 1 : 0;
      return result * sortOrder;
    };
  }
  // On Pax Change event listner.
  var updateItem = function updateItem(id) {
    var _data = {};
    var tripTotalWOExtras = 0,
      tripTotalPartialWOExtras = 0,
      extrasTotal = 0;
    var tripTotal = 0;
    var item = wp_travel_cart.cart && wp_travel_cart.cart.cart_items && wp_travel_cart.cart.cart_items[id];
    var itemNode = shoppingCart.querySelector("[data-cart-id=\"".concat(id, "\"]"));
    var pricing = item.trip_data.pricings.find(function (p) {
      return p.id == parseInt(item.pricing_id);
    });
    var categories = pricing.categories;
    var _tripExtras = pricing.trip_extras;
    var wptCTotals = itemNode.querySelectorAll('[data-wpt-category-count]');
    var payoutPercentage = item.trip_data && item.trip_data.minimum_partial_payout_percent;

    // Categories.
    var formGroupsCategory = itemNode.querySelectorAll('[data-wpt-category]');
    var totalPax = 0;
    formGroupsCategory.forEach(function (fg) {
      var tempCatCount = fg.querySelector('[data-wpt-category-count-input]');
      var tempCount = tempCatCount && parseInt(tempCatCount.value) || 0;
      totalPax += tempCount;
    });
    formGroupsCategory.forEach(function (fg) {
      var categoryTotalContainer = fg.querySelector('[data-wpt-category-total]');
      var dataCategoryCount = fg.querySelector('[data-wpt-category-count-input]');
      var dataCategoryPrice = fg.querySelector('[data-wpt-category-price]');
      var _category = categories.find(function (c) {
        return c.id == parseInt(fg.dataset.wptCategory);
      });
      var _price = _category && _category.is_sale ? parseFloat(_category['sale_price']) : parseFloat(_category['regular_price']);
      // Update price for default pricing without group price.
      _price = GetConvertedPrice(_price); // Multiple currency support on edit cart.
      dataCategoryPrice.innerHTML = wp_travel_cart.format(_price);
      // End of Update price for default pricing without group price.
      var _count = dataCategoryCount && parseInt(dataCategoryCount.value) || 0;
      if ('undefined' != typeof pricing.has_group_price && pricing.has_group_price && pricing.group_prices && pricing.group_prices.length > 0) {
        var groupPrices = pricing.group_prices;
        groupPrices = groupPrices.sort(dynamicSort('max_pax'));
        var group_price = groupPrices.find(function (gp) {
          return parseInt(gp.min_pax) <= totalPax && parseInt(gp.max_pax) >= totalPax;
        });
        if (group_price && group_price.price) {
          _price = parseFloat(group_price.price);
          _price = GetConvertedPrice(_price); // Multiple currency support on edit cart.
          if (dataCategoryPrice) dataCategoryPrice.innerHTML = wp_travel_cart.format(_price);
        }
      } else if (_category.has_group_price) {
        var _groupPrice = _category.group_prices.find(function (gp) {
          return _count >= parseInt(gp.min_pax) && _count <= parseInt(gp.max_pax);
        });
        if (_groupPrice && _groupPrice.price) {
          _price = _groupPrice.price;
          _price = GetConvertedPrice(_price); // Multiple currency support on edit cart.
        }

        if (dataCategoryPrice) dataCategoryPrice.innerHTML = wp_travel_cart.format(_price);
      }
      var categoryTotal = _category.price_per == 'group' ? _count > 0 && _price || 0 : _price * _count;
      wptCTotals && wptCTotals.forEach(function (wpct) {
        if (wpct.dataset.wptCategoryCount == fg.dataset.wptCategory) wpct.innerHTML = _count;
      });
      if (categoryTotalContainer) categoryTotalContainer.innerHTML = wp_travel_cart.format(categoryTotal);
      tripTotal += parseFloat(categoryTotal);
      tripTotalWOExtras += parseFloat(categoryTotal);
      tripTotalPartialWOExtras += parseFloat(categoryTotal) * parseFloat(payoutPercentage) / 100;
    });

    // Extras.
    var formGroupsTx = itemNode.querySelectorAll('[data-wpt-tx]');
    formGroupsTx && formGroupsTx.forEach(function (tx) {
      var _extra = _tripExtras.find(function (c) {
        return c.id == parseInt(tx.dataset.wptTx);
      });
      if (!_extra.tour_extras_metas) {
        return;
      }
      var txTotalContainer = tx.querySelector('[data-wpt-tx-total]');
      var datatxCount = tx.querySelector('[data-wpt-tx-count-input]');
      var dataCategoryExtPrice = tx.querySelector('[data-wpt-tx-price]');
      var _price = _extra.is_sale && _extra.tour_extras_metas.extras_item_sale_price || _extra.tour_extras_metas.extras_item_price;
      _price = GetConvertedPrice(_price); // Multiple currency support on edit cart.
      dataCategoryExtPrice.innerHTML = wp_travel_cart.format(_price);
      var _count = datatxCount && datatxCount.value || 0;
      var itemTotal = parseFloat(_price) * parseInt(_count);
      if (txTotalContainer) txTotalContainer.innerHTML = wp_travel_cart.format(itemTotal);
      tripTotal += itemTotal;
      extrasTotal += itemTotal;
    });
    _data = {
      tripTotalWOExtras: tripTotalWOExtras,
      tripTotalPartialWOExtras: tripTotalPartialWOExtras,
      extrasTotal: extrasTotal,
      tripTotal: tripTotal
    };
    itemNode.querySelector('[data-wpt-item-total]').innerHTML = wp_travel_cart.format(tripTotal);
    return _data;
  };
  shoppingCart && shoppingCart.addEventListener('wptcartchange', function (e) {
    var cartTotal = 0,
      tripTotalWOExtras = 0,
      txTotal = 0,
      tripTotalPartialWOExtras = 0;
    var cartTotalContainers = document.querySelectorAll('[data-wpt-cart-net-total]');
    var cartTotalPartialContainers = document.querySelectorAll('[data-wpt-cart-partial-total]');
    var cartSubtotalContainer = e.target.querySelector('[data-wpt-cart-subtotal]');
    var cartDiscountContainer = e.target.querySelector('[data-wpt-cart-discount]');
    var cartTaxContainer = e.target.querySelector('[data-wpt-cart-tax]');
    // let cartTaxContainer = e.target.querySelector('[data-wpt-cart-tax]')
    var _cartItems = e.target.querySelectorAll('[data-cart-id]');
    _cartItems && _cartItems.forEach(function (ci) {
      var totals = updateItem(ci.dataset.cartId);
      cartTotal += totals.tripTotal;
      tripTotalWOExtras += totals.tripTotalWOExtras;
      tripTotalPartialWOExtras += totals.tripTotalPartialWOExtras;
      txTotal += totals.extrasTotal;
    });
    if (cartSubtotalContainer) cartSubtotalContainer.innerHTML = wp_travel_cart.format(cartTotal);

    // let fullTotalContainer = e.target.querySelector('[data-wpt-cart-full-total]')
    if (e.detail && e.detail.coupon || wp_travel_cart.cart.coupon && wp_travel_cart.cart.coupon.coupon_id) {
      var coupon = e.detail && e.detail.coupon || wp_travel_cart.cart.coupon;
      var _cValue = coupon.value && parseInt(coupon.value) || 0;
      // fullTotalContainer.innerHTML = wp_travel_cart.format(cartTotal)
      if (cartDiscountContainer) {
        cartDiscountContainer.innerHTML = coupon.type == 'fixed' ? '- ' + wp_travel_cart.format(_cValue) : '- ' + wp_travel_cart.format(cartTotal * _cValue / 100);
        cartDiscountContainer.closest('[data-wpt-extra-field]').removeAttribute('style');
      }
      cartTotal = coupon.type == 'fixed' ? cartTotal - _cValue : cartTotal * (100 - _cValue) / 100;
    }
    if (wp_travel_cart.cart.total.discount <= 0) {
      // fullTotalContainer.innerHTML = ''
      cartDiscountContainer.closest('[data-wpt-extra-field]').style.display = 'none';
    }
    if (wp_travel_cart.cart.tax) {
      if (cartTaxContainer) cartTaxContainer.innerHTML = '+ ' + wp_travel_cart.format(cartTotal * parseInt(wp_travel_cart.cart.tax) / 100);
      cartTotal = cartTotal * (100 + parseInt(wp_travel_cart.cart.tax)) / 100;
    }
    if (cartTotalContainers) {
      cartTotalContainers.forEach(function (ctt) {
        return ctt.innerHTML = wp_travel_cart.format(cartTotal);
      });
    }
    if (cartTotalPartialContainers) {
      cartTotalPartialContainers.forEach(function (ctpc) {
        var _partialTotal = tripTotalPartialWOExtras + txTotal;
        if (wp_travel_cart.cart.tax) {
          _partialTotal = _partialTotal * (100 + parseFloat(wp_travel_cart.cart.tax)) / 100;
        }
        ctpc.innerHTML = wp_travel_cart.format(_partialTotal);
      });
    }

    // cartTotalContainer.innerHTML = wp_travel_cart.format(cartTotal)
    var cartItemsCountContainer = e.target.querySelector('[data-wpt-cart-item-count]');
    if (cartItemsCountContainer) cartItemsCountContainer.innerHTML = _cartItems.length;
  });
  cartItems && cartItems.forEach(function (ci) {
    var edit = ci.querySelector('a.edit');
    var collapse = ci.querySelector('.update-fields-collapse');
    var _deleteBtn = ci.querySelector('.del-btn');
    var loader = ci.querySelector('.wp-travel-cart-loader');
    _deleteBtn && _deleteBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (confirm(_deleteBtn.dataset.l10n)) {
        toggleCartLoader(true);
        wp_travel_cart.timeout(fetch("".concat(wp_travel.ajaxUrl, "?action=wp_travel_remove_cart_item&_nonce=").concat(wp_travel._nonce, "&cart_id=").concat(ci.dataset.cartId)).then(function (res) {
          return res.json();
        }).then(function (result) {
          if (result.success && result.data.code == 'WP_TRAVEL_REMOVED_CART_ITEM') {
            // if (result.data.cart && result.data.cart.length <= 0) {
            // }
            jQuery(document.body).trigger('wptravel_removed_cart_item', [result.data, ci.dataset.cartId, _deleteBtn]);
            window.location.reload();
            wp_travel_cart.cart = result.data.cart;
            var total = result.data.cart.total;
            if (wp_travel.payment) {
              wp_travel.payment.trip_price = parseFloat(total.total);
              wp_travel.payment.payment_amount = parseFloat(total.total_partial);
            }
            ci.remove();
            shoppingCart.dispatchEvent(new Event('wptcartchange'));
            toggleCartLoader();
          }
        }), 10000)["catch"](function (error) {
          alert('[X] Request Timeout!');
          toggleCartLoader();
        });
      }
    });
    edit && edit.addEventListener('click', function (e) {
      if (collapse.className.indexOf('active') < 0) {
        collapse.style.display = 'block';
        collapse.classList.add('active');
      } else {
        collapse.style.display = 'none';
        collapse.classList.remove('active');
      }
      if (collapse.className.indexOf('active') < 0) {
        return;
      }
      var cart_id = e.target.dataset.wptTargetCartId;
      var cart = wp_travel_cart.cart.cart_items && wp_travel_cart.cart.cart_items[cart_id] || {};
      if (cart.trip_data && cart.trip_data.inventory && cart.trip_data.inventory.enable_trip_inventory === 'yes') {
        var qs = '';
        var pricing_id = cart.pricing_id || 0;
        qs += pricing_id && "pricing_id=".concat(pricing_id) || '';
        var trip_id = cart.trip_data && cart.trip_data.id || 0;
        qs += trip_id && "&trip_id=".concat(trip_id) || '';
        var trip_time = cart.trip_time;
        qs += trip_time && "&trip_time=".concat(trip_time) || '';
        if (cart.arrival_date && new Date(cart.arrival_date).toString().toLowerCase() != 'invalid date') {
          var _date = new Date(cart.arrival_date);
          var _year = _date.getFullYear();
          var _month = _date.getMonth() + 1;
          _month = String(_month).padStart(2, '0');
          var _day = String(_date.getDate()).padStart(2, '0');
          _date = "".concat(_year, "-").concat(_month, "-").concat(_day);
          qs += _date && "&selected_date=".concat(_date) || '';
        }
        loader.removeAttribute('style');
        wp_travel_cart.timeout(fetch("".concat(wp_travel.ajaxUrl, "?").concat(qs, "&action=wp_travel_get_inventory&_nonce=").concat(wp_travel._nonce)).then(function (res) {
          return res.json().then(function (result) {
            loader.style.display = 'none';
            if (result.success && result.data.code === 'WP_TRAVEL_INVENTORY_INFO') {
              if (result.data.inventory.length > 0) {
                var inventory = result.data.inventory[0];
                ci.querySelectorAll('[data-wpt-category-count-input]').forEach(function (_ci) {
                  return _ci.max = inventory.pax_available;
                });
              }
            }
          });
        }))["catch"](function (error) {
          alert('[X] Request Timeout!');
          loader.style.display = 'none';
        });
      }
    });
    var wptCategories = ci.querySelectorAll('[data-wpt-category], [data-wpt-tx]');
    wptCategories && wptCategories.forEach(function (wc) {
      var _input = wc.querySelector('[data-wpt-category-count-input], [data-wpt-tx-count-input]');
      var spinners = wc.querySelectorAll('[data-wpt-count-up],[data-wpt-count-down]');
      spinners && spinners.forEach(function (sp) {
        sp.addEventListener('click', function (e) {
          e.preventDefault();
          var paxSum = 0;
          ci.querySelectorAll('[data-wpt-category-count-input]').forEach(function (input) {
            paxSum += parseInt(input.value);
          });
          if (typeof sp.dataset.wptCountUp != 'undefined') {
            if (_input && _input.dataset.wptCategoryCountInput) {
              var _inputvalue = parseInt(_input.value) + 1 < 0 ? 0 : parseInt(_input.value) + 1;
              if (paxSum + 1 <= parseInt(_input.max) && _inputvalue >= parseInt(_input.min)) {
                _input.value = _inputvalue;
              }
            } else {
              _input.value = parseInt(_input.value) + 1;
            }
          }
          if (typeof sp.dataset.wptCountDown != 'undefined') {
            if (_input && _input.dataset.wptCategoryCountInput) {
              var _inputvalue2 = parseInt(_input.value) - 1 < 0 ? 0 : parseInt(_input.value) - 1;
              if (paxSum - 1 <= parseInt(_input.max) && _inputvalue2 >= parseInt(_input.min)) {
                _input.value = _inputvalue2;
              }
            } else {
              _input.value = parseInt(_input.value) - 1 < parseInt(_input.min) ? _input.min : parseInt(_input.value) - 1;
            }
          }
          shoppingCart.dispatchEvent(new Event('wptcartchange'));
          bookNowBtn && bookNowBtn.dispatchEvent(new Event('wptcartchange'));
          ci.querySelector('form [type="submit"]').disabled = false;
          ci.querySelector('h5 a').style.color = 'orange';
        });
      });
    });
  });
  cartItems && cartItems.forEach(function (ci) {
    var loader = ci.querySelector('.wp-travel-cart-loader');
    var categories = ci.querySelectorAll('[data-wpt-category]');
    var tripExtras = ci.querySelectorAll('[data-wpt-tx]');
    var _form = ci.querySelector('form');
    _form.addEventListener('submit', function (e) {
      e.preventDefault();
      var _btn = _form.querySelector('[type="submit"]');
      _btn.disabled = true;
      loader.removeAttribute('style');
      var cartId = ci.dataset.cartId;
      var pax = {};
      categories && categories.forEach(function (cf) {
        var _input = cf.querySelector('[data-wpt-category-count-input]');
        var categoryId = cf.dataset.wptCategory;
        var value = _input && _input.value;
        pax = _objectSpread(_objectSpread({}, pax), {}, _defineProperty({}, categoryId, value));
      });
      var txCounts = {};
      tripExtras && tripExtras.forEach(function (tx) {
        var _input = tx.querySelector('[data-wpt-tx-count-input]');
        var txId = tx.dataset.wptTx;
        var value = _input && _input.value;
        txCounts = _objectSpread(_objectSpread({}, txCounts), {}, _defineProperty({}, txId, value));
      });
      var _data = {
        pax: pax,
        wp_travel_trip_extras: {
          id: Object.keys(txCounts),
          qty: Object.values(txCounts)
        }
      };
      wp_travel_cart.timeout(fetch("".concat(wp_travel.ajaxUrl, "?action=wp_travel_update_cart_item&cart_id=").concat(cartId, "&_nonce=").concat(wp_travel._nonce), {
        method: 'POST',
        body: JSON.stringify(_data)
      }).then(function (res) {
        return res.json();
      }).then(function (result) {
        loader.style.display = 'none';
        if (result.success) {
          wp_travel_cart.cart = result.data.cart;
          var totalData = result.data.cart && 'undefined' != typeof result.data.cart.total ? result.data.cart.total : [];
          var trip_total = 'undefined' != typeof totalData.total ? totalData.total : 0;
          var trip_total_partial = 'undefined' != typeof totalData.total_partial ? totalData.total_partial : 0;
          if (wp_travel.payment) {
            wp_travel.payment.trip_price = parseFloat(trip_total);
            wp_travel.payment.payment_amount = parseFloat(trip_total_partial);
          }
          toggleBookNowBtn();
          ci.querySelector('h5 a').removeAttribute('style');
          location.reload(); // For quick fix on multiple traveller field case.
        } else {
          _btn.disabled = false;
        }
      }), 10000)["catch"](function (error) {
        alert('[X] Request Timeout!');
        loader.style.display = 'none';
        _btn.disabled = false;
      });
    });
  });
  var paymentModeInput = document.getElementById('wp-travel-payment-mode');
  paymentModeInput && paymentModeInput.addEventListener('change', function (e) {
    var basket = document.querySelector('#shopping-cart');
    var container = basket && basket.querySelector('[data-wpt-cart-partial-total]') && basket.querySelector('[data-wpt-cart-partial-total]').closest('p');
    var item_container = basket && basket.querySelectorAll('[data-wpt-trip-partial-total]') && basket.querySelectorAll('[data-wpt-trip-partial-total]');
    var total_container = basket && basket.querySelectorAll('.wp-travel-payable-amount') && basket.querySelector('.wp-travel-payable-amount');
    var partial_total_container = basket && basket.querySelectorAll('[data-wpt-trip-partial-gross-total]') && basket.querySelector('[data-wpt-trip-partial-gross-total]');
    if ('partial' === e.target.value) {
      if (container && container.style.display == 'none') {
        container.removeAttribute('style');
      }
      item_container.forEach(function (el) {
        return el.removeAttribute('style');
      });
      partial_total_container.removeAttribute('style');
      partial_total_container.classList.add("selected-payable-amount");
      total_container.classList.remove("selected-payable-amount");
    } else {
      if (container) {
        container.style.display = 'none';
      }
      item_container.forEach(function (el) {
        return el.style.display = "none";
      });
      partial_total_container.style.display = 'none';
      partial_total_container.classList.remove("selected-payable-amount");
      total_container.classList.add("selected-payable-amount");
    }
  });

  // Coupon
  var couponForm = document.getElementById('wp-travel-coupon-form');
  var couponBtn = couponForm && couponForm.querySelector('button');
  var couponField = couponForm && couponForm.querySelector('.coupon-input-field');
  couponField && couponField.addEventListener('keyup', function (e) {
    toggleError(e.target);
    e.target.value.length > 0 && e.target.removeAttribute('style');
  });
  var toggleError = function toggleError(el, message) {
    if (message) {
      var p = document.createElement('p');
      p.classList.add('error');
      p.innerHTML = message;
      el.after(p);
    } else {
      var error = el.parentElement.querySelector('.error');
      error && error.remove();
    }
  };
  couponBtn && couponField && couponBtn.addEventListener('click', function (e) {
    e.preventDefault();
    if (couponField.value.length <= 0) {
      couponField.style.borderColor = 'red';
      couponField.focus();
    } else {
      toggleCartLoader(true);
      e.target.disabled = true;
      wp_travel_cart.timeout(fetch("".concat(wp_travel.ajaxUrl, "?action=wp_travel_apply_coupon&_nonce=").concat(wp_travel._nonce), {
        method: 'POST',
        body: JSON.stringify({
          couponCode: couponField.value
        })
      }).then(function (res) {
        return res.json();
      }).then(function (result) {
        toggleCartLoader();
        if (result.success) {
          wp_travel_cart.cart = result.data.cart;
          couponField.toggleAttribute('readonly');
          e.target.innerHTML = e.target.dataset.successL10n;
          e.target.style.backgroundColor = 'green';
          shoppingCart.dispatchEvent(new CustomEvent('wptcartchange', {
            detail: {
              coupon: result.data.cart.coupon
            }
          }));
          location.reload();
        } else {
          couponField.focus();
          toggleError(couponField, result.data[0].message);
          e.target.disabled = false;
        }
      }), 10000)["catch"](function (error) {
        alert('[X] Request Timeout!');
        toggleCartLoader();
      });
    }
  });
};
document.getElementById('shopping-cart') && wptravelcheckout(document.getElementById('shopping-cart'));
