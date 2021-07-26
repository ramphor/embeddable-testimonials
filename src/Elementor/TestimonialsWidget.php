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

        $this->add_control(
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

        $this->add_control(
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

        $this->add_control(
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

    public function postLayoutOptionsTranformer($settings) {
        return array(
            'columns' => array_get($settings, 'columns', 4),
            'rows' => array_get($settings, 'rows', 1),
            'layout' => array_get($settings, 'layout', Carousel::LAYOUT_NAME),
        );
    }


    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $query_args = array(
            'orderby' => array_get($settings, 'order_by'),
            'order' => array_get($settings, 'order'),
            'limit' => array_get($settings, 'posts_per_page', 5),
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

    protected function _content_template()
    {
    }
}
