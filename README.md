# WooCommerce User Role Enhancer

🚀 **WooCommerce User Role Enhancer** extends WooCommerce by adding **30 customizable user roles** based on the default **Customer** role. It also supports random role assignment to existing customers and applies random coupons to orders via WP-CLI.

---

## 🔥 Key Features

- ✅ **30 New WooCommerce User Roles** with customer-level capabilities.
- ✅ **Random Role Assignment** to existing WooCommerce customers.
- ✅ **Random Coupon Application** to existing orders via WP-CLI.
- ✅ **Efficient Batch Processing** ideal for large WooCommerce stores.
- ✅ **Detailed Logging & WP-CLI Integration**.

---

## 🛠 Installation & Usage

### 1️⃣ **Install Plugin**

Upload the plugin folder to `wp-content/plugins/`, then activate via the WordPress admin panel or WP-CLI:

```bash
wp plugin activate woocommerce-user-role-enhancer
```

### 2️⃣ **Create User Roles**

Run the following WP-CLI command:

```bash
wp wc create-custom-roles
```

### 3️⃣ **Assign Roles to Existing Customers**

Use WP-CLI to randomly assign new roles to your customers:

```bash
wp wc assign-roles-to-customers
```

### 4️⃣ **Apply Random Coupons to Orders**

Apply a random coupon to a specified number of orders (`-1` applies to all orders):

```bash
wp wc coupon-apply-orders <limit>
```

**Examples:**

Apply coupons to 100 orders:

```bash
wp wc coupon-apply-orders 100
```

Apply coupons to all orders:

```bash
wp wc coupon-apply-orders -1
```

---

## 🎯 User Roles Created

- Bronze Member  
- Silver Member  
- Gold Member  
- Platinum Member  
- VIP Member  
- Premium Member  
- Wholesale Customer  
- Retail Customer  
- Regular Buyer  
- Loyal Customer  
- Elite Buyer  
- Online Shopper  
- Frequent Shopper  
- Guest Buyer  
- Local Customer  
- Global Customer  
- Basic Member  
- Standard Member  
- Advanced Member  
- Exclusive Member  
- Trial Member  
- Lifetime Member  
- Special Member  
- Preferred Member  
- Corporate Buyer  
- Institutional Buyer  
- Seasonal Buyer  
- Subscription Customer  
- Digital Buyer  
- Physical Goods Buyer  

All roles share the same permissions as the default WooCommerce **Customer** role.

---

## ⚡ WP-CLI Commands

| Command                                | Description                               |
| -------------------------------------- | ----------------------------------------- |
| `wp wc create-custom-roles`            | Creates custom WooCommerce user roles.    |
| `wp wc assign-roles-to-customers`      | Assigns random roles to existing customers. |
| `wp wc coupon-apply-orders <limit>`    | Applies random coupons to existing orders.|

---

## 📝 Logs

Logs for role assignment and coupon application processes are saved in:

- `wp-content/wc_assign_roles_log.txt`  
- `wp-content/wc_coupon_apply_log.txt`

---

## ℹ️ Plugin Information

- **Version:** 1.0.0  
- **Author:** Jahid  
- **Website:** [CoderPlus](https://coderplus.co)  
- **License:** GPLv2

