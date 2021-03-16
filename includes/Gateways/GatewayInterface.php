<?php
namespace ChiliDevs\ContactForm7\Gateways;

interface GatewayInterface {

    /**
     * Send SMS via gateways
     *
     * @param array $form_data Hold form data
     * @param array $options Keep all gateway settings
     *
     * @return array
     */
    public function send( $form_data, $options );
}
