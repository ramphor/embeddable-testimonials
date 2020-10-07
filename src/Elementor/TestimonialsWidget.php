<?php
namespace Ramphor\Testimonials\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Ramphor\Testimonials\TestimonialsQuery;
use Ramphor\Testimonials\Renderer\TestimonialsRenderer;

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
                        'taxonomy'   => 'testimonial_cat',
                        'fields'     => 'id=>name',
                        'hide_empty' => false,
                    )
                ),
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
                'label'   => __('Number of items', 'ramphor_testimonials'),
                'type'    => Controls_Manager::NUMBER,
                'max'     => 10,
                'step'    => 1,
                'default' => 3,
                'description' => __('The number of items you want to see on the screen.', 'ramphor_testinomials')
            )
        );

        $this->add_control(
            'limit',
            array(
                'label'   => __('Number of Testimonials', 'ramphor_testimonials'),
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
        $settings = $this->get_settings_for_display();
        $query    = new TestimonialsQuery(array());
        $renderer = new TestimonialsRenderer($query);

        $renderer->setProps($settings);

        echo (string) $renderer;
    }

    protected function _content_template()
    {
    }
}
