document.addEventListener("DOMContentLoaded", () => {

    const messages = document.querySelectorAll(".coupon-message");

    if (messages.length > 0) {
        messages.forEach(msg => {

            // auto hide after 3 seconds
            setTimeout(() => {
                msg.style.opacity = "0";
                msg.style.transform = "translateY(-10px)";

                // remove element after fade
                setTimeout(() => {
                    msg.remove();
                }, 400);

            }, 3000);
        });
    }

});
