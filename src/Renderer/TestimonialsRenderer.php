<?php
namespace Ramphor\Testimonials\Renderer;

class TestimonialsRenderer
{
    protected $props = array();

    public function setProps($props)
    {
        if (!is_array($props)) {
            return;
        }
        $this->props = $props;
    }

    public function get_content()
    {
        return 'testimonials renderer';
    }

    public function __toString()
    {
        return $this->get_content();
    }
}
