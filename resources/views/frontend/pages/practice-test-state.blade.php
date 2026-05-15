@extends('layouts.frontend')

@section('title', $stateName . ' ' . $testName . ' — Free Practice Test | Secure Licences')

@section('content')

{{-- ─────────── HERO (sky + city + yellow car + form card) ─────────── --}}
<section class="pts-hero">
    <div class="container">
        <div class="row align-items-stretch">
            <div class="col-lg-7 pts-hero-text">
                <nav aria-label="breadcrumb" class="pts-hero-breadcrumb mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Secure Licences</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('practice-test') }}">Learner Test</a></li>
                        <li class="breadcrumb-item active">{{ $stateCode }}</li>
                    </ol>
                </nav>
                <h1 class="pts-hero-title">
                    <small class="pts-hero-state">{{ strtoupper($stateName) }}</small>
                    <span class="pts-hero-testname">{{ $testName }}</span>
                </h1>
            </div>

            <div class="col-lg-5 pts-hero-form-col">
                {{-- Curved arrow pointing to form (desktop only) --}}
                <svg class="pts-hero-arrow d-none d-lg-block" viewBox="0 0 140 80" aria-hidden="true">
                    <path d="M5 60 Q 70 5, 130 30" fill="none" stroke="#fbbf24" stroke-width="4" stroke-linecap="round"/>
                    <path d="M120 18 L132 30 L120 42" fill="none" stroke="#fbbf24" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <div class="pts-form-card">
                    <h2 class="pts-form-title">FREE Online Practice<br>{{ $testName }}</h2>
                    <form id="pts-start-form" action="#pts-quiz" class="pts-form" novalidate>
                        <input type="text" name="first_name" class="form-control" placeholder="Your first name" required>
                        <div class="pts-form-group">
                            <i class="bi bi-envelope pts-form-icon"></i>
                            <input type="email" name="email" class="form-control" placeholder="Email address" required>
                        </div>
                        <select name="test_date" class="form-select" required>
                            <option value="">I plan to sit my test ...</option>
                            <option value="within_two">Within 2 weeks</option>
                            <option value="two_to_four">2 – 4 weeks time</option>
                            <option value="four_to_eight">4 – 8 weeks time</option>
                            <option value="more_than_eight">8 weeks+ time</option>
                        </select>
                        <button type="submit" class="btn btn-warning fw-bold w-100 pts-form-submit">Begin Free Test</button>
                    </form>
                    <hr class="my-3">
                    <div class="pts-form-fb">
                        <i class="bi bi-facebook"></i>
                        <span>Like our page to receive regular learner test questions &amp; driving lesson discounts</span>
                        <a href="https://www.facebook.com/securelicences" target="_blank" rel="noopener" class="pts-form-fb-btn">
                            <i class="bi bi-hand-thumbs-up-fill"></i> Like
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Decorative city skyline + yellow car illustration --}}
    <svg class="pts-hero-bg" viewBox="0 0 1200 280" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        {{-- Buildings silhouette (back) --}}
        <g fill="#0e7490">
            <rect x="80"  y="120" width="60"  height="160"/>
            <rect x="160" y="80"  width="80"  height="200"/>
            <rect x="260" y="110" width="50"  height="170"/>
            <rect x="330" y="60"  width="100" height="220"/>
            <rect x="450" y="100" width="70"  height="180"/>
            <rect x="540" y="130" width="60"  height="150"/>
            <rect x="620" y="80"  width="90"  height="200"/>
            <rect x="730" y="100" width="60"  height="180"/>
            <rect x="810" y="60"  width="100" height="220"/>
            <rect x="930" y="110" width="70"  height="170"/>
            <rect x="1020" y="80" width="80"  height="200"/>
        </g>
        {{-- Building windows (yellow dots) --}}
        <g fill="#fbbf24" opacity="0.55">
            @for($x = 90; $x < 1100; $x += 18)
                @for($y = 130; $y < 270; $y += 22)
                    <rect x="{{ $x }}" y="{{ $y }}" width="6" height="8" rx="1"/>
                @endfor
            @endfor
        </g>
        {{-- Traffic light pole (left) --}}
        <g transform="translate(40, 130)">
            <rect x="0" y="0" width="28" height="60" rx="6" fill="#1f2937"/>
            <circle cx="14" cy="14" r="6" fill="#ef4444"/>
            <circle cx="14" cy="30" r="6" fill="#fbbf24"/>
            <circle cx="14" cy="46" r="6" fill="#22c55e"/>
            <rect x="11" y="60" width="6" height="120" fill="#374151"/>
        </g>
        {{-- Trees --}}
        <g fill="#10b981">
            <circle cx="180" cy="220" r="38"/>
            <circle cx="195" cy="200" r="28"/>
            <rect x="183" y="240" width="6" height="40" fill="#7c2d12"/>
        </g>
        <g fill="#10b981">
            <circle cx="1130" cy="220" r="42"/>
            <circle cx="1110" cy="195" r="30"/>
            <rect x="1128" y="240" width="6" height="40" fill="#7c2d12"/>
        </g>
        {{-- Road --}}
        <rect x="0" y="240" width="1200" height="40" fill="#374151"/>
        <g stroke="#fbbf24" stroke-width="3" stroke-dasharray="20 14">
            <line x1="0" y1="260" x2="1200" y2="260"/>
        </g>
        {{-- Yellow car (centred) --}}
        <g transform="translate(540, 175)">
            {{-- Body lower (yellow) --}}
            <path d="M 0 50 L 12 30 L 38 22 L 100 22 L 130 30 L 142 50 Z" fill="#fbbf24"/>
            {{-- Body upper (white) --}}
            <path d="M 24 30 L 38 14 L 95 14 L 110 30 Z" fill="#fff" stroke="#1f2937" stroke-width="1"/>
            {{-- Outline --}}
            <path d="M 0 50 L 12 30 L 38 22 L 100 22 L 130 30 L 142 50" fill="none" stroke="#1f2937" stroke-width="2"/>
            {{-- Window split --}}
            <line x1="66" y1="14" x2="66" y2="30" stroke="#1f2937" stroke-width="1.5"/>
            {{-- L badge --}}
            <rect x="56" y="36" width="22" height="14" rx="2" fill="#fff" stroke="#1f2937" stroke-width="1"/>
            <text x="67" y="47" font-size="12" font-weight="900" fill="#1f2937" text-anchor="middle">L</text>
            {{-- Wheels --}}
            <circle cx="32" cy="52" r="11" fill="#1f2937"/>
            <circle cx="32" cy="52" r="5" fill="#6b7280"/>
            <circle cx="110" cy="52" r="11" fill="#1f2937"/>
            <circle cx="110" cy="52" r="5" fill="#6b7280"/>
        </g>
        {{-- Cones --}}
        <g>
            <polygon points="450,265 460,235 470,265" fill="#f97316"/>
            <rect x="448" y="265" width="24" height="4" fill="#1f2937"/>
            <polygon points="720,265 730,235 740,265" fill="#f97316"/>
            <rect x="718" y="265" width="24" height="4" fill="#1f2937"/>
        </g>
    </svg>
