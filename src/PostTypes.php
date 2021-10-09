<?php
namespace Ramphor\Testimonials;

use Embrati\Embrati;
use Ramphor\Testimonials\Testimonials;

class PostTypes
{
    const TESTIMONIAL_POST_TYPE = 'testimonial';
    const TESTIMONIAL_CATEGORY_TAXONOMY = 'testimonial_cat';

    protected $embrati;

    public function __construct()
    {
        add_action('init', array($this, 'registerPostTypes'), 15);
        add_action('init', array($this, 'registerTaxonomies'), 15);
        add_action('add_meta_boxes', array($this, 'registerRatingMetabox'));
        add_action('add_meta_boxes', array($this, 'registerMetabox'));
        add_action('save_post', array($this, 'saveTestimonialMetas'), 10, 2);
    }

    public function registerPostTypes()
    {
        $lables = array(
            'name' => __('Testimonials', 'ramphor_testimonials'),
            'plural_name' => __('Testimonial', 'ramphor_testimonials'),
        );
        $post_type_supports = apply_filters(
            'ramphor_testimonials_post_type_supports',
            array('title', 'editor', 'thumbnail', 'excerpt', 'post-formats')
        );

        register_post_type(
            static::TESTIMONIAL_POST_TYPE,
            apply_filters('ramphor_testimonial_post_type_args', array(
                'public' => false,
                'labels' => $lables,
                'supports' => $post_type_supports,
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
            static::TESTIMONIAL_CATEGORY_TAXONOMY,
            static::TESTIMONIAL_POST_TYPE,
            apply_filters('ramphor_testimonial_category_taxonomy_args', array(
                'public' => true,
                'labels' => $labels,
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_admin_column' => true,
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
            static::TESTIMONIAL_POST_TYPE,
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
            'step' => 0.5,
            'starSize' => 32
        ));
    }

    public function registerMetabox()
    {
        add_meta_box(
            'testimonial_metadata',
            __('Testimonial Informations', 'ramphor_testimonials'),
            array($this, 'renderMetadataBox'),
            static::TESTIMONIAL_POST_TYPE,
            'normal',
            'high'
        );
    }

    public function renderMetadataBox($post)
    {
        ?>
        <p>
            <label for=""><?php _e('Testimony Name', 'ramphor_testimonials'); ?></label>
            <input
                type="text"
                name="testimony_name"
                class="widefat"
                value="<?php echo sanitize_text_field(get_post_meta($post->ID, 'testimony_name', true)); ?>"
            />
        </p>
        <p>
            <label for=""><?php _e('Testimony\'s Company', 'ramphor_testimonials'); ?></label>
            <input
                type="text"
                class="widefat"
                name="testimonial_company"
                value="<?php echo sanitize_text_field(get_post_meta($post->ID, 'testimonial_company', true)); ?>"
            />
        </p>
        <p>
            <label for=""><?php _e('Position', 'ramphor_testimonials'); ?></label>
            <input
                type="text"
                class="widefat"
                name="testimonial_position"
                value="<?php echo sanitize_text_field(get_post_meta($post->ID, 'testimonial_position', true)); ?>"
            />
        </p>
        <?php
    }

    public function saveTestimonialMetas($post_id, $post)
    {
        if (static::TESTIMONIAL_POST_TYPE !== $post->post_type) {
            return;
        }
        if (isset($_POST['testimony_name'])) {
            $testimony_name = $_POST['testimony_name'];
            if (!empty($testimony_name)) {
                update_post_meta($post_id, 'testimony_name', $testimony_name);
            }
        }
        if (isset($_POST['testimonial_company'])) {
            $testimonial_company = $_POST['testimonial_company'];
            if (!empty($testimonial_company)) {
                update_post_meta($post_id, 'testimonial_company', $testimonial_company);
            }
        }
        if (isset($_POST['testimonial_position'])) {
            $testimonial_position = $_POST['testimonial_position'];
            if (!empty($testimonial_position)) {
                update_post_meta($post_id, 'testimonial_position', $testimonial_position);
            }
        }
    }
}
