/**
 * public/js/matching.js
 * Alpine component: matchingGame
 *
 * Mục tiêu:
 * - Ổn định đăng ký component dù script load trước/sau Alpine.
 * - Không dùng biến global "state"; toàn bộ state nằm trong object return.
 * - Khi hoàn thành (done), gọi $wire.submitMatching(...) và BẮT lỗi Promise
 *   để UI không bị kẹt nếu server trả 422/403/500.
 */
(function () {
  function createComponent() {
    const defaults = {
      pairs: [],        // [{ id: 1, left: '猫', right: 'cat' }, ...]
      shuffle: true,
      revealMillis: 600,
    };

    function shuffle(arr) {
      for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
      }
      return arr;
    }

    return (initial = {}) => {
      const cfg = Object.assign({}, defaults, initial || {});
      // chuẩn hoá dữ liệu vào
      const base = (cfg.pairs || []).map((p, idx) => ({
        id: Number(p.id ?? idx + 1),
        left: String(p.left ?? ''),
        right: String(p.right ?? ''),
      })).filter(p => p.left !== '' && p.right !== '');
      let left = base.map(p => ({ id: p.id, text: p.left }));
      let right = base.map(p => ({ id: p.id, text: p.right }));
      if (cfg.shuffle) { shuffle(left); shuffle(right); }

      return {
        // ===== STATE =====
        totalPairs: base.length,
        left,
        right,
        selectedLeft: null,
        selectedRight: null,
        matches: [],            // [{ leftId, rightId, correct }]
        correctCount: 0,
        locked: false,

        // ===== COMPUTED =====
        get done() {
          return this.matches.filter(m => m.correct).length === this.totalPairs;
        },

        // ===== ACTIONS =====
        selectLeft(id) {
          if (this.locked) return;
          this.selectedLeft = id;
          this.tryMatch();
        },
        selectRight(id) {
          if (this.locked) return;
          this.selectedRight = id;
          this.tryMatch();
        },
        tryMatch() {
          if (this.selectedLeft == null || this.selectedRight == null) return;

          const correct = this.selectedLeft === this.selectedRight;
          this.matches.push({ leftId: this.selectedLeft, rightId: this.selectedRight, correct });
          if (correct) this.correctCount++;

          this.locked = true;
          setTimeout(() => {
            // loại cặp đã chọn khỏi 2 cột (dù đúng hay sai)
            this.left = this.left.filter(i => i.id !== this.selectedLeft);
            this.right = this.right.filter(i => i.id !== this.selectedRight);
            this.selectedLeft = null;
            this.selectedRight = null;

            // Nếu đã hoàn thành: cố gắng submit; nếu fail thì mở khoá lại
            if (this.done && this.$wire && typeof this.$wire.submitMatching === 'function') {
              const itemId = Number(this.$root?.dataset?.itemId || 0);

              // Giữ locked trong lúc đợi server
              this.$wire.submitMatching(itemId, this.matches)
                .then(() => {
                  // OK → Livewire sẽ render item mới (nếu component làm vậy)
                  // Không reset tại đây để tránh đua với Livewire re-render
                })
                .catch((err) => {
                  console.error('submitMatching failed:', err);
                  this.locked = false; // Mở lại cho phép bấm "Làm lại" hoặc thử lại
                  // Gợi ý phát một sự kiện toast nếu bạn có listener global
                  try {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Submit failed' } }));
                  } catch (_) {}
                });
            } else {
              // Chưa xong → mở khoá cho lượt chọn kế tiếp
              this.locked = false;
            }
          }, cfg.revealMillis);
        },
        reset() {
          this.selectedLeft = this.selectedRight = null;
          this.matches = [];
          this.correctCount = 0;
          this.locked = false;
          this.left = base.map(p => ({ id: p.id, text: p.left }));
          this.right = base.map(p => ({ id: p.id, text: p.right }));
          if (cfg.shuffle) { shuffle(this.left); shuffle(this.right); }
        },

        // Tuỳ chọn: chỉ random lại cột phải
        shuffleRight() {
          if (this.locked) return;
          this.right = shuffle(this.right.slice());
        }
      };
    };
  }

  function register() {
    if (!window.Alpine) return;
    window.Alpine.data('matchingGame', createComponent());
  }

  if (window.Alpine) {
    // Trường hợp Alpine đã có sẵn (module/Vite start rồi)
    register();
  } else {
    // Trường hợp load match.js trước Alpine (CDN)
    document.addEventListener('alpine:init', register, { once: true });
  }

  // Tiện debug trong DevTools
  try { window.__matchingFactory = createComponent; } catch (_) {}
})();
