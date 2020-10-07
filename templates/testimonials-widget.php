<div class="testimonials-widget">
    <?php echo $header; ?>
    <div <?php echo $wrapper_attributes; ?>>
        <?php if ($wp_query->have_posts()) : ?>
            <?php do_action('testimonials_before_loop'); ?>

            <?php while ($wp_query->have_posts()) : ?>
                <?php $wp_query->the_post(); ?>

                <?php do_action('testimonials_before_loop_item'); ?>

                <?php $t::render('content/testimonial'); ?>

                <?php do_action('testimonials_after_loop_item'); ?>

            <?php endwhile; ?>

            <?php do_action('testimonials_after_loop'); ?>

        <?php else : ?>
            <?php $t::render('common/not_found'); ?>
        <?php endif; ?>
    </div>
</div>