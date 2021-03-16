<?php
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get option value for settings
 *
 * @since 1.0.0
 *
 * @param string $option
 * @param string $section
 * @param mixed $default
 *
 * @return mixed
 */
function cf7_sms_get_option( $option, $section, $default = '' ) {
    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/**
 * Get sms class name
 *
 * @param string $class_name SMS Class name
 *
 * @return array
 */
function cf7_sms_class_mapping( $class_name = '' ) {
    $classes = apply_filters( 'cf7_sms_class_map', [
        'nexmo'     => ChiliDevs\ContactForm7\Gateways\Vonage::class,
        'clicksend' => ChiliDevs\ContactForm7\Gateways\ClickSend::class,
    ] );

    return isset( $classes[ $class_name ] ) ? $classes[ $class_name ] : '';
}
