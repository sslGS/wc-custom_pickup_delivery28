<?php

if(!defined('ABSPATH')) exit;

/**
 * @since 0.6.2.0
 * @extends \WC_Email
*/
class WC_Custom_Pickup_Delivery extends WC_Email {

    public function __construct() {
        
        $this->id = 'wc_custom_order';
        $this->customer_email = true;
        $this->title = 'Курьер ожидает встречи';
        $this->description = 'Кастомный класс email';

        $this->heading = 'Custom Order';
        $this->subject = 'Custom Order email';

		$this->template_plain = 'emails/plain/customer-completed-order.php';

        // add_action('woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger'));
        // add_action('woocommerce_order_status_failed_to_processing_notification',  array( $this, 'trigger'));
        $this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
		);

        parent::__construct();
    }

    public function get_default_subject() {
			return __( 'Your {site_title} order is now complete', 'woocommerce' );
	}
    public function get_default_heading() {
			return __( 'Thanks for shopping with us', 'woocommerce' );
	}

    public function trigger($order_id) {
        $this->setup_locale();

        if ($order_id && ! is_a($order, 'WC_Order')) {
            $order = wc_get_order($order_id);
        }

        if (is_a($order, 'WC_Order')) {
            $this->object                         = $order;
            $this->recipient                      = $this->object->get_billing_email();
            $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
        }

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();
    }

    public function get_content_html() {
	    return wc_get_template_html(
			$this->template_html,
            array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				)
		);
    }

    public function get_content_plain() {
        return wc_get_template_html(
			$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				)
		);
    }

    public function get_default_additional_content() {
			return __( 'Thanks for shopping with us.', 'woocommerce' );
	}
}

return new WC_Custom_Pickup_Delivery();
