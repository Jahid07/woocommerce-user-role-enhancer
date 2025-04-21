<?php
/**
 * Plugin Name: WooCommerce User Role Enhancer
 * Plugin URI: https://coderplus.co
 * Description: Adds additional user roles based on WooCommerce's Customer role and assigns a default new role to existing customers via WP-CLI. Also applies a single random coupon to existing orders.
 * Version: 1.0.0
 * Author: Jahid
 * Author URI: https://coderplus.co
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Function to create new user roles
function wc_create_custom_roles() {
    // Get WooCommerce customer role capabilities
    $customer_role = get_role('customer');
    if (!$customer_role) {
        error_log("Customer role not found.");
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::warning("Customer role not found.");
        }
        return;
    }
    $customer_caps = $customer_role->capabilities;

    // Define new roles
    $new_roles = [
        'bronze_member', 'silver_member', 'gold_member', 'platinum_member', 'vip_member',

        'premium_member', 'wholesale_customer', 'retail_customer', 'regular_buyer', 'loyal_customer',
        'elite_buyer', 'online_shopper', 'frequent_shopper', 'guest_buyer', 'local_customer',
        'global_customer', 'basic_member', 'standard_member', 'advanced_member', 'exclusive_member',
        'trial_member', 'lifetime_member', 'special_member', 'preferred_member', 'corporate_buyer',
        'institutional_buyer', 'seasonal_buyer', 'subscription_customer', 'digital_buyer', 'physical_goods_buyer'
    ];

    foreach ($new_roles as $role) {
        if (!get_role($role)) {
            add_role($role, ucwords(str_replace('_', ' ', $role)), $customer_caps);
            error_log("Created role: {$role}");
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::log("Created role: {$role}");
            }
        } else {
            error_log("Role already exists: {$role}");
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::log("Role already exists: {$role}");
            }
        }
    }
}

// Function to assign roles to customers
function wc_assign_roles_to_customers() {
    $log_file = WP_CONTENT_DIR . '/wc_assign_roles_log.txt';

    // Define new roles (consistent with wc_create_custom_roles)
    $roles = [
        'bronze_member', 'silver_member', 'gold_member', 'platinum_member', 'vip_member',
        'premium_member', 'wholesale_customer', 'retail_customer', 'regular_buyer', 'loyal_customer',
        'elite_buyer', 'online_shopper', 'frequent_shopper', 'guest_buyer', 'local_customer',
        'global_customer', 'basic_member', 'standard_member', 'advanced_member', 'exclusive_member',
        'trial_member', 'lifetime_member', 'special_member', 'preferred_member', 'corporate_buyer',
        'institutional_buyer', 'seasonal_buyer', 'subscription_customer', 'digital_buyer', 'physical_goods_buyer'
    ];

    file_put_contents($log_file, "Starting role assignment process at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Starting role assignment process...");
    }

    // Fetch all customers
    $customers = get_users(['role' => 'customer']);

    $customer_count = count($customers);
    file_put_contents($log_file, "Total customers found: {$customer_count}\n", FILE_APPEND);
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Total customers found: {$customer_count}");
    }
    
    if (empty($customers)) {
        file_put_contents($log_file, "No customers found. Exiting.\n", FILE_APPEND);
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::warning("No customers found. Exiting.");
        }
        return;
    }

    foreach ($customers as $customer) {
        $random_role = $roles[array_rand($roles)]; // Pick a random role
        $user_id = $customer->ID;

        // Check if the role exists
        if (!get_role($random_role)) {
            file_put_contents($log_file, "Role '{$random_role}' does not exist. Skipping user #{$user_id}\n", FILE_APPEND);
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::log("Role '{$random_role}' does not exist. Skipping user #{$user_id}");
            }
            continue;
        }

        // Add the new role while keeping existing roles
        $user = new WP_User($user_id);
        $user->add_role($random_role);

        file_put_contents($log_file, "Added role '{$random_role}' to user #{$user_id} (kept customer role)\n", FILE_APPEND);
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::log("Added role '{$random_role}' to user #{$user_id} (kept customer role)");
        }
    }

    file_put_contents($log_file, "Role assignment completed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
}

/**
 * Apply a single random coupon to existing WooCommerce orders.
 *
 * ## OPTIONS
 *
 * <limit>
 * : The number of orders to process. Use -1 for all orders.
 *
 * ## EXAMPLES
 *
 * wp wc coupon-apply-orders 100
 * wp wc coupon-apply-orders -1
 */
