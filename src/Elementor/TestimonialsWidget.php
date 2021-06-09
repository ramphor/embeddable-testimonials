<?php
namespace Ramphor\Testimonials\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Ramphor\Testimonials\TestimonialsQuery;
use Ramphor\Testimonials\PostTypes;
use Ramphor\Testimonials\Renderer\TestimonialsRenderer;
use Jankx\Specs\WP_Query;

class TestimonialsWidget extends Widget_Base
{
    public function get_name()
    {
        return 'ramphor_testimonials';
    }

    public function get_title()
    {
        return __('Testimonials', 'ramphor_testimonials');
    }

    public function get_icon()
    {
        return 'eicon-testimonial';
    }

    public function get_categories()
    {
        return array('general', 'theme-elements');
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Content', 'ramphor_testimonials'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'title',
            array(
                'label' => __('Widget Title', 'ramphor_testimonials'),
                'type'  => Controls_Manager::TEXT,
            )
        );

        $this->add_control(
            'category',
            array(
                'label'       => __('Categories', 'ramphor_testimonials'),
                'type'        => Controls_Manager::SELECT2,
                'default'     => 0,
                'options'     => get_terms(
                    array(
                        'taxonomy'   => PostTypes::TESTIMONIAL_CATEGORY_TAXONOMY,
                        'fields'     => 'id=>name',
                        'hide_empty' => false,
                    )
                ),
            )
        );

        $this->add_control(
            'order_by',
            array(
                'label'   => __('Order by', 'ramphor_testimonials'),
                'type'    => Controls_Manager::SELECT,
                'options' => WP_Query::order_by(),
                'default' => WP_Query::DEFAULT_ORDER_BY
            )
        );
        $this->add_control(
            'order',
            array(
                'label'   => __('Order by', 'ramphor_testimonials'),
                'type'    => Controls_Manager::SELECT,
                'options' => WP_Query::order()
            )
        );

        $this->add_control(
            'carousel',
            array(
                'label'   => __('Enable carousel', 'ramphor_testimonials'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'items',
            array(
                'label'   => __('Items per page', 'ramphor_testimonials'),
                'type'    => Controls_Manager::NUMBER,
                'max'     => 10,
                'step'    => 1,
                'default' => 3,
                'description' => __('The number of items you want to see on the screen.', 'ramphor_testinomials')
            )
        );

        $this->add_control(
            'rows',
            array(
                'label' => __('Rows', 'jankx_ecommerce'),
                'type' => Controls_Manager::NUMBER,
                'max' => 10,
                'step' => 1,
                'default' => 1,
            )
        );

        $this->add_control(
            'limit',
            array(
                'label'   => __('Limit', 'ramphor_testimonials'),
                'type'    => Controls_Manager::NUMBER,
                'max'     => 100,
                'step'    => 1,
                'default' => 5,
            )
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $query_args = array(
            'orderby' => array_get($settings, 'order_by'),
            'order' => array_get($settings, 'order'),
            'limit' => array_get($settings, 'limit', 5),
        );

        if (array_get($settings, 'category')) {
            $query_args['category'] = array_get($settings, 'category');
        }

        $query    = new TestimonialsQuery($query_args);
        $renderer = new TestimonialsRenderer($query);

        $renderer->setProps($settings);

        echo (string) $renderer;
    }

    protected function _content_template()
    {
    }
}
