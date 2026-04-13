<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lesson Confirmation – Secure Licences</title>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #faf9f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .confirmation-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }
        .confirmation-header {
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }
        .confirmation-header .icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .icon-pending { background: #fff7ed; color: #ff8400; }
        .icon-success { background: #ecfdf5; color: #10b981; }
        .icon-error { background: #fef2f2; color: #ef4444; }
        .icon-info { background: #fffbeb; color: #f59e0b; }

        .confirmation-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #121110;
            margin-bottom: 0.5rem;
        }
        .confirmation-header p {
            color: #6b6a68;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .booking-details {
            margin: 0 2rem;
            padding: 1.25rem;
            background: #faf9f7;
            border-radius: 12px;
            border: 1px solid #e8e7e5;
        }
        .booking-details .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }
        .booking-details .detail-row + .detail-row {
            border-top: 1px solid #e8e7e5;
        }
        .detail-label {
            font-size: 0.85rem;
            color: #6b6a68;
            font-weight: 500;
        }
        .detail-value {
            font-size: 0.9rem;
            color: #121110;
            font-weight: 600;
            text-align: right;
        }
        .confirmation-actions {
            padding: 1.5rem 2rem 2rem;
            text-align: center;
        }
        .btn-confirm {
            display: inline-block;
            background: linear-gradient(135deg, #ff8400, #c2650a);
            color: #fff;
            border: none;
            padding: 0.875rem 2.5rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(255,132,0,0.3);
        }
        .btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255,132,0,0.4);
        }
        .btn-confirm:active { transform: translateY(0); }

        .footer-note {
            padding: 0 2rem 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #9a9997;
            line-height: 1.5;
        }
        .footer-note a { color: #ff8400; text-decoration: none; }

        .confirmed-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #ecfdf5;
            color: #059669;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .timestamp {
            margin-top: 0.75rem;
            font-size: 0.8rem;
            color: #6b6a68;
        }
        .logo {
            font-weight: 800;
            font-size: 1.1rem;
            color: #121110;
            margin-bottom: 1.5rem;
        }
        .logo .l-badge {
            display: inline-flex;
            width: 24px;
            height: 24px;
            background: #ff8400;
            color: #fff;
            font-weight: 800;
            font-size: 0.8rem;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            margin: 0 1px;
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="confirmation-header">
            <div class="logo">Secure<span class="l-badge">L</span>icences</div>

            @if($status === 'invalid')
                <div class="icon icon-error"><i class="bi bi-exclamation-triangle"></i></div>
                <h1>Invalid Link</h1>
                <p>This confirmation link is not valid or has expired. If you believe this is an error, please contact our support team.</p>

            @elseif($status === 'already_confirmed')
                <div class="icon icon-info"><i class="bi bi-check-circle"></i></div>
                <h1>Already Confirmed</h1>
                <p>You have already confirmed this lesson. Thank you!</p>

            @elseif($status === 'confirmed')
                <div class="icon icon-success"><i class="bi bi-check-lg"></i></div>
                <h1>Lesson Confirmed!</h1>
                <p>Thank you for confirming your lesson was completed. This has been recorded successfully.</p>

            @elseif($status === 'pending')
                <div class="icon icon-pending"><i class="bi bi-clipboard-check"></i></div>
                <h1>Confirm Your Lesson</h1>
                <p>Your instructor has marked this lesson as completed. Please confirm that you received this lesson.</p>
            @endif
        </div>

        @if($booking && in_array($status, ['pending', 'confirmed', 'already_confirmed']))
            <div class="booking-details">
                <div class="detail-row">
                    <span class="detail-label">Booking</span>
                    <span class="detail-value">#{{ $booking->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Instructor</span>
                    <span class="detail-value">{{ $booking->instructor->name ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">{{ $booking->scheduled_at ? $booking->scheduled_at->format('D, d M Y') : '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time</span>
                    <span class="detail-value">{{ $booking->scheduled_at ? $booking->scheduled_at->format('g:i A') : '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">{{ $booking->duration_minutes }} minutes</span>
                </div>
                @if($booking->suburb)
                    <div class="detail-row">
                        <span class="detail-label">Location</span>
                        <span class="detail-value">{{ $booking->suburb->name }} {{ $booking->suburb->postcode }}</span>
                    </div>
                @endif
            </div>
        @endif

        <div class="confirmation-actions">
            @if($status === 'pending')
                <form method="POST" action="{{ url('/lesson-confirmation/' . $token) }}">
                    @csrf
                    <button type="submit" class="btn-confirm">
                        <i class="bi bi-check2-circle"></i> Yes, I Completed This Lesson
                    </button>
                </form>

            @elseif($status === 'confirmed')
                <div class="confirmed-badge">
                    <i class="bi bi-shield-check"></i> Confirmed Successfully
                </div>
                <div class="timestamp">
                    Confirmed on {{ $booking->learner_confirmed_at->format('d M Y \a\t g:i A') }}
                </div>

            @elseif($status === 'already_confirmed')
                <div class="confirmed-badge">
                    <i class="bi bi-shield-check"></i> Previously Confirmed
                </div>
                <div class="timestamp">
                    Originally confirmed on {{ $booking->learner_confirmed_at->format('d M Y \a\t g:i A') }}
                </div>
            @endif
        </div>

        <div class="footer-note">
            @if($status === 'pending')
                Having an issue? <a href="mailto:support@securelicences.com.au">Contact Support</a> instead.
                <br>Your confirmation is recorded as proof that this service was delivered.
            @elseif($status === 'invalid')
                <a href="mailto:support@securelicences.com.au">Contact Support</a>
            @else
                <a href="{{ url('/') }}">Return to Secure Licences</a>
            @endif
        </div>
    </div>
</body>
</html>
