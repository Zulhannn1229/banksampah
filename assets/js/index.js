document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = document.getElementById('heroCarousel');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 5000,
        wrap: true
    });
});

function toggleReadMore(button) {
    const container = button.closest('.card-text-container');
    const preview = container.querySelector('.card-text-preview');
    const moreText = container.querySelector('.card-text-more');
    
    if (moreText.style.display === 'inline') {
        moreText.style.display = 'none';
        button.textContent = 'Baca selengkapnya';
    } else {
        moreText.style.display = 'inline';
        button.textContent = 'Sembunyikan';
    }
}