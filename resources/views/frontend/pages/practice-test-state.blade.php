@extends('layouts.frontend')

@section('title', $stateName . ' ' . $testName . ' - Free Practice Test')

@section('content')
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('practice-test') }}">Learner Tests Online</a></li>
            <li class="breadcrumb-item active">{{ $stateCode }} {{ $testName }}</li>
        </ol></nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <h1 class="h2 fw-bold mb-3">{{ $stateName }} {{ $testName }}</h1>
                <p class="text-muted mb-4">{{ $description }}</p>

                {{-- Practice test quiz area --}}
                <div class="card border-0 shadow-sm mb-4" id="quiz-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Practice Questions</h5>
                            <span class="badge bg-warning text-dark" id="quiz-progress">Question 1 of {{ $questionCount }}</span>
                        </div>

                        <div id="quiz-container">
                            <div class="py-4 text-center" id="quiz-start">
                                <i class="bi bi-play-circle display-3 text-warning mb-3 d-block"></i>
                                <h5 class="fw-bold">Ready to practice?</h5>
                                <p class="text-muted">This practice test contains {{ $questionCount }} questions based on {{ $stateName }} road rules. You need {{ $passScore }}% to pass.</p>
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
                                    <h4 class="fw-bold" id="quiz-score-text"></h4>
                                    <p class="text-muted" id="quiz-score-detail"></p>
                                    <div class="d-flex gap-2 justify-content-center mt-3">
                                        <button class="btn btn-warning fw-bold" id="quiz-retry-btn">Try Again</button>
                                        <a href="{{ route('find-instructor') }}" class="btn btn-outline-secondary">Book a Lesson</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">About the {{ $stateName }} {{ $testName }}</h5>
                        <p class="text-muted small">{{ $aboutText }}</p>
                        <h6 class="fw-bold mt-3">What to bring to the test</h6>
                        <ul class="small text-muted">
                            <li>Proof of identity (100 points of ID)</li>
                            <li>Completed application form</li>
                            <li>Payment for the licence fee</li>
                            <li>Parental consent (if under 18)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3" style="position:sticky;top:100px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Ready for the real thing?</h6>
                        <p class="small text-muted">Once you've passed the knowledge test, book driving lessons with a verified instructor in {{ $stateName }}.</p>
                        <a href="{{ route('find-instructor') }}?q={{ urlencode($stateCode) }}" class="btn btn-warning fw-bold w-100 mb-2">Find {{ $stateCode }} Instructors</a>
                        <a href="{{ route('practice-test') }}" class="btn btn-outline-secondary btn-sm w-100">Other State Tests</a>
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
    // Sample practice questions (in production these would come from a database)
    var questions = @json($questions);
    var currentQ = 0;
    var score = 0;
    var total = questions.length;
    var passPercent = {{ $passScore }};

    var startEl = document.getElementById('quiz-start');
    var questionEl = document.getElementById('quiz-question');
    var resultsEl = document.getElementById('quiz-results');
    var progressEl = document.getElementById('quiz-progress');
    var qText = document.getElementById('quiz-question-text');
    var qOptions = document.getElementById('quiz-options');
    var qFeedback = document.getElementById('quiz-feedback');
    var nextBtn = document.getElementById('quiz-next-btn');

    document.getElementById('quiz-start-btn').addEventListener('click', function() {
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
