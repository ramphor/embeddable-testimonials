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
            $wrapper_classes[] = 'glide';
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
        add_action('testimonials_after_loop', array($this, 'glides_bullets'), 15);

        add_action('testimonials_before_loop', array($this, 'open_glides_slides'));
        add_action('testimonials_after_loop', array($this, 'close_glides_slides'));

        add_action('testimonials_before_loop_item', array($this, 'open_glides_slide'));
        add_action('testimonials_after_loop_item', array($this, 'close_glides_slide'));
    }

    protected function cleanGlideJS()
    {
        remove_action('testimonials_after_loop', array($this, 'glides_bullets'), 15);

        remove_action('testimonials_before_loop', array($this, 'open_glides_slides'));
        remove_action('testimonials_after_loop', array($this, 'close_glides_slides'));

        remove_action('testimonials_before_loop_item', array($this, 'open_glides_slide'));
        remove_action('testimonials_after_loop_item', array($this, 'close_glides_slide'));
    }

    public function glides_track()
    {
        echo '<div class="glide__track" data-glide-el="track">...</div>';
    }

    public function glides_bullets()
    {
        ?>
        <div class="glide__bullets" data-glide-el="controls[nav]">
            <button class="glide__bullet" data-glide-dir="=0"></button>
            <button class="glide__bullet" data-glide-dir="=1"></button>
            <button class="glide__bullet" data-glide-dir="=2"></button>
        </div>
        <?php
    }

    public function open_glides_slides()
    {
        echo '<div class="glide__track" data-glide-el="track">';
            echo '<div class="glide__slides">';
    }

    public function close_glides_slides()
    {
            echo '</div>';
        echo '</div>';
    }

    public function open_glides_slide()
    {
        echo '<div class="glide__slide">';
    }

    public function close_glides_slide()
    {
        echo '</div>';
    }

    protected function setupScripts()
    {
        add_action('wp_print_footer_scripts', array($this, 'initCarousel'));
    }

    public function initCarousel()
    {
        ?>
        <script>
            new Glide('#<?php echo $this->carouselId; ?>', {
                type: 'carousel',
                startAt: 0,
                perView: 3
            }).mount();
        </script>
        <?php
    }

    public function __toString()
    {
        return (string) $this->get_content();
    }
}
