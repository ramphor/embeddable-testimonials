<?php

namespace Ramphor\Testimonials\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Ramphor\Testimonials\TestimonialsQuery;
use Ramphor\Testimonials\PostTypes;
use Ramphor\Testimonials\Renderer\TestimonialsRenderer;
use Jankx\Specs\WP_Query;
use Jankx\PostLayout\PostLayoutManager;
use Jankx\PostLayout\Layout\Card;
use Jankx\PostLayout\Layout\Carousel;
use Jankx\PostLayout\Layout\Preset5;

class TestimonialsWidget extends Widget_Base
{
    protected $settings;

    public function get_name()
    {
        return 'ramphor_testimonials';
    }

    public function get_title()
    {
        return __('Testimonials', 'ramphor_testimonials');
    }

    public function get_icon()
    {
        return 'eicon-testimonial';
    }

    public function get_categories()
    {
        return array('general', 'theme-elements');
    }

    protected function getImageSizeName($sizeName)
    {
        switch ($sizeName) {
            case 'thumbnail':
                return __('Thumbnail');
            case 'medium':
                return __('Medium');
            case 'large':
                return __('Large');
            default:
                return preg_replace_callback(array(
                    '/^(\w)/',
                    '/(\w)([\-|_]{1,})/'
                ), function ($matches) {
                    if (isset($matches[2])) {
                        return sprintf('%s ', $matches[1]) ;
                    } elseif (isset($matches[1])) {
                        return strtoupper($matches[1]);
                    }
                }, $sizeName);
        }
    }

    protected function getImageSizes()
    {
        $ret = array();
        foreach (get_intermediate_image_sizes() as $imageSize) {
            if (apply_filters('jankx_image_size_ignore_medium_large_size', true)) {
                if ($imageSize === 'medium_large') {
                    continue;
                }
            }
            $ret[$imageSize] = $this->getImageSizeName($imageSize);
        }
        $ret['full'] = __('Full size', 'jankx');
        $ret['custom'] = __('Custom Size', 'jankx');

        return $ret;
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Content', 'ramphor_testimonials'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'title',
            array(
                'label' => __('Widget Title', 'ramphor_testimonials'),
                'type'  => Controls_Manager::TEXT,
            )
        );

        $this->add_control(
            'category',
            array(
                'label'       => __('Categories', 'ramphor_testimonials'),
                'type'        => Controls_Manager::SELECT2,
                'default'     => 0,
                'options'     => get_terms(
                    array(
                        'taxonomy'   => PostTypes::TESTIMONIAL_CATEGORY_TAXONOMY,
                        'fields'     => 'id=>name',
                        'hide_empty' => false,
                    )
                ),
            )
        );

        $this->add_control(
            'order_by',
            array(
                'label'   => __('Order by', 'ramphor_testimonials'),
                'type'    => Controls_Manager::SELECT,
                'options' => WP_Query::order_by(),
                'default' => WP_Query::DEFAULT_ORDER_BY
            )
        );
        $this->add_control(
            'order',
            array(
                'label'   => __('Order by', 'ramphor_testimonials'),
                'type'    => Controls_Manager::SELECT,
                'options' => WP_Query::order()
            )
        );

        $this->add_responsive_control(
            'layout',
            [
                'label' => __('Layout', 'ramphor_testimonials'),
                'type' => Controls_Manager::SELECT,
                'default' => Card::LAYOUT_NAME,
                'options' => PostLayoutManager::getLayouts(array(
                    'field' => 'names',
                    'exclude' => 'parent'
                )),
            ]
        );

