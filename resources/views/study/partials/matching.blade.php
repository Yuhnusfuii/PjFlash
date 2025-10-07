{{-- MATCHING – IIFE version: không cần Alpine.data, không cần JS ngoài --}}

@php
    $raw = $item->data['matching'] ?? null;
    $pairsCandidate = [];

    if (is_array($raw)) {
        if (isset($raw['pairs']) && is_array($raw['pairs'])) {
            $pairsCandidate = $raw['pairs'];
        } elseif (array_is_list($raw)) {
            $pairsCandidate = $raw;
        } elseif ((isset($raw['lefts']) && is_array($raw['lefts'])) || (isset($raw['l']) && is_array($raw['l']))) {
            $lefts  = $raw['lefts'] ?? $raw['l'] ?? [];
            $rights = $raw['rights'] ?? ($raw['r'] ?? []);
            $max = min(count($lefts), count($rights));
            for ($i = 0; $i < $max; $i++) {
                $pairsCandidate[] = ['left' => $lefts[$i] ?? null, 'right' => $rights[$i] ?? null];
            }
        }
    }

    $pairsFiltered = array_values(array_filter(array_map(function ($p) {
        $left  = isset($p['left'])  ? (string)$p['left']  : (isset($p['l']) ? (string)$p['l'] : '');
        $right = isset($p['right']) ? (string)$p['right'] : (isset($p['r']) ? (string)$p['r'] : '');
        return ['left' => trim($left), 'right' => trim($right)];
    }, $pairsCandidate), fn($p) => $p['left'] !== '' && $p['right'] !== ''));

    $rightList = array_map(fn($p) => $p['right'], $pairsFiltered);
    $hasPayload = count($pairsFiltered) > 0;
@endphp

