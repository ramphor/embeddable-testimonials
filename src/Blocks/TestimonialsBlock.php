<?php

namespace Ramphor\Testimonials\Blocks;

use Jankx\Blocks\BlockAbstract;
use Ramphor\Testimonials\Renderer\TestimonialsRenderer;
use Ramphor\Testimonials\TestimonialsQuery;

class TestimonialsBlock extends BlockAbstract
{
    protected $type = 'ramphor/testimonials';

    public function render($data, $content)
    {
        $settings   = [];
        $query_args = array(
            'orderby' => array_get($settings, 'order_by'),
            'order' => array_get($settings, 'order'),
            'limit' => $this->get_responsive_setting('posts_per_page', 5),
        );

        if (array_get($settings, 'category')) {
            $query_args['category'] = array_get($settings, 'category');
        }

        $query    = new TestimonialsQuery($query_args);
        $renderer = new TestimonialsRenderer($query);

        $renderer->setProps([]);

        return $renderer->__toString();
    }
}
