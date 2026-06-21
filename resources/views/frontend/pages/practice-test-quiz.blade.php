@extends('layouts.frontend')
@section('title', $stateName . ' ' . $testName . ' — Practice Test — Secure Licence')
@section('meta_description', 'Free ' . $stateName . ' ' . $testName . ' practice test. ' . $totalQuestions . ' questions across 2 sections with instant results and answer review.')

@push('styles')
<style>
    .pq-wrap{position:relative;background:#a9dcf0;min-height:60vh;padding:2.5rem 0 0;overflow:hidden;}
    .pq-wrap .pq-inner{position:relative;z-index:2;padding-bottom:240px;}
    .pq-scene{position:absolute;left:0;right:0;bottom:-1px;width:100%;height:auto;z-index:1;pointer-events:none;}
    .pq-card{background:#fff;border-radius:1rem;box-shadow:0 18px 50px rgba(20,23,28,.18);max-width:760px;margin:0 auto;padding:2rem 2.25rem;}
    .pq-title{text-align:center;color:#fff;font-weight:800;margin-bottom:1.5rem;text-shadow:0 1px 2px rgba(0,0,0,.1);}
    .pq-passrow{display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px dashed #eef0f2;}
    .pq-qnum{width:40px;height:40px;border-radius:50%;background:#ffd500;color:#1a1d21;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .pq-opt{display:flex;align-items:center;gap:.75rem;border:1px solid #e3e6ea;border-radius:.7rem;padding:.85rem 1rem;cursor:pointer;transition:all .12s ease;}
    .pq-opt:hover{border-color:#ffd500;background:#fffdf5;}
    .pq-opt.sel{border-color:#ffd500;background:#fff8e1;}
    .pq-opt input{accent-color:#caa300;}
    .pq-img{display:block;max-width:100%;max-height:300px;width:auto;height:auto;margin:1rem auto;border-radius:.6rem;object-fit:contain;background:#f8f9fb;}
    .pq-rev-item{border-bottom:1px solid #eef0f2;}
    .pq-rev-head{display:flex;align-items:center;gap:.6rem;padding:.85rem 0;cursor:pointer;}
    .pq-rev-head .num{font-weight:700;width:26px;flex-shrink:0;}
    .pq-rev-q{flex:1;}
    .pq-rev-item.wrong .pq-rev-q,.pq-rev-item.wrong .num{color:#b42318;}
    .pq-rev-body{display:none;padding:.5rem 0 1rem 2.2rem;}
    .pq-rev-body.open{display:block;}
    .pq-ans{display:flex;gap:.5rem;padding:.3rem 0;}
    .pq-ans.correct{color:#1a7f43;font-weight:600;}
    .pq-ans.your-wrong{color:#b42318;text-decoration:line-through;}
    .pq-ans.muted{color:#adb5bd;}
</style>
@endpush

@section('content')
<div class="pq-wrap">
    {{-- Illustrated road scene (city skyline, trees, traffic light, cones, car) --}}
    <svg class="pq-scene" viewBox="0 0 1440 340" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg">
        {{-- clouds --}}
        <g fill="#ffffff" opacity="0.95">
            <ellipse cx="170" cy="60" rx="46" ry="20"/><ellipse cx="210" cy="55" rx="34" ry="16"/>
            <ellipse cx="1180" cy="80" rx="50" ry="20"/><ellipse cx="1230" cy="74" rx="36" ry="16"/>
            <ellipse cx="650" cy="44" rx="40" ry="16"/>
        </g>
        {{-- city skyline (teal) --}}
        <g fill="#1f93b3">
            <rect x="120" y="150" width="80" height="150"/><rect x="210" y="120" width="60" height="180"/>
            <rect x="280" y="170" width="70" height="130"/><rect x="360" y="140" width="55" height="160"/>
            <rect x="430" y="180" width="80" height="120"/><rect x="540" y="130" width="60" height="170"/>
            <rect x="620" y="165" width="90" height="135"/><rect x="730" y="145" width="55" height="155"/>
            <rect x="800" y="185" width="75" height="115"/><rect x="900" y="125" width="65" height="175"/>
            <rect x="980" y="160" width="85" height="140"/><rect x="1085" y="140" width="58" height="160"/>
            <rect x="1160" y="175" width="80" height="125"/><rect x="1260" y="150" width="60" height="150"/>
        </g>
        <g fill="#177e9b" opacity="0.55">
            <rect x="226" y="138" width="44" height="22"/><rect x="556" y="148" width="44" height="20"/>
            <rect x="916" y="143" width="49" height="22"/><rect x="1099" y="158" width="44" height="20"/>
        </g>
        {{-- traffic light --}}
        <g>
            <rect x="250" y="200" width="8" height="95" fill="#33414d"/>
            <rect x="238" y="160" width="32" height="60" rx="6" fill="#33414d"/>
            <circle cx="254" cy="174" r="7" fill="#e74c3c"/><circle cx="254" cy="190" r="7" fill="#f1c40f"/><circle cx="254" cy="206" r="7" fill="#2ecc71"/>
        </g>
        {{-- trees --}}
        <g>
            <rect x="406" y="250" width="10" height="40" fill="#8a5a2b"/><circle cx="411" cy="240" r="34" fill="#5cae5c"/><circle cx="388" cy="252" r="24" fill="#4e9e4e"/><circle cx="434" cy="252" r="24" fill="#67b967"/>
            <rect x="1090" y="252" width="10" height="40" fill="#8a5a2b"/><circle cx="1095" cy="244" r="30" fill="#5cae5c"/><circle cx="1120" cy="256" r="22" fill="#4e9e4e"/>
            <rect x="1230" y="256" width="9" height="36" fill="#8a5a2b"/><circle cx="1234" cy="248" r="26" fill="#67b967"/>
        </g>
        {{-- grass --}}
        <rect x="0" y="288" width="1440" height="52" fill="#9ed36a"/>
        <rect x="0" y="284" width="1440" height="8" fill="#7cc242"/>
        {{-- road --}}
        <rect x="0" y="322" width="1440" height="18" fill="#dfe3e7"/>
        {{-- traffic cones --}}
        <g>
            @php $cones = [470,560,650,740,830,920,1010,1100,1190,1280]; @endphp
            @foreach($cones as $cx)
                <polygon points="{{ $cx }},300 {{ $cx-13 }},326 {{ $cx+13 }},326" fill="#f5821f"/>
                <rect x="{{ $cx-9 }}" y="312" width="18" height="5" fill="#fff"/>
                <rect x="{{ $cx-15 }}" y="324" width="30" height="5" rx="2" fill="#e06f10"/>
            @endforeach
        </g>
        {{-- car (yellow front + white body) --}}
        <g>
            <rect x="40" y="276" width="190" height="40" rx="14" fill="#ffffff"/>
            <path d="M40 300 Q40 276 64 276 L120 276 L120 316 L54 316 Q40 316 40 300 Z" fill="#ffd200"/>
            <rect x="120" y="284" width="95" height="20" rx="6" fill="#cfe7f2"/>
            <rect x="150" y="300" width="60" height="16" fill="#1a1d21" opacity="0.08"/>
            <text x="78" y="304" font-size="20" font-weight="800" fill="#1a1d21" font-family="Arial">L</text>
            <circle cx="80" cy="318" r="15" fill="#2b2f36"/><circle cx="80" cy="318" r="6" fill="#9aa1ab"/>
            <circle cx="185" cy="318" r="15" fill="#2b2f36"/><circle cx="185" cy="318" r="6" fill="#9aa1ab"/>
        </g>
    </svg>

    <div class="container pq-inner">
        <h1 class="pq-title h3">{{ $stateName }} {{ $testName }}</h1>

        @if(empty($sections))
            <div class="pq-card text-center">
                <p class="mb-0 text-muted">No practice questions are available yet. Please check back soon.</p>
            </div>
        @else
        {{-- ───── INTRO ───── --}}
        <div class="pq-card" id="pq-intro">
            <p class="mb-3">There are <strong>{{ $totalQuestions }} questions</strong> in the test divided into <strong>{{ count($sections) }} sections</strong>. You must pass each section to achieve an overall test pass.</p>
            <div class="mb-4">
                @foreach($sections as $s)
                    <div class="pq-passrow">
                        <span>{{ $s['label'] }}</span>
                        <span class="text-muted">{{ $s['passMark'] }}/{{ $s['count'] }} = Pass</span>
                    </div>
                @endforeach
            </div>
            <button class="btn btn-warning btn-lg w-100 fw-bold" id="pq-start">Start Test</button>
            <hr class="my-4">
            <p class="small text-muted mb-1">* At the completion of the test you will see your results &amp; you can review your answers.</p>
            <p class="small text-muted mb-0">* You can attempt the test as many times as you like.</p>
        </div>

        {{-- ───── QUESTION ───── --}}
        <div class="pq-card" id="pq-question" style="display:none;">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <span class="fw-semibold text-muted" id="pq-section-label"></span>
                <span class="text-muted" id="pq-total-label"></span>
            </div>
            <div class="d-flex gap-3 mb-3">
                <span class="pq-qnum" id="pq-qnum">1</span>
                <h4 class="fw-bold mb-0" id="pq-qtext"></h4>
            </div>
            <div id="pq-qimage"></div>
            <div class="d-grid gap-2 my-3" id="pq-options"></div>
            <div class="text-end">
                <button class="btn btn-secondary px-4" id="pq-next" disabled>Next</button>
            </div>
        </div>

        {{-- ───── RESULTS ───── --}}
        <div class="pq-card" id="pq-results" style="display:none;">
            <div class="text-center mb-4">
                <div id="pq-result-emoji" style="font-size:2.5rem;"></div>
                <h3 class="fw-bolder mb-1" id="pq-result-title"></h3>
                <p class="text-muted mb-0" id="pq-result-sub">Here are your results:</p>
            </div>
            <div id="pq-result-sections" class="mb-4"></div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-dark flex-fill fw-semibold" id="pq-review">Review Test</button>
                <button class="btn btn-warning flex-fill fw-semibold" id="pq-again">Start Again</button>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <h6 class="fw-bold mb-1"><i class="bi bi-geo-alt text-warning"></i> Find your driving instructor</h6>
                <p class="small text-muted mb-2">Ready to get behind the wheel? Compare verified instructors near you.</p>
                <form method="get" action="{{ route('find-instructor.results') }}" class="row g-2 justify-content-center">
                    <div class="col-sm-7"><input type="text" name="q" class="form-control" placeholder="Enter your suburb" required></div>
                    <div class="col-sm-3"><button class="btn btn-warning w-100 fw-semibold">Search</button></div>
                </form>
            </div>
        </div>

        {{-- ───── REVIEW ───── --}}
        <div class="pq-card" id="pq-reviewpanel" style="display:none;">
            <p class="text-muted">Click on a question to expand and see the answer:</p>
            <div id="pq-review-list"></div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-outline-dark flex-fill" id="pq-back">Back to results</button>
                <button class="btn btn-warning flex-fill fw-semibold" id="pq-again2">Start Again</button>
            </div>
        </div>
        @endif
    </div>
</div>

@if(!empty($sections))
<script>
(function () {
    var SECTIONS = @json($sections);

    // Flatten into a single ordered list, tagging each with its section.
    var flat = [];
    SECTIONS.forEach(function (sec, si) {
        sec.questions.forEach(function (q) {
            flat.push({ sec: si, q: q, answer: null });
        });
    });
    var total = flat.length;

    var el = function (id) { return document.getElementById(id); };
    var idx = 0;

    function show(panel) {
        ['pq-intro','pq-question','pq-results','pq-reviewpanel'].forEach(function (p) {
            var node = el(p); if (node) node.style.display = (p === panel) ? '' : 'none';
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function sectionProgress(si) {
        // how many questions of this section appear up to & including current
        var before = 0, total = SECTIONS[si].count;
        for (var i = 0; i <= idx; i++) { if (flat[i].sec === si) before++; }
        return before + ' / ' + total;
    }

    function renderQuestion() {
        var item = flat[idx];
        var sec = SECTIONS[item.sec];
        el('pq-section-label').textContent = sec.label + ' (' + sectionProgress(item.sec) + ')';
        el('pq-total-label').textContent = 'Completed Total ' + (idx + 1) + ' of ' + total;
        el('pq-qnum').textContent = (idx + 1);
        el('pq-qtext').textContent = item.q.question;
        el('pq-qimage').innerHTML = item.q.image
            ? '<img src="' + item.q.image + '" class="pq-img" alt="">' : '';

        var opts = el('pq-options');
        opts.innerHTML = '';
        item.q.options.forEach(function (opt, oi) {
            var label = document.createElement('label');
            label.className = 'pq-opt' + (item.answer === oi ? ' sel' : '');
            label.innerHTML = '<input type="radio" name="pq-opt" value="' + oi + '" ' +
                (item.answer === oi ? 'checked' : '') + '> <span>' + opt + '</span>';
            label.querySelector('input').addEventListener('change', function () {
                item.answer = oi;
                opts.querySelectorAll('.pq-opt').forEach(function (n) { n.classList.remove('sel'); });
                label.classList.add('sel');
                el('pq-next').removeAttribute('disabled');
                el('pq-next').classList.remove('btn-secondary');
                el('pq-next').classList.add('btn-warning');
            });
            opts.appendChild(label);
        });

        var next = el('pq-next');
        var isLast = (idx === total - 1);
        next.textContent = isLast ? 'Finish & see results' : 'Next';
        if (item.answer === null) {
            next.setAttribute('disabled', 'disabled');
            next.classList.add('btn-secondary'); next.classList.remove('btn-warning');
        } else {
            next.removeAttribute('disabled');
            next.classList.remove('btn-secondary'); next.classList.add('btn-warning');
        }
        show('pq-question');
    }

    function renderResults() {
        // tally per section
        var html = '', allPass = true;
        SECTIONS.forEach(function (sec, si) {
            var correct = 0, count = sec.count;
            flat.forEach(function (it) {
                if (it.sec === si && it.answer === it.q.correct) correct++;
            });
            var pass = correct >= sec.passMark;
            if (!pass) allPass = false;
            html += '<div class="pq-passrow"><span><strong>' + sec.label + '</strong></span>' +
                '<span><span class="text-success">&#10003; ' + correct + '</span> ' +
                '<span class="text-danger ms-2">&#10007; ' + (count - correct) + '</span>' +
                '<span class="ms-3 fw-bold ' + (pass ? 'text-success' : 'text-danger') + '">' +
                (pass ? 'PASS' : 'FAIL') + '</span></span></div>';
        });
        el('pq-result-sections').innerHTML = html;
        el('pq-result-emoji').textContent = allPass ? '🎉' : '🔁';
        el('pq-result-title').textContent = allPass
            ? 'Congratulations! You passed.'
            : 'Almost there! Review your answers and try again.';
        show('pq-results');
    }

    function renderReview() {
        var list = el('pq-review-list');
        list.innerHTML = '';
        flat.forEach(function (it, i) {
            var correctChoice = it.q.correct;
            var isWrong = it.answer !== correctChoice;
            var wrap = document.createElement('div');
            wrap.className = 'pq-rev-item' + (isWrong ? ' wrong' : '');

            var head = document.createElement('div');
            head.className = 'pq-rev-head';
            head.innerHTML = '<span class="num">' + (i + 1) + '</span>' +
                '<span class="pq-rev-q">' + it.q.question + '</span>' +
                '<span>' + (isWrong ? '<i class="bi bi-x-lg text-danger"></i>' : '<i class="bi bi-check-lg text-success"></i>') + '</span>' +
                '<i class="bi bi-chevron-down text-muted"></i>';

            var body = document.createElement('div');
            body.className = 'pq-rev-body';
            var inner = '';
            it.q.options.forEach(function (opt, oi) {
                var cls = 'muted';
                var icon = '';
                if (oi === correctChoice) { cls = 'correct'; icon = '<i class="bi bi-check-circle-fill"></i>'; }
                else if (oi === it.answer) { cls = 'your-wrong'; icon = '<i class="bi bi-x-circle-fill"></i>'; }
                inner += '<div class="pq-ans ' + cls + '">' + (icon || '<i class="bi bi-dash"></i>') + ' <span>' + opt + '</span></div>';
            });
            if (it.q.explanation) {
                inner += '<div class="small text-muted mt-2"><i class="bi bi-info-circle"></i> ' + it.q.explanation + '</div>';
            }
            body.innerHTML = inner;

            head.addEventListener('click', function () { body.classList.toggle('open'); });
            wrap.appendChild(head); wrap.appendChild(body);
            list.appendChild(wrap);
        });
        show('pq-reviewpanel');
    }

    function restart() {
        flat.forEach(function (it) { it.answer = null; });
        idx = 0;
        // re-shuffle order within sections for variety
        renderQuestion();
    }

    el('pq-start').addEventListener('click', function () { idx = 0; renderQuestion(); });
    el('pq-next').addEventListener('click', function () {
        if (flat[idx].answer === null) return;
        if (idx < total - 1) { idx++; renderQuestion(); }
        else { renderResults(); }
    });
    el('pq-review').addEventListener('click', renderReview);
    el('pq-back').addEventListener('click', function () { show('pq-results'); });
    el('pq-again').addEventListener('click', restart);
    el('pq-again2').addEventListener('click', restart);
})();
</script>
@endif
@endsection
