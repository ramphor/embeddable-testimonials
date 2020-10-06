<?php
namespace Ramphor\Testimonials;

use Jankx\Template\Template as TemlateLib;

class Template
{
    protected static $templateLoader;

    protected static function getLoader()
    {
        if (is_null(static::$templateLoader)) {
            $templateDirectory = sprintf('%s/templates', dirname(EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE));
            static::$templateLoader = TemlateLib::getLoader(
                $templateDirectory,
                apply_filters('ramphor_testimonals_templates_directory_name', 'testimonials'),
                'wordpress'
            );
        }

        return static::$templateLoader;
    }

    public static function render()
    {
        return call_user_func_array(
            array(static::$templateLoader, 'render'),
            func_get_args()
        );
    }
}
