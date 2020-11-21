<?php
namespace Ramphor\Testimonials;

use Embrati\Embrati;
use Ramphor\Testimonials\Testimonials;

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
            'name' => __('Testimonials', 'ramphor_testimonials'),
            'plural_name' => __('Testimonial', 'ramphor_testimonials'),
        );
        register_post_type(
            static::RATING_POST_TYPE,
            apply_filters('ramphor_testimonial_post_type_args', array(
                'public' => false,
                'labels' => $lables,
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
                'menu_icon' => 'dashicons-testimonial',
                'show_ui' => true,
                'show_in_menu' => true,
            ))
        );
    }

    public function registerTaxonomies()
    {
        $labels = array(
            'name' => __('Categories', 'ramphor_testimonials'),
            'plural_name' => __('Category', 'ramphor_testimonials'),
        );
        register_taxonomy(
            static::RATING_CATEGORY_TAXONOMY,
            static::RATING_POST_TYPE,
            apply_filters('ramphor_testimonial_category_taxonomy_args', array(
                'public' => false,
                'labels' => $labels,
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
            ))
        );
    }

    public function registerRatingMetabox()
    {
        $this->embrati = Embrati::getInstance();
        $this->embrati->registerAdminScripts();
        add_meta_box(
            'testimonial_rating',
            __('Rating', 'ramphor_testimonials'),
            array($this, 'renderRating'),
            static::RATING_POST_TYPE,
            'side'
        );
    }

    public function renderRating($post)
    {
        echo '<div class="testimonial-loading"></div>'; // wpcs: XSS Ok
        $rating = floatval(get_post_meta($post->ID, Testimonials::POST_META_STAR_RATING, true));
        $this->embrati->create('testimonial-rating', array(
            'max' => 5,
            'rating' => $rating,
        ));
    }

    public function registerMetabox()
    {
        add_meta_box(
            'testimonial_metadata',
            __('Testimonial Informations', 'ramphor_testimonials'),
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
            <label for=""><?php _e('Testimony Name', 'ramphor_testimonials'); ?></label>
            <input type="text" class="widefat" />
        </p>
        <p>
            <label for=""><?php _e('Testimony\'s Company', 'ramphor_testimonials'); ?></label>
            <input type="text" class="widefat">
        </p>
        <p>
            <label for=""><?php _e('Position', 'ramphor_testimonials'); ?></label>
            <input type="text" class="widefat">
        </p>
        <?php
    }
}