</section>

{{-- ─────────── FEATURES STRIP (dark navy, 4 icons) ─────────── --}}
<section class="pts-features">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-lg-3">
                <i class="bi bi-mortarboard-fill pts-feature-icon"></i>
                <h3 class="pts-feature-text">Get your L's first time</h3>
            </div>
            <div class="col-6 col-lg-3">
                <i class="bi bi-award-fill pts-feature-icon"></i>
                <h3 class="pts-feature-text">Questions based on the real test</h3>
            </div>
            <div class="col-6 col-lg-3">
                <i class="bi bi-file-earmark-text-fill pts-feature-icon"></i>
                <h3 class="pts-feature-text">Delivered in the correct format</h3>
            </div>
            <div class="col-6 col-lg-3">
                <i class="bi bi-arrow-repeat pts-feature-icon"></i>
                <h3 class="pts-feature-text">Unlimited attempts</h3>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── WHAT IS THE TEST? ─────────── --}}
<section class="pts-info-section pts-info-alt">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h3 class="pts-info-heading">What is the {{ $testName }}?</h3>
                <p class="pts-info-text">{{ $whatText }}</p>
            </div>
            <div class="col-lg-6 text-center">
                {{-- Isometric driving track illustration --}}
                <svg class="pts-info-illu" viewBox="0 0 360 260" aria-hidden="true">
                    <defs>
                        <linearGradient id="trackTop" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#4b5563"/>
                            <stop offset="1" stop-color="#374151"/>
                        </linearGradient>
                    </defs>
                    {{-- Base block --}}
                    <polygon points="60,90 180,30 300,90 180,150" fill="#9ca3af"/>
                    <polygon points="60,90 60,150 180,210 180,150" fill="#7c2d12"/>
                    <polygon points="180,150 300,90 300,150 180,210" fill="#92400e"/>
                    {{-- Track top --}}
                    <polygon points="80,95 180,45 280,95 180,145" fill="url(#trackTop)"/>
                    {{-- Road paths on top (yellow lines) --}}
                    <g stroke="#fbbf24" stroke-width="2" stroke-dasharray="4 3" fill="none">
                        <path d="M 110 105 L 180 65 L 240 95 L 200 115 L 250 135"/>
                        <path d="M 130 130 L 175 110 L 210 130"/>
                    </g>
                    {{-- Start/finish lines --}}
                    <rect x="100" y="100" width="14" height="8" fill="#fbbf24"/>
                    <rect x="100" y="100" width="14" height="2" fill="#1f2937"/>
                    <rect x="100" y="105" width="14" height="2" fill="#1f2937"/>
                    {{-- Tiny tree --}}
                    <circle cx="252" cy="92" r="6" fill="#10b981"/>
                </svg>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── HOW DO YOU PASS? ─────────── --}}
