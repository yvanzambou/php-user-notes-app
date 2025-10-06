document.addEventListener("DOMContentLoaded", () => {
    const msg = document.getElementById("flash-message");
    if (msg) {
        setTimeout(() => {
            msg.style.transition = "opacity 1s";
            msg.style.opacity = "0";
            setTimeout(() => msg.remove(), 1000);
        }, 5000);
    }
});