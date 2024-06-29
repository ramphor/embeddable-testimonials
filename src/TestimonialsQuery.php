<?php

namespace Ramphor\Testimonials;

use WP_Query;
use Ramphor\Testimonials\PostTypes;

class TestimonialsQuery
{
    protected $args = array();
    public function __construct($args = array())
    {
        $this->buildArgs($args);
    }

    public function buildArgs($args)
    {
        $ret = array();
        $tax_query = array();
        if (isset($args['category'])) {
            $catgory = is_array($args['category']) ? $args['category'] : array($args['category']);
            $tax_query[] = array(
                'taxonomy' => PostTypes::TESTIMONIAL_CATEGORY_TAXONOMY,
                'field' => 'term_id',
                'terms' => $catgory,
                'operator' => 'IN',
            );
        }

        if (!empty($args['orderby']) && $args['orderby'] !== 'none') {
            $ret['orderby'] = $args['orderby'];
        }
        if (isset($args['order']) && in_array(strtolower($args['order']), array('asc', 'desc'))) {
            $ret['order'] = $args['order'];
        }

        if (!empty($args['limit'])) {
            $ret['posts_per_page'] = $args['limit'];
        }

        $ret['tax_query'] = $tax_query;

        return $this->args = array_merge($ret, array(
            'post_type' => 'testimonial'
        ));
    }

    public function getQuery()
    {
        $query = new WP_Query(apply_filters(
            'ramphor_testimonials_query_args',
            $this->args
        ));

        do_action_ref_array(
            'ramphor_testimonials_create_query',
            array(&$query)
        );

        // Return query
        return $query;
    }
}
