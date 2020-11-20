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
