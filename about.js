// const header = document.querySelector('header');

// function toggleStickyHeader() {
//     if (window.scrollY > 0) {
//         header.classList.add('sticky');
//     } else {
//         header.classList.remove('sticky');
//     }
// }

// // Toggle sticky header on scroll
// window.addEventListener('scroll', toggleStickyHeader);

// // Initial call to set sticky header based on initial position
// toggleStickyHeader();

// counter

const countElements = document.querySelectorAll('.count-number');
    const targetCounts = [90, 50, 1000, 100]; // Adjust target counts for each section
    const countInterval = 20; // Adjust the interval as needed (milliseconds)

    countElements.forEach((countElement, index) => {
        let currentCount = 0;
        const targetCount = targetCounts[index];

        function updateCount() {
            if (currentCount <= targetCount) {
                countElement.textContent = currentCount;
                currentCount++;
                setTimeout(updateCount, countInterval);
            }
        }

        updateCount();
    });

    // Carousel testimonial 

    const testimonialCarousel = document.querySelector('.testimonial-carousel');
const prevButton = document.querySelector('.prev-btn');
const nextButton = document.querySelector('.next-btn');

let currentPosition = 0;

function moveCarousel(direction) {
    const slideWidth = testimonialCarousel.querySelector('.testimonial').offsetWidth;
    currentPosition += direction * slideWidth;

    if (currentPosition > 0) {
        currentPosition = -slideWidth * (testimonialCarousel.children.length - 1);
    } else if (currentPosition < -slideWidth * (testimonialCarousel.children.length - 1)) {
        currentPosition = 0;
    }

    testimonialCarousel.style.transform = `translateX(${currentPosition}px)`;
}

nextButton.addEventListener('click', () => moveCarousel(-1));
prevButton.addEventListener('click', () => moveCarousel(1));

setInterval(() => {
    moveCarousel(-1);
}, 2000); // Change interval to 2 seconds (2000 milliseconds)


