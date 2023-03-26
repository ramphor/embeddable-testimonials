<?php
namespace Ramphor\Testimonials;

use Embrati\Embrati;
use Jankx\Template\Template;
use Jankx\TemplateEngine\Engines\Plates;
use Jankx\PostLayout\PostLayoutManager;
use Ramphor\Testimonials\Elementor\TestimonialsWidget;

final class Testimonials
{
    const POST_META_STAR_RATING = '_ramphor_testimonial_rating';
    const TEMPLATE_ENGINE_ID = 'ramphor_testimonials';

    protected static $instance;

    public $postType;
    public $embrati;
    public $ajax;
    public $display;

    protected $engine;

    public static $version;

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $this->includes();
        $this->initFeatures();
        $this->integrateWithOtherPlugins();
    }

    protected function includes()
    {
        require_once dirname(__FILE__) . '/testimonial-functions.php';
    }

    protected function initFeatures()
    {
        $pluginInfo = get_file_data(EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE, array(
            'version' => 'Version'
        ));
        static::$version = $pluginInfo['version'];

        $this->postType = new PostTypes();
        $this->embrati  = Embrati::getInstance();
        $this->ajax     = new AjaxRequest();
        $this->display = new Display($this);

        add_action('wp_enqueue_scripts', array($this->embrati, 'registerStyles'));

        add_action('embrati_registered_scripts', array($this, 'registerTestimonialScripts'));
        add_filter('embrati_enqueue_script', array($this, 'changeEnqueueSCript'));

        add_action('init', array($this->ajax, 'init'));
        add_action('wp', array($this->display, 'display'));

        add_action('after_setup_theme', array($this, 'createTemplateEngine'));

        $this->embrati->setJsRateCallback('ramphor_set_star_rating');
    }

    public function integrateWithOtherPlugins()
    {
        $activePlugins = get_option('active_plugins');
        if (($index = array_search('elementor/elementor.php', $activePlugins)) !== false) {
            $this->integrationWithElementor();
        }
    }

    protected function integrationWithElementor()
    {
        add_action('elementor/widgets/register', array($this, 'registerElementorWidgets'));
    }

    public function registerElementorWidgets($widget_manager)
    {
        $widget_manager->register(new TestimonialsWidget());
    }

    protected function asset_url($path = '')
    {
        $abspath = constant('ABSPATH');
        $testimonialDirPath = dirname(EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE);
        if (PHP_OS==='WINNT') {
            $abspath = str_replace('\\', '/', $abspath);
            $testimonialDirPath = str_replace('\\', '/', $testimonialDirPath);
        }

        $assetDirUrl = str_replace($abspath, site_url('/'), $testimonialDirPath);
        return sprintf(
            '%s/assets/%s',
            $assetDirUrl,
            $path
        );
    }

    public function registerTestimonialScripts()
    {
        wp_register_script(
            'ramphor-testimonials',
            $this->asset_url('js/testimonials.js'),
            array('embrati'),
            Testimonials::$version,
            true
        );

        $globalData = array(
            'set_rate_url' => admin_url('admin-ajax.php?action=ramphor_testimonial_set_rate'),
        );
        $current_screen = get_current_screen();
        if ($current_screen->id === 'testimonial') {
            global $post;
            $globalData['current_nonce'] = wp_create_nonce('set_star_rating_for_' . $post->ID);
            $globalData['post_id'] = $post->ID;
        }
        wp_localize_script('ramphor-testimonials', 'testimonials_global', $globalData);
    }

    public function changeEnqueueSCript()
    {
        return 'ramphor-testimonials';
    }

    public function createTemplateEngine()
    {
        $engine = Template::createEngine(
            static::TEMPLATE_ENGINE_ID,
            apply_filters('ramphor_testimonals_templates_directory_name', 'testimonials'),
            sprintf('%s/templates', dirname(EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE)),
            Plates::ENGINE_NAME
        );
        $this->engine = &$engine;
        PostLayoutManager::createInstance($engine);
    }

    public function getTemplateEngine()
    {
        return $this->engine;
    }
}
