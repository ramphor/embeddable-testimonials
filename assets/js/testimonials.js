function ramphor_testimonial_start_update_rate() {
}
function ramphor_testimonial_end_update_rate() {
}

function ramphor_set_star_rating(rating, done = ramphor_testimonial_end_update_rate) {
    // var testimonial_rating; This is global variable
    testimonials_global = window.testimonials_global || {};
    if (!testimonials_global.set_rate_url || !testimonials_global.post_id) {
        return;
    }
    ramphor_testimonial_start_update_rate();

    var xhr = new XMLHttpRequest();

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            response = JSON.parse(xhr.response);
            if (response.success) {
                if (response.data > 0) {
                    testimonial_rating.setRating(response.data);
                }
            }
        }

        if (typeof done === 'function') {
            done();
        }
    }

    xhr.open('POST', testimonials_global.set_rate_url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({
        rating: rating,
        post_id: testimonials_global.post_id,
        nonce: testimonials_global.current_nonce,
    }));
}
