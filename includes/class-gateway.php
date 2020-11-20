<?php
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CF7_Gateway {

     /**
     * Instance of self
     *
     * @var CF7_Gateway
     */
    private static $instance = null;

    /**
     * Initializes the CF7_Gateway() class
     *
     * Checks for an existing CF7_Gateway() instance
     * and if it doesn't find one, creates it.
     *
     * @since 1.0.0
     *
     * @return Instance
     */
    public static function init() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Hold nexmo gateway process
     *
     * @since 1.0.0
     *
     * @return WP_Error | $response
     */
    public function nexmo( $form_data, $options ) {
        $api_key    = ! empty( $options['nexmo_api'] ) ? $options['nexmo_api']              : '';
        $api_secret = ! empty( $options['nexmo_api_secret'] ) ? $options['nexmo_api_secret']: '';
        $from_name  = ! empty( $options['nexmo_from_name'] ) ? $options['nexmo_from_name'] : 'VONAGE';

        if ( '' === $api_key || '' === $api_secret ) {
            return new WP_Error( 'no-gateway-settings', __( 'No API key or Secret found', 'cf7-sms' ), [ 'status' => 401 ] );
        }

        if ( empty( $form_data['number'] ) ) {
            return new WP_Error( 'no-number-found', __( 'No number found for sending SMS', 'cf7-sms' ), [ 'status' => 401 ] );
        }

        $basic  = new \Vonage\Client\Credentials\Basic( $api_key, $api_secret);
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS( $form_data['number'], $from_name, $form_data['body'] )
        );

        $message = $response->current();

        if ( $message->getStatus() == 0 ) {
            $response = [
                'message' => __( 'SMS sent successfully', 'cf7-sms' ),
                'response' => $message
            ];
            return $response;
        } else {
            $response = [
                'message' => __( 'The message failed with status:', 'cf7-sms' ) . $message->getStatus(),
                'response' => $message
            ];
            return $response;
        }
    }

}

