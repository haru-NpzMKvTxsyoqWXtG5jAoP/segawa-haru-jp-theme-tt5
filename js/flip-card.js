/**
 * フリップカード機能
 */
document.addEventListener('DOMContentLoaded', function() {
  const flipCard = document.querySelector('.haru-flip-card');
  
  if (flipCard) {
    // フォーカス可能にする
    flipCard.setAttribute('tabindex', '0');
    
    flipCard.addEventListener('click', function() {
      this.classList.toggle('flipped');
    });
    
    flipCard.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();  // Space がスクロールしないように
        flipCard.click();    // 既存 click ハンドラを再利用
      }
    });
  }
});