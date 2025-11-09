
<!--====== Start Newsletter Section ======-->
        <section class="newsletter-section">
            <div class="newsletter-wrapper-two p-r z-1 pt-80 pb-85">
                <div class="newsletter-image-two" data-aos="fade-left" data-aos-duration="1200"><img
                        src="assets/images/newsletter/newsletter-2.jpg" alt="newsletter image"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="section-content-box" data-aos="fade-up" data-aos-duration="1000">
                                <!--=== Section Title  ===-->
                                <div class="section-title">
                                    <div class="sub-heading d-inline-flex align-items-center">
                                        <i class="flaticon-sparkler"></i>
                                        <span class="sub-title" style="color: #13172B;">Join Our
                                            Newsletter</span>
                                    </div>
                                    <h3 class="mt-2 mb-3 fw-bold" style="color: #13172B;">
                                        Stay Updated With The Latest <span style="color: #de3576;">Trends &
                                            Offers</span>
                                    </h3>
                                    <p class="text-muted mb-4" style="color: #13172B;">
                                        Subscribe to receive weekly updates on new arrivals, exclusive discounts, and
                                        special offers from top Pakistani brands.
                                    </p>
                                </div>
                                <form action="https://api.web3forms.com/submit" method="POST">
                                    <input type="hidden" name="access_key" value="e8692be6-515c-4244-a7ba-6497ae5b8e20">
                                    <input type="email" class="form_control" placeholder="Write your Email Address"
                                        name="email" required>
                                    <button type="submit" class="theme-btn style-one">Subscribe</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--====== End Newsletter Section ======-->

<script>
const form = document.querySelector('form[action="https://api.web3forms.com/submit"]');
const msgBox = document.createElement('div');
form.appendChild(msgBox);

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    msgBox.textContent = "⏳ Submitting...";
    msgBox.style.color = "#777";

    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, { method: 'POST', body: formData });
        const text = await res.text();

        // check if success is in response text
        if (text.includes("success")) {
            msgBox.textContent = "🎉 Thank you for subscribing!";
            msgBox.style.color = "pink";
            form.reset();
        } else {
            msgBox.textContent = "⚠️ Something went wrong. Try again.";
            msgBox.style.color = "red";
        }
    } catch (err) {
        msgBox.textContent = "⚠️ Network error. Please try again.";
        msgBox.style.color = "red";
    }
});
</script>

