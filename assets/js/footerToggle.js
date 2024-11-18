document.addEventListener("DOMContentLoaded", function() {
    const toggleTitles = document.querySelectorAll(".toggleTitle");

    toggleTitles.forEach(title => {
        title.addEventListener("click", () => {
            const content = title.nextElementSibling;
            const icon = title.querySelector(".toggleIcon");
            
            content.classList.toggle("active");
            icon.classList.toggle("rotate");
        });
    });
});
