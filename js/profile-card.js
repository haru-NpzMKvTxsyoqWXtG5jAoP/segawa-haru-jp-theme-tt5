document.addEventListener('DOMContentLoaded', function () {
  const cardContainers = document.querySelectorAll('.haru-card-container');
  
  cardContainers.forEach(container => {
    container.addEventListener('click', () => {
      const card = container.querySelector('.haru-card');
      if (card) {
        card.classList.toggle('is-flipped');
      }
    });
  });
}); 