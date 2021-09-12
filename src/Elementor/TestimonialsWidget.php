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

class TestimonialsWidget extends Widget_Base
{
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

    protected function _register_controls()
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

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'ramphor_testimonials'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 4,
                'of_type' => 'layout',
                'condition' => array(
                    'layout' => array(Card::LAYOUT_NAME, Carousel::LAYOUT_NAME)
                )
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
                'of_type' => 'layout',
                'condition' => array(
                    'layout' => array(Carousel::LAYOUT_NAME)
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

    public function postLayoutOptionsTranformer($settings)
    {
        return array(
            'specific_data' => array_get($settings, 'specific_data', ''),
            'columns_mobile' => array_get($settings, 'columns_mobile'),
            'columns' => $this->get_responsive_setting('columns', 4),
            'rows' => $this->get_responsive_setting('rows', 1),
            'layout' => $this->get_responsive_setting('layout', Carousel::LAYOUT_NAME),
        );
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

    public function get_responsive_setting($field_name, $default_value) {
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
