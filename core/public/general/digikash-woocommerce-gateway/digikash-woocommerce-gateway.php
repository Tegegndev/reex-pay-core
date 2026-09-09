<?php
/*
Plugin Name: DigiKash WooCommerce Gateway
Plugin URI: https://digikash.coevs.com/
Description: Accept payments via DigiKash in WooCommerce.
Version: 1.0.0
Author: DigiKash
Author URI: https://digikash.coevs.com/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

add_filter('woocommerce_payment_gateways', 'digikash_add_gateway_class');
function digikash_add_gateway_class($gateways) {
    $gateways[] = 'WC_Gateway_DigiKash';
    return $gateways;
}

add_action('plugins_loaded', 'digikash_init_gateway_class');
function digikash_init_gateway_class() {
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_Gateway_DigiKash extends WC_Payment_Gateway {
        public function __construct() {
            $this->id = 'digikash';
            $this->icon = '';
            $this->has_fields = false;
            $this->method_title = 'DigiKash';
            $this->method_description = 'Pay securely using DigiKash.';

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'type' => 'checkbox',
                    'label' => 'Enable DigiKash Payment',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'DigiKash',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'Payment method description.',
                    'default' => 'Pay securely using DigiKash.',
                ),
                'merchant_id' => array(
                    'title' => 'Merchant ID',
                    'type' => 'text',
                ),
                'api_key' => array(
                    'title' => 'API Key',
                    'type' => 'text',
                ),
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);
            // TODO: Call DigiKash API and get payment URL
            $payment_url = 'https://digikash.coevs.com/payment/checkout?order_id=' . $order_id;

            return array(
                'result'   => 'success',
                'redirect' => $payment_url
            );
        }
    }
}
