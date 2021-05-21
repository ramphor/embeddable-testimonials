<?php
namespace Ramphor\Testimonials;

use Jankx\Template\Template as TemlateLib;
use Ramphor\Testimonials\Testimonials;

class Template
{
    protected static $templateLoader;

    protected static function getEngine()
    {
        if (is_null(static::$templateLoader)) {
            $templateDirectory = sprintf('%s/templates', dirname(EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE));
            static::$templateLoader = TemlateLib::createEngine(
                'testimonials',
                apply_filters('ramphor_testimonals_templates_directory_name', 'testimonials'),
                $templateDirectory,
                'wordpress'
            );
        }

        return static::$templateLoader;
    }

    public static function render()
    {
        $args = func_get_args();
        if (!isset($args[1])) {
            $args[1] = array();
        }
        $args[1]['t'] = __CLASS__;
        $args[1]['e'] = Testimonials::getInstance()->embrati;

        return call_user_func_array(
            array(static::getEngine(), 'render'),
            $args
        );
    }
}
