 set coupon,
        bindProceedToCheckout: function () {
            var cartItems = quote.getItems(); 
            var products = cartItems.map(function (item) {
                return {
                    'id': item.sku,
                    'name': item.name,
                    'price': item.price,
                    'quantity': item.qty
                };
            });
    
            window.dataLayer = window.dataLayer || [];

            window.dataLayer.push({
                'event': 'begin_checkout',
                'ecommerce': {
                    'checkout': {
                        'products': products
                    }
                }
            });
        }
        listmixin
,
        bindProceedToCheckout: function () {
            var cartItems = quote.getItems(); 
            var products = cartItems.map(function (item) {
                return {
                    'id': item.sku,
                    'name': item.name,
                    'price': item.price,
                    'quantity': item.qty
                };
            });
    
            window.dataLayer = window.dataLayer || [];

            window.dataLayer.push({
                'event': 'begin_checkout',
                'ecommerce': {
                    'checkout': {
                        'products': products
                    }
                }
            });
        }

var qtyValue = parseInt($('.qty.amtheme-qty').val());

        var productData = {
                product_sku: '<?= $escaper->escapeJs($_product->getSku()) ?>',
                product_name: '<?= $escaper->escapeJs($_product->getName()) ?>',
                product_price_value: '<?= $escaper->escapeJs($_product->getFinalPrice()) ?>',
                qty: qtyValue,
            };

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'event': 'view_item',
                'ecommerce': {
                    'items:': {
                        'products': [{
                            'id': productData.product_sku,
                            'name': productData.product_name,
                            'price': productData.product_price_value,
                            'quantity': productData.qty
                        }]
                    }
                }
            });
            list.phtml
 var productList = [];
            <?php foreach ($_productCollection as $_product): ?>
                <?php if ($_product->getIsAvailable()): ?>
                    productList.push({
                        id: '<?= $escaper->escapeJs($_product->getSku()) ?>',
                        name: '<?= $escaper->escapeJs($_product->getName()) ?>',
                        price: '<?= $escaper->escapeJs($_product->getFinalPrice()) ?>',
                        category: '<?= $escaper->escapeJs($curCat ? $curCat->getName() : '') ?>',
                    });
                <?php endif; ?>
            <?php endforeach; ?>

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'view_item_list',
                ecommerce: {
                    items: productList
                }
            });

