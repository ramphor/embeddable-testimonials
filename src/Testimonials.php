<?php
namespace Ramphor\Testimonials;

use Embrati\Embrati;
use Ramphor\Testimonials\Elementor\TestimonialsWidget;

final class Testimonials
{
    protected static $instance;

    public $postType;
    public $embrati;

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $this->initFeatures();
        $this->integrateWithOtherPlugins();
    }

    protected function initFeatures()
    {
        $this->postType = new PostTypes();
        $this->embrati  = Embrati::getInstance();

        add_action('wp_enqueue_scripts', array($this->embrati, 'registerStyles'));
        add_action('wp_enqueue_scripts', array($this, 'registerScripts'), 40);
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
        add_action('elementor/widgets/widgets_registered', array($this, 'registerElementorWidgets'));
    }

    public function registerElementorWidgets($widget_manager)
    {
        $widget_manager->register_widget_type(new TestimonialsWidget());
    }

    protected function asset_url($path = '')
    {
        $assetDirUrl = str_replace(ABSPATH, site_url('/'), dirname(EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE));
        return sprintf(
            '%s/assets/%s',
            $assetDirUrl,
            $path
        );
    }

    public function registerScripts()
    {
        global $wp_scripts, $wp_styles;
        if (isset($wp_scripts->registered['glide'])) {
            wp_register_script('glide', $this->asset_url('vendor/glidejs/glide.js'), array(), '3.4.1', true);
        }
        if (isset($wp_styles->registered['glide'])) {
            wp_register_style('glide', $this->asset_url('vendor/glidejs/css/glide.core.css'), array(), '3.4.1');
        }
        if (isset($wp_styles->registered['glide-theme'])) {
            wp_register_style('glide', $this->asset_url('vendor/glidejs/css/glide.theme.css'), array('glide'), '3.4.1');
        }

        // Call scripts
        wp_enqueue_script('glide');
        wp_enqueue_style('glide-theme');
    }
}
