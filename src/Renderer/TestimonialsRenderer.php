<?php
namespace Ramphor\Testimonials\Renderer;

use Ramphor\Testimonials\TestimonialsQuery;
use Ramphor\Testimonials\Template;

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
        $this->props = $props;
    }

    protected function getHeaderContent()
    {
        if (empty($this->props['title'])) {
            return '';
        }
        return Template::render('common/header-text', array(
            'text' => $this->title,
        ), null, false);
    }

    public function get_content()
    {
        if (empty($this->query)) {
            return '';
        }

        $wrapper_classes = array(
            'testimonials',
        );
        $is_carousel = $this->props['carousel'] === 'yes';
        if ($is_carousel) {
            $this->setupGlideJS();
            $this->setupScripts();
            $wrapper_classes[] = 'carousel-style';
            $wrapper_classes[] = 'splide';
        } else {
            $wrapper_classes[] = 'card-list';
        }

        $content = Template::render('testimonials-widget', array(
            'header' => $this->getHeaderContent(),
            'wp_query' => $this->query->getQuery(),
            't' => Template::class,
            'is_carousel' => $is_carousel,
            'wrapper_attributes' => jankx_generate_html_attributes(array(
                'class' => $wrapper_classes,
                'id' => $this->carouselId,
            )),
        ), null, false);

        $this->cleanGlideJS();

        return $content;
    }

    protected function setupGlideJS()
    {
        add_action('testimonials_before_loop', array($this, 'open_splides_slides'));
        add_action('testimonials_after_loop', array($this, 'close_splides_slides'));

        add_action('testimonials_before_loop_item', array($this, 'open_splides_slide'));
        add_action('testimonials_after_loop_item', array($this, 'close_splides_slide'));
    }

    protected function cleanGlideJS()
    {
        remove_action('testimonials_before_loop', array($this, 'open_splides_slides'));
        remove_action('testimonials_after_loop', array($this, 'close_splides_slides'));

        remove_action('testimonials_before_loop_item', array($this, 'open_splides_slide'));
        remove_action('testimonials_after_loop_item', array($this, 'close_splides_slide'));
    }

    public function open_splides_slides()
    {
        echo '<div class="splide__track" data-splide-el="track">';
            echo '<ul class="splide__list">';
    }

    public function close_splides_slides()
    {
            echo '</ul>';
        echo '</div>';
    }

    public function open_splides_slide($wp_query)
    {
        if ($this->currentIndex === 0) {
            echo '<div class="splide__slide">';
        }
    }

    public function close_splides_slide($wp_query)
    {
        $this->currentIndex += 1;
        $rows = array_get($this->props, 'rows', 1);

        if ($this->currentIndex == $rows) {
            $this->currentIndex = 0;
        }

        $currentPostIndex = $wp_query->current_post;
        $totalIndex = $wp_query->post_count - 1;
        if ($this->currentIndex === 0 || $currentPostIndex >= $totalIndex) {
            echo '</div>';
        }
    }

    protected function setupScripts()
    {
        add_action('wp_print_footer_scripts', array($this, 'initCarousel'));
    }

    public function initCarousel()
    {
        ?>
        <script>
            new Splide('#<?php echo $this->carouselId; ?>', {
                type: 'carousel',
                startAt: 0,
                perPage: 3,
                padding: 10,
                gap: 20,
                arrows: false,
                breakpoints: {
                    800: {
                        perPage: 2
                    },
                    600: {
                        perPage: 1
                    }
                }
            }).mount();
        </script>
        <?php
    }

    public function __toString()
    {
        return (string) $this->get_content();
    }
}
