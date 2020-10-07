<?php
namespace Ramphor\Testimonials;

use WP_Query;

class TestimonialsQuery
{
    protected $args = array();
    public function __construct($args = array())
    {
        $this->args = $this->buildArgs($args);
    }

    public function buildArgs($args)
    {
        $ret = array();
        return array_merge($ret, array(
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
