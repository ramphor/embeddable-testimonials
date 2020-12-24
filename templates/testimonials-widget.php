<div class="testimonials-widget">
    <?php echo $header; ?>
    <div <?php echo $wrapper_attributes; ?>>
        <?php if ($wp_query->have_posts()) : ?>
            <?php do_action('testimonials_before_loop', $wp_query); ?>

            <?php while ($wp_query->have_posts()) : ?>
                <?php $wp_query->the_post(); ?>

                <?php do_action('testimonials_before_loop_item', $wp_query); ?>

                <?php $t::render('content/testimonial', array(
                    'post' => $wp_query->post
                )); ?>

                <?php do_action('testimonials_after_loop_item', $wp_query); ?>

            <?php endwhile; ?>

            <?php do_action('testimonials_after_loop', $wp_query); ?>

        <?php else : ?>
            <?php $t::render('common/not_found'); ?>
        <?php endif; ?>
    </div>
</div>