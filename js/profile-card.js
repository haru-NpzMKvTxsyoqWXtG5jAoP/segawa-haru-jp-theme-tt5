document.addEventListener('DOMContentLoaded', function() {
  // プロフィールカード対応（haru-プレフィックス対応）
  const profileCardContainers = document.querySelectorAll('.haru-profile-card-container');
  
  profileCardContainers.forEach(container => {
    container.addEventListener('click', () => {
      const card = container.querySelector('.haru-profile-card');
      if (card) {
        card.classList.toggle('is-flipped');
      }
    });
  });

  // 直接プロフィールカードをクリック対応
  const profileCards = document.querySelectorAll('.haru-profile-card');
  
  profileCards.forEach(card => {
    // クリックイベント
    card.addEventListener('click', function() {
      this.classList.toggle('is-flipped');
    });
    
    // キーボードアクセシビリティ対応
    card.addEventListener('keydown', function(event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        this.classList.toggle('is-flipped');
      }
    });
    
    // アクセシビリティ属性を設定
    card.setAttribute('tabindex', '0');
    card.setAttribute('role', 'button');
    card.setAttribute('aria-label', 'プロフィールカードをフリップ');
  });
}); 