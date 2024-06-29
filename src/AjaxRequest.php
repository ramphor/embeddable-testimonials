<?php

namespace Ramphor\Testimonials;

use Ramphor\Testimonials\Testimonials;

class AjaxRequest
{
    public function init()
    {
        add_action('wp_ajax_ramphor_testimonial_set_rate', array($this, 'setTestimonial'));
        add_action('wp_ajax_nopriv_ramphor_testimonial_set_rate', array($this, 'setTestimonial'));
    }

    public function setTestimonial()
    {
        $requestPayload = json_decode(file_get_contents('php://input'), true);
        if (empty($requestPayload)) {
            wp_send_json_error(__('The argument is invalid', 'ramphor_testimonials'));
        }
        if (empty($requestPayload['post_id']) || is_null(get_post($requestPayload['post_id']))) {
            wp_send_json_error(__('The post ID is invalid', 'ramphor_testimonials'));
        }
        if (
            empty($requestPayload['nonce']) ||
            !wp_verify_nonce($requestPayload['nonce'], sprintf('set_star_rating_for_%s', $requestPayload['post_id']))
        ) {
            wp_send_json_error(__('The request is not accept by security rules', 'ramphor_testimonials'));
        }
        // Don't do anything when the rating value is empty
        if (empty($requestPayload['rating'])) {
            wp_send_json_success();
        }

        update_post_meta(
            $requestPayload['post_id'],
            Testimonials::POST_META_STAR_RATING,
            $requestPayload['rating']
        );
        wp_send_json_success($requestPayload['rating']);
    }
}
