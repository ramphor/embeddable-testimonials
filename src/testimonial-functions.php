<?php
use Ramphor\Testimonials\Testimonials;

function ramphor_testimonial_get_rating($post = null)
{
    if (is_null($post)) {
        global $post;
    } else {
        $post = get_post($post);
    }
    $rating = floatval(get_post_meta($post->ID, Testimonials::POST_META_STAR_RATING, true));

    return $rating;
}