<div class="p-6 space-y-4 bg-white border shadow-sm rounded-2xl">
    <div class="text-xs text-gray-400 uppercase">Mode: Matching</div>
    <h3 class="text-lg font-semibold">Match the pairs</h3>

    @unless($hasPayload)
        <p class="text-sm text-gray-500">No Matching data for this item yet.</p>
        <button
            class="px-4 py-2 text-white bg-blue-600 rounded hover:opacity-90"
            x-data
            x-on:click="
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
                const res = await fetch('{{ route('api.items.matching.generate', $item) }}', {
                    method: 'POST',
                    headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
                    credentials: 'same-origin'
                });
                if (res.ok) { $wire.refreshCurrent?.(); } else { alert('Generate failed'); }
            "
        >Generate Matching</button>
        @php return; @endphp
    @endunless

    {{-- IIFE: tự tạo toàn bộ state + method ngay tại chỗ --}}
    <div
      x-cloak
      x-data="
        (() => {
          const P = @js($pairsFiltered);
          const R = @js($rightList);

          const left = (P || []).map(p => String(p.left));
          const rightOriginal = (R || []).map(r => String(r));
          const shuffled = (arr) => arr.map(v => ({ v, r: Math.random() }))
              .sort((a,b) => a.r - b.r).map(o => o.v);

          return {
            // ----- STATE -----
            state: {
              left,
              right: shuffled(rightOriginal),
              pairs: {},              // {leftIndex: rightIndex}
              activeLeft: null,
              activeRight: null,
              submitted: false,
              correctCount: 0,
              total: left.length,
              mappedGrade: 0,
              scoreRatio: 0,
              anyWrong: false,
            },

            // ----- HELPERS -----
            correctIndexForLeft(i) {
              const correctRight = String((P || [])[i]?.right ?? '');
              return this.state.right.findIndex(r => String(r) === correctRight);
            },
            hasPair(i) { return this.state.pairs[i] !== undefined; },
            pairIndexForRight(j) {
              for (const [i, rj] of Object.entries(this.state.pairs)) {
                if (Number(rj) === j) return Number(i);
              }
              return null;
            },
            isPickedRight(j) { return this.pairIndexForRight(j) !== null; },
            isLeftCorrect(i) {
              if (!this.state.submitted || !this.hasPair(i)) return false;
              return this.state.pairs[i] === this.correctIndexForLeft(i);
            },
            isRightCorrect(j) {
              if (!this.state.submitted) return false;
              const i = this.pairIndexForRight(j);
              if (i === null) return false;
              return this.isLeftCorrect(i);
            },
            badgeTextForRight(j) {
              const i = this.pairIndexForRight(j);
              return i === null ? '' : '#' + (i + 1);
            },
            badgeClassForRight(j) {
              if (!this.state.submitted) return 'bg-gray-100 border-gray-200 text-gray-500';
              return this.isRightCorrect(j)
                ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
                : 'bg-red-50 border-red-300 text-red-700';
            },

            // ----- ACTIONS -----
            pickLeft(i) {
              if (this.state.submitted) return;
              this.state.activeLeft = i;
              this.state.activeRight = null;
            },
            pickRight(j) {
              if (this.state.submitted) return;
              if (this.state.activeLeft === null) { this.state.activeRight = j; return; }
              const usedBy = this.pairIndexForRight(j);
              if (usedBy !== null) delete this.state.pairs[usedBy];
              this.state.pairs[this.state.activeLeft] = j;
              this.state.activeLeft = null;
              this.state.activeRight = null;
            },
            shuffle() {
              if (this.state.submitted) return;
              this.state.right = shuffled(this.state.right);
              this.state.pairs = {};
              this.state.activeLeft = null;
              this.state.activeRight = null;
            },
            resetAll() {
              this.state.pairs = {};
              this.state.activeLeft = null;
              this.state.activeRight = null;
              this.state.submitted = false;
              this.state.correctCount = 0;
              this.state.mappedGrade = 0;
              this.state.scoreRatio = 0;
              this.state.anyWrong = false;
            },
            submit() {
              let correct = 0, total = this.state.total;
              for (let i = 0; i < total; i++) {
                if (this.state.pairs[i] !== undefined &&
                    this.state.pairs[i] === this.correctIndexForLeft(i)) {
                  correct++;
                }
              }
              const ratio = total > 0 ? correct / total : 0;
              let g = 1;
              if (ratio >= 0.9) g = 5;
              else if (ratio >= 0.6) g = 3;
              else if (ratio >= 0.3) g = 2;
              else g = 1;

              this.state.correctCount = correct;
              this.state.scoreRatio  = ratio;
              this.state.mappedGrade = g;
              this.state.submitted   = true;
              this.state.anyWrong    = correct < total;

              if (window.$wire && typeof $wire.grade === 'function') {
                $wire.grade(g);
              }
            },
          };
        })()
      "
      class="space-y-4"
    >
      <div class="text-[11px] text-gray-500">
        total: <span x-text="state.total"></span>
      </div>

      <div class="grid grid-cols-2 gap-4">
        {{-- LEFT --}}
        <div>
          <div class="mb-2 text-xs text-gray-400 uppercase">Left</div>
          <ul class="space-y-2">
            <template x-for="(p, i) in state.left" :key="i">
              <li>
                <button
                  type="button"
                  class="w-full px-3 py-2 text-left transition border rounded"
                  :class="{
                    'bg-gray-100 border-gray-400': state.activeLeft === i && !state.submitted,
                    'bg-emerald-50 border-emerald-300': state.submitted && isLeftCorrect(i),
                    'bg-red-50 border-red-300': state.submitted && !isLeftCorrect(i) && hasPair(i),
                  }"
                  @click="pickLeft(i)"
                  :disabled="state.submitted"
                  x-text="p"
                ></button>
              </li>
            </template>
            <li class="text-xs text-red-600" x-show="state.left.length === 0">No LEFT.</li>
          </ul>
        </div>

        {{-- RIGHT --}}
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="text-xs text-gray-400 uppercase">Right</div>
            <button
              type="button"
              class="px-2 py-1 text-xs border rounded hover:bg-gray-50"
              @click="shuffle()"
              :disabled="state.submitted"
              title="Shuffle"
            >Shuffle</button>
          </div>

          <ul class="space-y-2">
            <template x-for="(txt, j) in state.right" :key="j">
              <li>
                <button
                  type="button"
                  class="w-full px-3 py-2 text-left transition border rounded"
                  :class="{
                    'ring-2 ring-gray-400': state.activeRight === j && !state.submitted,
                    'bg-emerald-50 border-emerald-300': state.submitted && isRightCorrect(j),
                    'bg-red-50 border-red-300': state.submitted && !isRightCorrect(j) && isPickedRight(j),
                  }"
                  @click="pickRight(j)"
                  :disabled="state.submitted"
                >
                  <span x-text="txt"></span>
                  <span
                    class="ml-2 inline-block text-[10px] px-1.5 py-0.5 rounded-full border align-middle"
                    :class="badgeClassForRight(j)"
                    x-text="badgeTextForRight(j)"
                    x-show="pairIndexForRight(j) !== null"
                  ></span>
                </button>
              </li>
            </template>
            <li class="text-xs text-red-600" x-show="state.right.length === 0">No RIGHT.</li>
          </ul>
        </div>
      </div>

      <div class="flex items-center gap-2 pt-2">
        <button
          type="button"
          class="px-4 py-2 text-white bg-black rounded hover:opacity-90 disabled:opacity-50"
          @click="submit()"
          :disabled="state.submitted || Object.keys(state.pairs).length === 0"
        >Submit</button>

        <button
          type="button"
          class="px-3 py-2 border rounded hover:bg-gray-50 disabled:opacity-50"
          @click="resetAll()"
          :disabled="state.submitted && !state.anyWrong"
        >Reset</button>
      </div>
    </div>
</div>