function wp_wc_coupon_apply_orders($args, $assoc_args) {
    $log_file = WP_CONTENT_DIR . '/wc_coupon_apply_log.txt';

    // Get the limit parameter (default to -1 for all orders)
    $limit = isset($args[0]) ? (int) $args[0] : -1;

    if ($limit < -1 || $limit == 0) {
        WP_CLI::error("Invalid limit value. Use a positive number or -1 for all orders.");
        return;
    }

    file_put_contents($log_file, "Starting coupon application process at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    WP_CLI::log("Starting coupon application process at " . date('Y-m-d H:i:s'));

    // Dynamically fetch all coupons
    $coupon_posts = get_posts(array(
        'post_type'      => 'shop_coupon',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ));

    if (empty($coupon_posts)) {
        file_put_contents($log_file, "No coupons found in the system.\n", FILE_APPEND);
        WP_CLI::warning("No coupons found in the system.");
        return;
    }

    $coupon_codes = array_map(function ($coupon_post) {
        return strtolower(get_the_title($coupon_post->ID));
    }, $coupon_posts);

    $coupon_count = count($coupon_codes);
    file_put_contents($log_file, "Found {$coupon_count} coupons: " . implode(', ', $coupon_codes) . "\n", FILE_APPEND);
    WP_CLI::log("Found {$coupon_count} coupons: " . implode(', ', $coupon_codes));

    // Get orders with the specified limit
    $args = array(
        'limit' => $limit,
        'type'  => 'shop_order',
    );
    $orders = wc_get_orders($args);

    if (empty($orders)) {
        file_put_contents($log_file, "No orders found.\n", FILE_APPEND);
        WP_CLI::warning("No orders found.");
        return;
    }

    $order_count = count($orders);
    file_put_contents($log_file, "Found {$order_count} orders to process.\n", FILE_APPEND);
    WP_CLI::log("Found {$order_count} orders to process.");

    $progress = \WP_CLI\Utils\make_progress_bar('Processing orders', $order_count);

    // Loop through each order
    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $order_changed = false;

        // Get existing coupon codes applied to the order
        $existing_coupons = $order->get_coupon_codes();

        // Pick a random coupon
        $random_coupon_code = $coupon_codes[array_rand($coupon_codes)];
        $coupon = new WC_Coupon($random_coupon_code);

        if (!$coupon->get_id()) {
            file_put_contents($log_file, "Coupon '$random_coupon_code' does not exist for order #$order_id.\n", FILE_APPEND);
            WP_CLI::log("Coupon '$random_coupon_code' does not exist for order #$order_id.");
            $progress->tick();
            continue;
        }

        // Check if the coupon is already applied
        if (in_array($random_coupon_code, $existing_coupons)) {
            file_put_contents($log_file, "Coupon '$random_coupon_code' already applied to order #$order_id.\n", FILE_APPEND);
            WP_CLI::log("Coupon '$random_coupon_code' already applied to order #$order_id.");
            $progress->tick();
            continue;
        }

        // Apply the random coupon to the order
        $result = $order->apply_coupon($coupon);

        if (is_wp_error($result)) {
            file_put_contents($log_file, "Failed to apply coupon '$random_coupon_code' to order #$order_id: " . $result->get_error_message() . "\n", FILE_APPEND);
            WP_CLI::log("Failed to apply coupon '$random_coupon_code' to order #$order_id: " . $result->get_error_message());
            $progress->tick();
            continue;
        }

        $order_changed = true;
        file_put_contents($log_file, "Applied coupon '$random_coupon_code' to order #$order_id.\n", FILE_APPEND);
        WP_CLI::success("Applied coupon '$random_coupon_code' to order #$order_id.");

        // Recalculate totals and save if changed
        if ($order_changed) {
            $order->calculate_totals();
            $order->save();
            file_put_contents($log_file, "Updated totals for order #$order_id.\n", FILE_APPEND);
            WP_CLI::log("Updated totals for order #$order_id.");
        }

        $progress->tick();
    }

    $progress->finish();
    file_put_contents($log_file, "Coupon application process completed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    WP_CLI::success("Coupon application process completed at " . date('Y-m-d H:i:s'));
}

// Register WP-CLI Commands
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('wc create-custom-roles', function() {
        wc_create_custom_roles();
        WP_CLI::success("Custom roles created successfully.");
    });

    WP_CLI::add_command('wc assign-roles-to-customers', function() {
        wc_assign_roles_to_customers();
        WP_CLI::success("Roles assigned to customers successfully.");
    });

    WP_CLI::add_command('wc coupon-apply-orders', 'wp_wc_coupon_apply_orders');
}