<section class="pts-info-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 text-center order-lg-1">
                <svg class="pts-info-illu" viewBox="0 0 320 280" aria-hidden="true">
                    {{-- Winding road --}}
                    <path d="M 80 250 Q 180 210, 140 160 Q 90 110, 180 70 Q 250 40, 200 20"
                          fill="none" stroke="#1f2937" stroke-width="32" stroke-linecap="round"/>
                    <path d="M 80 250 Q 180 210, 140 160 Q 90 110, 180 70 Q 250 40, 200 20"
                          fill="none" stroke="#fbbf24" stroke-width="3" stroke-dasharray="10 8"/>
                    {{-- Sign 1 --}}
                    <g transform="translate(40, 170)">
                        <rect x="6" y="0" width="3" height="40" fill="#1f2937"/>
                        <polygon points="-15,0 25,0 5,-25" fill="#fbbf24" stroke="#1f2937" stroke-width="2"/>
                        <path d="M -2 -12 Q 5 -18, 12 -12 Q 5 -6, -2 -12 Z" fill="#1f2937"/>
                    </g>
                    {{-- Sign 2 --}}
                    <g transform="translate(220, 100)">
                        <rect x="6" y="0" width="3" height="40" fill="#1f2937"/>
                        <polygon points="-15,0 25,0 5,-25" fill="#fbbf24" stroke="#1f2937" stroke-width="2"/>
                        <text x="5" y="-7" font-size="12" font-weight="900" fill="#1f2937" text-anchor="middle">!</text>
                    </g>
                    {{-- Sign 3 --}}
                    <g transform="translate(240, 200)">
                        <rect x="6" y="0" width="3" height="40" fill="#1f2937"/>
                        <polygon points="-15,0 25,0 5,-25" fill="#fbbf24" stroke="#1f2937" stroke-width="2"/>
                        <path d="M -2 -8 Q 5 -16, 12 -8 M 12 -8 Q 5 0, -2 -8" fill="none" stroke="#1f2937" stroke-width="2"/>
                    </g>
                </svg>
            </div>
            <div class="col-lg-6 order-lg-2">
                <h3 class="pts-info-heading">How do you pass the {{ $testName }}?</h3>
                <p class="pts-info-text">{{ $passText }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── EYESIGHT TEST ─────────── --}}
