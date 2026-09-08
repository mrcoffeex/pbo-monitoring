import "./bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    const root = document.querySelector("[data-hero-carousel]");

    if (!root) {
        return;
    }

    const slides = Array.from(root.querySelectorAll("[data-hero-slide]"));
    const dots = Array.from(root.querySelectorAll("[data-hero-dot]"));

    if (slides.length < 2) {
        return;
    }

    let index = 0;
    let timer = null;
    const intervalMs = 7000;
    const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;

    const show = (next) => {
        index = (next + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle("is-active", slideIndex === index);
        });

        dots.forEach((dot, dotIndex) => {
            dot.setAttribute(
                "aria-current",
                dotIndex === index ? "true" : "false",
            );
        });
    };

    const stop = () => {
        if (timer !== null) {
            clearInterval(timer);
            timer = null;
        }
    };

    const start = () => {
        if (prefersReducedMotion) {
            return;
        }

        stop();
        timer = setInterval(() => show(index + 1), intervalMs);
    };

    dots.forEach((dot, dotIndex) => {
        dot.addEventListener("click", () => {
            show(dotIndex);
            start();
        });
    });

    root.addEventListener("mouseenter", stop);
    root.addEventListener("mouseleave", start);
    root.addEventListener("focusin", stop);
    root.addEventListener("focusout", start);

    show(0);
    start();
});
