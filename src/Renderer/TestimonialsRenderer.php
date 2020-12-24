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

    public function glides_bullets($wp_query)
    {
        $items = ceil($wp_query->post_count / array_get($this->props, 'rows', 1));
        $pages = ceil($items / array_get($this->props, 'items', 3));
        if ($pages < 2) {
            return;
        }
        ?>
        <div class="glide__bullets" data-glide-el="controls[nav]">
            <?php for ($i=0; $i < $pages; $i++) : ?>
                <button class="glide__bullet" data-glide-dir="=<?php echo $i; ?>"></button>
            <?php endfor; ?>
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

    public function open_glides_slide($wp_query)
    {
        if ($this->currentIndex === 0) {
            echo '<div class="glide__slide">';
        }
    }

    public function close_glides_slide($wp_query)
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
            new Glide('#<?php echo $this->carouselId; ?>', {
                type: 'carousel',
                startAt: 0,
                perView: 3,
                breakpoints: {
                    800: {
                        perView: 2
                    },
                    600: {
                        perView: 1
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