<section class="pts-info-section pts-info-alt">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h3 class="pts-info-heading">Eyesight Test</h3>
                <p class="pts-info-text">Good eyesight is essential for safe driving, so when you sit your {{ $testName }} your vision will also be checked. The eyesight test takes about 5 minutes and assesses both peripheral vision and colour recognition.</p>
            </div>
            <div class="col-lg-6 text-center">
                {{-- Eye chart + doctor figure --}}
                <svg class="pts-info-illu" viewBox="0 0 360 260" aria-hidden="true">
                    {{-- Eye chart frame --}}
                    <rect x="30" y="30" width="200" height="200" fill="#fff" stroke="#1f2937" stroke-width="3" rx="6"/>
                    {{-- Eye chart letters/Cs --}}
                    <text x="130" y="65" font-size="34" font-weight="900" fill="#1f2937" text-anchor="middle" font-family="serif">O C</text>
                    <text x="130" y="100" font-size="26" font-weight="900" fill="#1f2937" text-anchor="middle" font-family="serif">N E L</text>
                    <text x="130" y="130" font-size="20" font-weight="900" fill="#1f2937" text-anchor="middle" font-family="serif">F P T O Z</text>
                    <text x="130" y="155" font-size="14" font-weight="900" fill="#1f2937" text-anchor="middle" font-family="serif">L P E D C R</text>
                    <text x="130" y="175" font-size="11" font-weight="900" fill="#1f2937" text-anchor="middle" font-family="serif">F E L O P Z D</text>
                    <text x="130" y="192" font-size="9"  font-weight="900" fill="#1f2937" text-anchor="middle" font-family="serif">D E F P O T E C</text>
                    {{-- Stand --}}
                    <rect x="125" y="230" width="10" height="20" fill="#1f2937"/>
                    <polygon points="100,250 160,250 130,260" fill="#1f2937"/>
                    {{-- Doctor figure (right) --}}
                    <g transform="translate(245, 65)">
                        <circle cx="40" cy="20" r="20" fill="#fde68a"/>
                        <rect x="22" y="40" width="36" height="60" fill="#fff" stroke="#1f2937" stroke-width="2"/>
                        <rect x="22" y="40" width="18" height="60" fill="#fff" stroke="#1f2937" stroke-width="2"/>
                        <rect x="34" y="50" width="12" height="40" fill="#3b82f6"/>
                        <polygon points="38,52 42,68 38,68 36,52" fill="#1f2937"/>
                        <line x1="22" y1="55" x2="0" y2="40" stroke="#1f2937" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="0" cy="40" r="5" fill="#fbbf24"/>
                    </g>
                </svg>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── HANDBOOK ─────────── --}}
