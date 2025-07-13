/**
 * フリップカード機能
 */
document.addEventListener('DOMContentLoaded', function() {
  const flipCard = document.querySelector('.haru-flip-card');
  
  if (flipCard) {
    flipCard.addEventListener('click', function() {
      this.classList.toggle('flipped');
    });
  }
});