<div class="testimonial">
    <?php if (has_post_thumbnail()) : ?>
        <div class="testimonial-thumbnail">
            <?php the_post_thumbnail(); ?>
        </div>
    <?php endif; ?>
    <div class="rating"></div>
    <div class="desc"><?php the_content(); ?></div>
    <div class="position">
        <span class="testimony"><?php the_title(); ?></span>
    </div>
</div>