<section class="pts-info-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 text-center order-lg-1">
                {{-- Open handbook illustration --}}
                <svg class="pts-info-illu" viewBox="0 0 360 260" aria-hidden="true">
                    {{-- Book spine shadow --}}
                    <ellipse cx="180" cy="240" rx="160" ry="10" fill="#000" opacity="0.08"/>
                    {{-- Open book --}}
                    <path d="M 30 80 L 170 60 L 170 220 L 30 200 Z" fill="#fde68a" stroke="#1f2937" stroke-width="2"/>
                    <path d="M 330 80 L 190 60 L 190 220 L 330 200 Z" fill="#fcd34d" stroke="#1f2937" stroke-width="2"/>
                    {{-- Centre crease --}}
                    <line x1="180" y1="60" x2="180" y2="220" stroke="#1f2937" stroke-width="2"/>
                    {{-- Lines on left page --}}
                    <g stroke="#1f2937" stroke-width="1.5" opacity="0.5">
                        <line x1="45"  y1="95"  x2="160" y2="80"/>
                        <line x1="45"  y1="110" x2="160" y2="95"/>
                        <line x1="45"  y1="125" x2="140" y2="112"/>
                        <line x1="45"  y1="140" x2="160" y2="125"/>
                        <line x1="45"  y1="155" x2="160" y2="140"/>
                        <line x1="45"  y1="170" x2="140" y2="158"/>
                    </g>
                    {{-- Lines on right page --}}
                    <g stroke="#1f2937" stroke-width="1.5" opacity="0.5">
                        <line x1="200" y1="80"  x2="315" y2="95"/>
                        <line x1="200" y1="95"  x2="315" y2="110"/>
                        <line x1="200" y1="112" x2="295" y2="125"/>
                        <line x1="200" y1="125" x2="315" y2="140"/>
                        <line x1="200" y1="140" x2="315" y2="155"/>
                        <line x1="200" y1="158" x2="295" y2="170"/>
                    </g>
                    {{-- Title bar --}}
                    <rect x="55" y="100" width="80" height="10" fill="#1f2937"/>
                    <rect x="210" y="100" width="80" height="10" fill="#1f2937"/>
                    {{-- Bookmark --}}
                    <polygon points="240,60 252,60 252,90 246,82 240,90" fill="#ef4444"/>
                    {{-- Decorative pen --}}
                    <g transform="translate(260, 30) rotate(20)">
                        <rect x="0" y="0" width="60" height="6" rx="2" fill="#3b82f6"/>
                        <polygon points="60,0 70,3 60,6" fill="#1f2937"/>
                    </g>
                </svg>
            </div>
            <div class="col-lg-6 order-lg-2">
                <h3 class="pts-info-heading">{{ $handbookName }} Handbook</h3>
                <p class="pts-info-text">{{ $handbookText }}</p>
                <p class="pts-info-text mb-2"><strong>Download the official handbook from {{ $authority }}:</strong></p>
                <a href="{{ $handbookUrl }}" target="_blank" rel="noopener" class="pts-handbook-link">
                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ $handbookName }} — {{ $authority }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────── INLINE QUIZ (appears after form submit) ─────────── --}}
