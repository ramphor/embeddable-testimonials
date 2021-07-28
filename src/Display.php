<?php
namespace Ramphor\Testimonials;

class Display
{
    protected $testimonials;
    protected $templateEngine;

    public function __construct($testimonials)
    {
        $this->testimonials = &$testimonials;
    }

    public function display()
    {
        $this->templateEngine = $this->testimonials->getTemplateEngine();

        add_action('jankx_testimonial_layout_before_loop_item', array($this, 'createVideoOverlay'));
    }

    public function createVideoOverlay($post)
    {
        if ($post->format() !== 'video') {
            return;
        }
        $this->templateEngine->render('video-overlay');
    }
}
