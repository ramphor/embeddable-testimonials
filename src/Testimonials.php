<?php
namespace Ramphor\Testimonials;

use Ramphor\Testimonials\Elementor\TestimonialsWidget;

final class Testimonials
{
    protected static $instance;

    public $postType;

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
}
