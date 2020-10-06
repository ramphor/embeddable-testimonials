<?php
namespace Ramphor\Testimonials\Elementor;

use Elementor\Widget_Base;
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
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $query = new TestimonialsQuery(array());
        $renderer = new TestimonialsRenderer($query);

        $renderer->setProps($settings);

        echo (string) $renderer;
    }

    protected function _content_template()
    {
    }
}
