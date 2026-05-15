{{-- Stylised Australia map. Pass $highlight (state slug: nsw/vic/qld/wa/sa/tas/act/nt) to colour that region. --}}
@php $h = $highlight ?? null; @endphp
<svg class="pt-au-map h-{{ $h }}" viewBox="0 0 600 480" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Map of Australia with {{ $h }} highlighted">
    {{-- Western Australia (big block on the left) --}}
    <path data-state="wa" d="M 30 150 Q 40 110, 90 95 L 240 92 L 240 365 Q 200 380, 160 376 L 80 358 Q 32 320, 28 260 Z"/>
    {{-- Northern Territory (top middle) --}}
    <path data-state="nt" d="M 240 92 L 380 92 L 380 240 L 240 240 Z"/>
    {{-- South Australia (middle) --}}
    <path data-state="sa" d="M 240 240 L 380 240 L 380 360 L 280 380 L 240 365 Z"/>
    {{-- Queensland (top right) --}}
    <path data-state="qld" d="M 380 92 L 510 96 Q 545 130, 548 180 L 542 242 L 380 242 Z"/>
    {{-- New South Wales (middle right) --}}
    <path data-state="nsw" d="M 380 242 L 542 242 L 532 302 L 470 332 L 380 332 Z"/>
    {{-- Victoria (bottom right) --}}
    <path data-state="vic" d="M 380 332 L 470 332 L 458 382 L 350 386 L 380 360 Z"/>
    {{-- Tasmania (island below) --}}
    <ellipse data-state="tas" cx="430" cy="438" rx="38" ry="22"/>
    {{-- ACT (small circle inside NSW) --}}
    <circle data-state="act" cx="502" cy="292" r="7"/>
    {{-- State labels --}}
    <text x="135" y="240" class="pt-au-label">WA</text>
    <text x="307" y="170" class="pt-au-label pt-au-label-sm">NT</text>
    <text x="307" y="310" class="pt-au-label">SA</text>
    <text x="455" y="170" class="pt-au-label">QLD</text>
    <text x="455" y="290" class="pt-au-label">NSW</text>
    <text x="410" y="365" class="pt-au-label pt-au-label-sm">VIC</text>
    <text x="430" y="443" class="pt-au-label pt-au-label-xs">TAS</text>
</svg>