        $this->add_control(
            'thumbnail_size',
            [
                'label' => __('Image size', 'jankx'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->getImageSizes(),
                'default' => 'medium',
            ]
        );

        $this->add_control(
            'image_width',
            [
                'label' => __('Image Width', 'jankx'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 5,
                'default' => 400,
                'condition' => array(
                    'thumbnail_size' => 'custom'
                )
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label' => __('Image Height', 'jankx'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 5,
                'default' => 320,
                'condition' => array(
                    'thumbnail_size' => 'custom',
                )
            ]
        );

        if (!empty($item_styles = $this->getItemStyles())) {
            $item_styles = array_merge(array('default' => __('Default')), $item_styles);
            $this->add_responsive_control(
                'item_style',
                array(
                    'label'   => __('Item Styles', 'ramphor_testimonials'),
                    'type'    => Controls_Manager::SELECT,
                    'options' => $item_styles,
                    'default' => 'default',
                )
            );
        }


        $this->add_responsive_control(
            'show_carousel_pagination',
            [
                'label' => __('Carousel Pagination', 'jankx'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'jankx'),
                'label_off' => __('Hide', 'jankx'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => array(
                    'post_layout' => array(Carousel::LAYOUT_NAME, Preset5::LAYOUT_NAME)
                )
            ]
        );

        $this->add_responsive_control(
            'show_carousel_nav',
            [
                'label' => __('Carousel Nav', 'jankx'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'jankx'),
                'label_off' => __('Hide', 'jankx'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'post_layout' => array(Carousel::LAYOUT_NAME, Preset5::LAYOUT_NAME)
                )
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'ramphor_testimonials'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_responsive_control(
            'rows',
            [
                'label' => __('Rows', 'ramphor_testimonials'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'last_columns_items',
            [
                'label' => __('Last Column Items', 'ramphor_testimonials'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 3,
                'of_type' => 'post_layout',
                'condition' => array(
                    'post_layout' => array(Preset5::LAYOUT_NAME)
                )
            ]
        );

        $this->add_responsive_control(
            'posts_per_page',
            [
                'label' => __('Number of Posts', 'ramphor_testimonials'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'default' => 5,
            ]
        );

        $this->end_controls_section();
    }

    public function getItemStyles()
    {
        return apply_filters('ramphor_testimonials_item_styles', array());
    }

    public function postLayoutOptionsTranformer($settings)
    {
        $postLayoutOptions = array(
            'specific_data' => array_get($settings, 'specific_data', ''),
            'columns_mobile' => array_get($settings, 'columns_mobile'),
            'columns' => $this->get_responsive_setting('columns', 4),
            'rows' => $this->get_responsive_setting('rows', 1),
            'layout' => $this->get_responsive_setting('layout', Carousel::LAYOUT_NAME),
            'show_dot'  => $this->get_responsive_setting('show_carousel_pagination', 'no') === 'yes',
            'show_nav'  => $this->get_responsive_setting('show_carousel_nav', 'yes') === 'yes',
            'last_columns_items'  => array_get($settings, 'last_columns_items', 3),
            'thumbnail_size'  => array_get($settings, 'thumbnail_size', 'thumbnail'),
        );

        $itemStyleOptions = array_get($settings, 'item_style', 'default');

        if ($itemStyleOptions !== 'default') {
            $postLayoutOptions['item_style'] = $itemStyleOptions;
        }
        return $postLayoutOptions;
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $query_args = array(
            'orderby' => array_get($settings, 'order_by'),
            'order' => array_get($settings, 'order'),
            'limit' => $this->get_responsive_setting('posts_per_page', 5),
        );

        if (array_get($settings, 'category')) {
            $query_args['category'] = array_get($settings, 'category');
        }

        $query    = new TestimonialsQuery($query_args);
        $renderer = new TestimonialsRenderer($query);

        $renderer->setProps(
            $this->postLayoutOptionsTranformer($settings)
        );

        echo (string) $renderer;
    }

    public function get_responsive_setting($field_name, $default_value)
    {
        if (is_null($this->settings)) {
            $this->settings = $this->get_settings_for_display();
        }
        $settings = &$this->settings;
        $desktop_value = array_get($settings, $field_name, $default_value);

        if (jankx_is_mobile()) {
            $mobile_value = array_get($settings, sprintf('%s_mobile', $field_name));
            return $mobile_value ? $mobile_value : $desktop_value;
        }
        if (jankx_is_tablet()) {
            $tablet_value = array_get($settings, sprintf('%s_tablet', $field_name));
            return $tablet_value ? $tablet_value : $desktop_value;
        }

        return $desktop_value;
    }
}
