<?php

namespace Ramphor\Testimonials;

use Jankx\PostFormats\Abstracts\Format;
use Jankx\PostFormats\PostFormats;
use Ramphor\Testimonials\Testimonials;

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
        add_action(
            'jankx_testimonial_layout_before_loop_post_thumbnail',
            array($this, 'createVideoOverlay'),
            10,
            2
        );
    }

    public function createVideoOverlay($post, $data_index)
    {
        $format = Format::getFormat($post);
        $feature = PostFormats::getFeature($format);
        if (!$feature) {
            return;
        }
        $testimonial = Testimonials::getInstance();
        $feature->setTemplateEngine(
            $testimonial->getTemplateEngine()
        );
        switch ($format) {
            case 'video':
                return $feature->makeVideoOverlay($post, $data_index);
        }

        if ($post->format() !== 'video') {
            return;
        }
        $this->templateEngine->render('video-overlay');
    }
}
