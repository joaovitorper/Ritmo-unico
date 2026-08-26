document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(".onboarding-slide");
    const nextButton = document.getElementById("nextButton");
    const prevButton = document.getElementById("prevButton");
    const skipButton = document.getElementById("skipButton");
    const indicators = document.querySelectorAll(".indicator");

    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle("active", i === index);
        });

        indicators.forEach((indicator, i) => {
            indicator.classList.toggle("active", i === index);
        });

        if (prevButton) {
            prevButton.style.display = index === 0 ? "none" : "block";
        }

        if (nextButton) {
            nextButton.textContent =
                index === slides.length - 1 ? "Começar" : "Próximo";
        }
    }

    if (nextButton) {
        nextButton.addEventListener("click", () => {
            if (currentSlide < slides.length - 1) {
                currentSlide++;
                showSlide(currentSlide);
            } else {
                window.location.href = "login.php";
            }
        });
    }

    if (prevButton) {
        prevButton.addEventListener("click", () => {
            if (currentSlide > 0) {
                currentSlide--;
                showSlide(currentSlide);
            }
        });
    }

    if (skipButton) {
        skipButton.addEventListener("click", () => {
            window.location.href = "login.php";
        });
    }

    indicators.forEach((indicator, index) => {
        indicator.addEventListener("click", () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });

    showSlide(currentSlide);
});