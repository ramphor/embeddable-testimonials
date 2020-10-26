<div class="testimonial">
    <?php if (has_post_thumbnail()) : ?>
        <div class="testimonial-thumbnail">
            <?php the_post_thumbnail('thumbnail'); ?>
        </div>
    <?php endif; ?>
    <div class="rating"><?php echo $e->display('testimonials-rating', array()); ?></div>
    <div class="desc"><?php the_content(); ?></div>
    <div class="position">
        <span class="testimony"><?php the_title(); ?></span>
    </div>
</div>
