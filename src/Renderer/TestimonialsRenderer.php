<?php
namespace Ramphor\Testimonials\Renderer;

use Ramphor\Testimonials\Testimonials;
use Ramphor\Testimonials\TestimonialsQuery;
use Jankx\PostLayout\PostLayoutManager;
use Jankx\PostLayout\Layout\Carousel;

class TestimonialsRenderer
{
    protected static $carouselInstances = array();

    protected $query;
    protected $carouselId;
    protected $props = array();
    protected $currentIndex = 0;

    public function __construct($query = null)
    {
        $carouselId       = empty(static::$carouselInstances) ? 1 : max(static::$carouselInstances) + 1;
        $this->carouselId = sprintf('testimonials-%d', $carouselId);
        array_push(static::$carouselInstances, $carouselId);

        if (is_a($query, TestimonialsQuery::class)) {
            $this->query = $query;
        }
    }

    public function setProps($props)
    {
        if (!is_array($props)) {
            return;
        }
        $this->props = apply_filters(
            'ramphor_embeded_testimonials_renderer_props',
            $props
        );
    }

    public function get_content()
    {
        if (empty($this->query)) {
            return '';
        }

        $testimonials = Testimonials::getInstance();
        $postLayoutManager = PostLayoutManager::getInstance($testimonials->getTemplateEngine());
        $testimonialLayout = $postLayoutManager->createLayout(
            array_get($this->props, 'layout', Carousel::LAYOUT_NAME),
            $this->query->getQuery()
        );
        $testimonialLayout->setOptions($this->props);

        return $testimonialLayout->render(false);
    }

    public function __toString()
    {
        return (string) $this->get_content();
    }
}
