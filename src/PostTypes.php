<?php
namespace Ramphor\Testimonials;

use Embrati\Embrati;

class PostTypes
{
    const RATING_POST_TYPE = 'testimonial';
    const RATING_CATEGORY_TAXONOMY = 'testimonial_cat';

    protected $embrati;

    public function __construct()
    {
        add_action('init', array($this, 'registerPostTypes'), 15);
        add_action('init', array($this, 'registerTaxonomies'), 15);
        add_action('add_meta_boxes', array($this, 'registerRatingMetabox'));
        add_action('add_meta_boxes', array($this, 'registerMetabox'));
    }

    public function registerPostTypes()
    {
        $lables = array(
            'name' => __('Testimonials', 'ramphor_testimonial'),
            'plural_name' => __('Testimonial', 'ramphor_testimonial'),
        );
        register_post_type(
            static::RATING_POST_TYPE,
            apply_filters('ramphor_testimonial_post_type_args', array(
                'public' => true,
                'labels' => $lables,
                'menu_icon' => 'dashicons-testimonial',
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
                '_builtin' => true,
            ))
        );
    }

    public function registerTaxonomies()
    {
        $labels = array(
            'name' => __('Categories', 'ramphor_testimonial'),
            'plural_name' => __('Category', 'ramphor_testimonial'),
        );
        register_taxonomy(
            static::RATING_CATEGORY_TAXONOMY,
            static::RATING_POST_TYPE,
            apply_filters('ramphor_testimonial_category_taxonomy_args', array(
                'public' => true,
                'labels' => $labels,
                'hierarchical' => true,
                '_builtin' => true,
            ))
        );
    }

    public function registerRatingMetabox()
    {
        $this->embrati = Embrati::getInstance();
        $this->embrati->registerAdminScripts();
        add_meta_box(
            'testimonial_rating',
            __('Rating', 'ramphor_testimonial'),
            array($this, 'renderRating'),
            static::RATING_POST_TYPE,
            'side'
        );
    }

    public function renderRating()
    {
        $this->embrati->create('testimonial-rating', array(
            'max' => 5,
            'rating' => 4,
        ));
    }

    public function registerMetabox()
    {
        add_meta_box(
            'testimonial_metadata',
            __('Testimonial Informations', 'ramphor_testimonial'),
            array($this, 'renderMetadataBox'),
            static::RATING_POST_TYPE,
            'normal',
            'high'
        );
    }

    public function renderMetadataBox()
    {
        ?>
        <p>
            <label for=""><?php _e('Testimony Name', 'ramphor_testimonial'); ?></label>
            <input type="text" class="widefat" />
        </p>
        <p>
            <label for=""><?php _e('Testimony\'s Company', 'ramphor_testimonial'); ?></label>
            <input type="text" class="widefat">
        </p>
        <p>
            <label for=""><?php _e('Position', 'ramphor_testimonial'); ?></label>
            <input type="text" class="widefat">
        </p>
        <?php
    }
}