<section class="pts-quiz-section" id="pts-quiz">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="pts-quiz-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="pts-quiz-title mb-0">{{ $stateName }} — Practice Questions</h2>
                        <span class="badge bg-warning text-dark" id="quiz-progress">Question 1 of {{ $questionCount }}</span>
                    </div>

                    <div id="quiz-container">
                        <div class="py-4 text-center" id="quiz-start">
                            <i class="bi bi-play-circle display-3 text-warning mb-3 d-block"></i>
                            <h3 class="fw-bold pts-quiz-h">Ready to practice?</h3>
                            <p class="text-muted">{{ count($questions) }} sample questions based on {{ $stateName }} road rules. You need {{ $passScore }}% to pass.</p>
                            <button class="btn btn-warning btn-lg fw-bold px-5" id="quiz-start-btn">Start Practice Test</button>
                        </div>
                        <div id="quiz-question" style="display:none;">
                            <p class="fw-semibold mb-3" id="quiz-question-text"></p>
                            <div id="quiz-options" class="d-grid gap-2 mb-3"></div>
                            <div id="quiz-feedback" class="alert small" style="display:none;"></div>
                            <button class="btn btn-warning fw-bold" id="quiz-next-btn" style="display:none;">Next Question</button>
                        </div>
                        <div id="quiz-results" style="display:none;">
                            <div class="text-center py-3">
                                <div id="quiz-score-icon" class="display-3 mb-3"></div>
                                <h3 class="fw-bold pts-quiz-h" id="quiz-score-text"></h3>
                                <p class="text-muted" id="quiz-score-detail"></p>
                                <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                                    <button class="btn btn-warning fw-bold" id="quiz-retry-btn">Try Again</button>
                                    <a href="{{ route('find-instructor') }}?q={{ urlencode($stateCode) }}" class="btn btn-outline-dark">Book a {{ $stateCode }} Lesson</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="pts-about-block">
                        <h4 class="fw-bold mb-2">About the {{ $stateName }} {{ $testName }}</h4>
                        <p class="text-muted small mb-2">{{ $aboutText }}</p>
                        <h6 class="fw-bold mt-3">What to bring on test day</h6>
                        <ul class="text-muted small mb-0">
                            <li>Proof of identity (100 points of ID)</li>
                            <li>Completed application form</li>
                            <li>Payment for the licence fee</li>
                            <li>Parental consent (if you're under 18)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function() {
    // Smooth-scroll to quiz when the hero form is submitted, and auto-start
    var startForm = document.getElementById('pts-start-form');
    var startBtn  = document.getElementById('quiz-start-btn');
    if (startForm) {
        startForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var target = document.getElementById('pts-quiz');
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setTimeout(function() { if (startBtn) startBtn.click(); }, 600);
        });
    }

    var questions = @json($questions);
    var currentQ = 0, score = 0;
    var total = questions.length;
    var passPercent = {{ $passScore }};

    var startEl    = document.getElementById('quiz-start');
    var questionEl = document.getElementById('quiz-question');
    var resultsEl  = document.getElementById('quiz-results');
    var progressEl = document.getElementById('quiz-progress');
    var qText      = document.getElementById('quiz-question-text');
    var qOptions   = document.getElementById('quiz-options');
    var qFeedback  = document.getElementById('quiz-feedback');
    var nextBtn    = document.getElementById('quiz-next-btn');

    if (startBtn) startBtn.addEventListener('click', function() {
        currentQ = 0; score = 0;
        startEl.style.display = 'none';
        resultsEl.style.display = 'none';
        questionEl.style.display = 'block';
        showQuestion();
    });

    document.getElementById('quiz-retry-btn').addEventListener('click', function() {
        currentQ = 0; score = 0;
        resultsEl.style.display = 'none';
        questionEl.style.display = 'block';
        showQuestion();
    });

    nextBtn.addEventListener('click', function() {
        currentQ++;
        if (currentQ >= total) { showResults(); return; }
        showQuestion();
    });

    function showQuestion() {
        var q = questions[currentQ];
        progressEl.textContent = 'Question ' + (currentQ + 1) + ' of ' + total;
        qText.textContent = q.question;
        qFeedback.style.display = 'none';
        nextBtn.style.display = 'none';
        qOptions.innerHTML = q.options.map(function(opt, i) {
            return '<button class="btn btn-outline-secondary text-start quiz-option" data-index="' + i + '">' + opt + '</button>';
        }).join('');
        qOptions.querySelectorAll('.quiz-option').forEach(function(btn) {
            btn.addEventListener('click', function() { checkAnswer(parseInt(btn.dataset.index), q.correct); });
        });
    }

    function checkAnswer(selected, correct) {
        qOptions.querySelectorAll('.quiz-option').forEach(function(btn, i) {
            btn.disabled = true;
            if (i === correct) btn.classList.replace('btn-outline-secondary', 'btn-success');
            else if (i === selected && selected !== correct) btn.classList.replace('btn-outline-secondary', 'btn-danger');
        });
        if (selected === correct) {
            score++;
            qFeedback.className = 'alert alert-success small';
            qFeedback.textContent = 'Correct!';
        } else {
            qFeedback.className = 'alert alert-danger small';
            qFeedback.textContent = 'Incorrect. The correct answer is: ' + questions[currentQ].options[correct];
        }
        qFeedback.style.display = 'block';
        nextBtn.style.display = 'inline-block';
    }

    function showResults() {
        questionEl.style.display = 'none';
        resultsEl.style.display = 'block';
        var pct = Math.round((score / total) * 100);
        var passed = pct >= passPercent;
        document.getElementById('quiz-score-icon').innerHTML = passed
            ? '<i class="bi bi-check-circle-fill text-success"></i>'
            : '<i class="bi bi-x-circle-fill text-danger"></i>';
        document.getElementById('quiz-score-text').textContent = passed ? 'You passed!' : 'Not quite there yet';
        document.getElementById('quiz-score-detail').textContent = 'You scored ' + score + '/' + total + ' (' + pct + '%). ' + (passed ? 'Great work!' : 'You need ' + passPercent + '% to pass. Keep practising!');
        progressEl.textContent = 'Complete';
    }
})();
</script>
@endpush
