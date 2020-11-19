<?php
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CF7_SMS_Form_Settings {

    public function __construct() {
        add_filter( 'wpcf7_editor_panels', [ $this, 'add_settings_panel' ], 10 );
        add_action( 'wpcf7_after_save', [ $this, 'save_sms_data' ] );
        add_action( 'wpcf7_mail_sent', array( $this, 'send_sms' ) );
    }

    public function add_settings_panel( $panels ) {
        $panels['sms-settings'] = array(
            'title' => __( 'SMS Settings', 'cf7-sms' ),
            'callback' => array( $this, 'editor_sms_settings' )
        );

        return $panels;
    }

    public function editor_sms_settings( $form ) {
        $options = get_post_meta( $form->id(), '_sms_settings', true );
        ?>
        <div id="sms-sortables" class="meta-box-sortables ui-sortable">
            <div id="maildiv" class="postbox ">
                <div class="handlediv" title="Click to toggle"><br></div>
                <h3 class="hndle" style="padding:12px;"><span><?php _e( 'Admin SMS Settings', 'cf7-sms' ); ?></span></h3>
                <div class="inside">
                    <div class="mail-fields">
                        <div class="half-left">
                            <div class="mail-field">
                                <label for="wpcf7-sms-recipient"><?php _e( 'Admin Phone Number:', 'cf7-sms' ); ?></label><br>
                                <input type="text" id="wpcf7-sms-recipient" name="cf7_sms[phone]" class="large-text" size="70" value="<?php echo ! empty( $options['phone'] ) ? esc_attr( $options['phone'] ) : ''; ?>">
                                <p><i><?php printf( 'Insert your phone number (e.g.: <code>%s</code> )', '+8801673322116' ) ?></i></p>
                            </div>
                        </div>
                        <br>
                        <div class="half-right">
                            <div class="mail-field">
                                <label for="wpcf7-mail-body">Enter your Message Format:</label><br>
                                <p>
                                    <?php echo esc_html( __( "In the following fields, you can use these mail-tags:", 'contact-form-7' ) ); ?><br />
                                    <?php $form->suggest_mail_tags( 'sms-settings' ); ?></legend>
                                </p>
                                <textarea id="wpcf7-mail-body" name="cf7_sms[message]" class="large-text" rows="8"><?php echo ! empty( $options['message'] ) ? esc_attr( $options['message'] ) : ''; ?></textarea>
                                <p><i>Enter your cutom message for showing form data. Just follow the Mail -> Message Body section convention</i></p>
                            </div>
                        </div>

                        <br class="clear">
                    </div>
                </div>
            </div>
        </div>

        <?php
    }

    public function save_sms_data( $form ) {
        if ( empty( $form->id() ) ) {
            return;
        }

        if ( ! wpcf7_admin_has_edit_cap() ) {
            return;
        }

        $postdata = wp_unslash($_POST['cf7_sms']);
        update_post_meta( $form->id(), '_sms_settings', $postdata );
    }

    public function send_sms( $form ) {
        $options = get_option( 'cf7_sms_settings' );

        if ( empty( $options ) ) {
            return;
        }

        if ( empty( $options['sms_gateway'] ) ) {
            return;
        }

        $form_settings = get_post_meta( $form->id(), '_sms_settings', true );

        $replace = array();

        preg_match_all("/\[(.*?)\]/", $form_settings['message'], $matches );

        $find = $matches[0];

        foreach ($matches[1] as $value) {
            $replace[] = $_POST[$value];
        }
    }
}
