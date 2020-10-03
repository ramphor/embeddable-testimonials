<?php
namespace Ramphor\Testimonials;

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
    }

    protected function initFeatures()
    {
        $this->postType = new PostTypes();
    }
}
