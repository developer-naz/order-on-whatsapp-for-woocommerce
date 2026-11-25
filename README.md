<div align="center">

# 💬 Order on WhatsApp for WooCommerce

**Let customers order on WhatsApp with one tap: product, size, quantity and price already written for them.**

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759B?style=flat&logo=wordpress&logoColor=white)
![WooCommerce](https://img.shields.io/badge/WooCommerce-8.0%2B-96588A?style=flat&logo=woocommerce&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat&logo=php&logoColor=white)
![Block themes](https://img.shields.io/badge/block%20themes-supported-success?style=flat)
![Tests](https://img.shields.io/badge/end--to--end%20tests-passing-brightgreen?style=flat)
![License](https://img.shields.io/badge/license-GPL--2.0-orange?style=flat)

</div>

---

## ✨ Features

- 🛍️ **Product page button** with the product name, **chosen variation** (e.g. `Size: M`), **quantity**, price and link
- 🔒 Variable products: the button waits until the customer picks the options, and says so if they tap too early
- 🏪 **Shop page buttons** under every product (optional)
- 🛒 **Cart button**: sends the whole cart (`• Panjabi × 2 = ৳1,800` …) and the total. Always up to date, also with the **Cart block**
- 🟢 **Floating chat button** on every page (optional)
- 🚫 **WhatsApp-only mode**: hide the normal "Add to cart" buttons
- ✍️ Editable message templates with placeholders, button texts and color
- 🇧🇩 Bangladeshi numbers like `01712-345678` are converted to `8801712345678` automatically
- 🧩 Shortcode `[order_on_whatsapp]` for page builders

Example message:

~~~
Hello! I want to order:

*Polo Shirt*
Size: M
Quantity: 2
Price: ৳650.00

Link: https://yourshop.com/product/polo-shirt/
~~~

---

## 🚀 Setup

1. **Code → Download ZIP**, then **Plugins → Add New → Upload Plugin** and activate.
2. Go to **WooCommerce → Settings → WhatsApp**.
3. Enter your **WhatsApp number** and choose where the buttons appear. Done.

The buttons only appear once a number is set.

### Placeholders

| Message | Placeholders |
|---|---|
| Product | `{product_name}` `{variation}` `{quantity}` `{price}` `{sku}` `{url}` `{site_name}` |
| Cart | `{cart_items}` `{cart_total}` `{site_name}` |

Use `*text*` for **bold** in WhatsApp. Lines left empty (e.g. `{variation}` on a simple product) are removed automatically.

### Shortcode

~~~
[order_on_whatsapp]            ← current product (inside a product page or template)
[order_on_whatsapp id="123"]   ← a specific product
[order_on_whatsapp cart="yes"] ← the cart button
~~~

---

## 🧑‍💻 For developers

~~~php
// Hide the button for one category.
add_filter( 'oow_show_product_button', function ( $show, $product ) {
	return $show && ! has_term( 'wholesale', 'product_cat', $product->get_id() );
}, 10, 2 );

// Add a line to every cart message.
add_filter( 'oow_cart_message', fn( $message ) => $message . "\n\nPayment: Cash on Delivery" );
~~~

Hooks: `oow_show_product_button`, `oow_product_message`, `oow_cart_message`, `oow_option_{key}`.

---

## 🧪 Tested

Tested end-to-end in a real browser on WordPress 6.8 + WooCommerce 10.3 with the **Twenty Twenty-Five block theme**:

- Simple product: button, number converted to `8801712345678`, name / quantity / price / link in the message, no leftover placeholders
- Changing quantity to 3 updates the message
- Variable product: disabled until a size is chosen, shows a note if tapped early, then includes `Size: M` and the M price
- Shop page buttons, floating button
- Cart button redirects to WhatsApp with `Test T-Shirt × 2` and the total

---

## 📁 Files

~~~
order-on-whatsapp-for-woocommerce/
├── order-on-whatsapp-for-woocommerce.php
├── includes/
│   ├── functions.php     # Settings, number, message building
│   ├── frontend.php      # Buttons, shortcode, cart redirect, styles
│   └── settings.php      # WooCommerce → Settings → WhatsApp
├── assets/js/order-on-whatsapp.js   # Live quantity / variation in the message
├── CHANGELOG.md
└── LICENSE
~~~

## 🗺️ Roadmap

- [ ] Click statistics per product
- [ ] Several agents / numbers (rotate or by category)
- [ ] Working hours (show the button only when someone can reply)

## 📄 License

[GPL-2.0-or-later](LICENSE). WhatsApp is a trademark of its owner; this plugin is not affiliated with or endorsed by WhatsApp.

---

<div align="center">

### 👨‍💻 Built by Md Nazmul Islam (Developer Naz)

[![LinkedIn](https://img.shields.io/badge/LinkedIn-developernaz-0A66C2?style=flat&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/developernaz/)
[![GitHub](https://img.shields.io/badge/GitHub-developer--naz-181717?style=flat&logo=github&logoColor=white)](https://github.com/developer-naz)

More: [BD Delivery Charge](https://github.com/developer-naz/bd-delivery-charge-for-woocommerce) · [Fake Order Blocker](https://github.com/developer-naz/woocommerce-fake-order-blocker) · [Low Stock Alert](https://github.com/developer-naz/low-stock-alert-for-woocommerce)

</div>
