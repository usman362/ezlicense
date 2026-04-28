# Email Automation Module — Setup Guide

This guide covers the complete email system for SecureLicences. All emails use a consistent branded template (yellow `L` logo, brand colors) and are automatically logged for admin visibility.

---

## 🎨 Brand Theme

All emails use the **SecureLicences** custom theme located at:
```
resources/views/vendor/mail/html/themes/securelicences.css
```

**Brand colors used in emails:**
- Logo accent: `#ffd500` (yellow)
- Primary CTA buttons: `#ff8400` (orange)
- Success messages: `#10b981` (green)
- Error/danger: `#ef4444` (red)
- Body text: `#4b4a47`
- Headings: `#121110`

**Logo treatment** (in every email header & footer):
```
Secure[L]icences   ← "L" rendered as yellow rounded badge
```

---

## 📬 All Notifications (18 total)

### 🟢 Booking Lifecycle (8)
| Notification | Trigger | Recipients | Channels |
|--------------|---------|------------|----------|
| `BookingConfirmed` | New booking created | Learner | Email + SMS + DB |
| `InstructorNewBooking` | New booking created | Instructor | Email + SMS + DB |
| `BookingProposed` | Reschedule proposal | Learner | Email + SMS + DB |
| `BookingCancelled` | Cancellation by either party | Other party | Email + SMS + DB |
| `InstructorArrived` | Instructor marks arrival | Learner | Email + SMS + DB |
| `LessonConfirmationRequest` | Lesson marked complete (anti-chargeback) | Learner | Email + SMS + DB |
| `LessonReminder24h` ✨NEW | 24 hours before lesson | Both | Email + SMS + DB |
| `ReviewRequested` | After lesson completion | Learner | Email + DB |

### 💰 Financial (3)
| Notification | Trigger | Recipients | Channels |
|--------------|---------|------------|----------|
| `PaymentReceipt` ✨NEW | After successful payment | Learner | Email + DB |
| `WalletCredited` | Wallet top-up successful | Learner | Email + DB |
| `PayoutProcessed` | Weekly payout sent | Instructor | Email + DB |

### 👤 Account & Onboarding (5)
| Notification | Trigger | Recipients | Channels |
|--------------|---------|------------|----------|
| `WelcomeNotification` | User registration | New user | Email + DB |
| `PasswordResetNotification` ✨BRANDED | Password reset request | User | Email |
| `InstructorVerificationUpdated` | Admin approves/rejects instructor | Instructor | Email + DB |
| `InstructorSignupAdminAlert` ✨NEW | New instructor signs up | All admins | Email + DB |
| `DocumentReviewed` ✨NEW | Document approved/rejected | Instructor | Email + DB |

### 🛡️ Admin & Reviews (2)
| Notification | Trigger | Recipients | Channels |
|--------------|---------|------------|----------|
| `AdminBookingAlert` | Booking events (new/cancelled/completed) | All admins | Email + DB |
| `ReviewApproved` | Review published on profile | Instructor | Email + DB |

---

## ⏰ Scheduled Email Commands

Three commands run automatically via Laravel scheduler:

```bash
# Mondays at 2:00 AM AEST — generates instructor payouts
php artisan payouts:generate

# Every 4 hours — sends confirmation reminders (anti-chargeback)
php artisan confirmations:remind --hours=4 --max-reminders=3

# Hourly — sends 24-hour lesson reminders to both parties
php artisan lessons:remind-24h
```

**Logs** are written to:
- `storage/logs/payouts.log`
- `storage/logs/confirmations.log`
- `storage/logs/lesson-reminders.log`

**To enable on the live server**, add this cron entry:
```cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## ⚙️ Production SMTP Setup

### 1. Edit `.env` on the live server

Change from `MAIL_MAILER=log` (debug) to a real provider:

```env
# ──────────────────────────────────────────────────────────
# Recommended: SMTP (works with most providers)
# ──────────────────────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@securelicences.com.au"
MAIL_FROM_NAME="Secure Licences"
```

### 2. Common providers

#### **Gmail (testing only — low daily limit)**
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-16-char-app-password   # Generate at https://myaccount.google.com/apppasswords
MAIL_ENCRYPTION=tls
```

#### **SendGrid (recommended for production)**
```env
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your-api-key-here
MAIL_ENCRYPTION=tls
```

#### **Mailgun**
```env
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@your-domain.mailgun.org
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
```

#### **Amazon SES** (best deliverability)
```env
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-ses-smtp-username
MAIL_PASSWORD=your-ses-smtp-password
MAIL_ENCRYPTION=tls
```

### 3. After updating .env
```bash
php artisan config:clear
php artisan config:cache
```

### 4. Test
Send a test email from the admin tinker:
```bash
php artisan tinker
> Mail::raw('Test from SecureLicences', fn($m) => $m->to('your-email@example.com')->subject('Test'));
```

---

## 📞 SMS Setup (Vonage)

For SMS notifications (booking confirmations, reminders, instructor arrival):

### 1. Add to `.env`
```env
VONAGE_KEY=your-vonage-api-key
VONAGE_SECRET=your-vonage-secret
VONAGE_SMS_FROM="SecureLic"   # 11 chars max (alphanumeric sender ID)
```

### 2. Configure SMS opt-in per user

The site has a `notifications_sms_opt_in` boolean on users. Users can toggle SMS in their profile settings. If they opt out, only email + in-app notifications fire.

---

## 🔍 Email Logs (Admin)

Every outbound email is automatically captured to the `email_logs` table.

**Admin dashboard** → **Email Logs** (sidebar)

Shows:
- KPI cards (Total / Today / Last 7 days / Failed)
- Searchable + filterable list (by recipient, subject, notification type, status, date range)
- Status badge per row (Sent / Failed)

**File path:** `app/Models/EmailLog.php` + `app/Listeners/LogSentEmail.php`

---

## 🚀 Queueing (Performance)

All notifications use `use Queueable;` trait but currently send **synchronously** in the request lifecycle (no `ShouldQueue` interface). This is fine for low-traffic deployments.

To enable async/background sending (recommended for high-traffic production):

1. Add `implements ShouldQueue` to each notification class
2. Configure queue driver in `.env`:
   ```env
   QUEUE_CONNECTION=database
   ```
3. Run a worker:
   ```bash
   php artisan queue:work --tries=3 --timeout=60
   ```
4. Use Supervisor or systemd to keep the worker running 24/7 in production.

---

## 🧪 Testing Notifications Locally

```bash
# Switch back to log driver locally so emails go to storage/logs/laravel.log
# (no real send during dev)
MAIL_MAILER=log

# View the log
tail -f storage/logs/laravel.log
```

To preview HTML in the browser, use Laravel's `Notification::fake()` in tests, or:
```bash
php artisan tinker
> $user = App\Models\User::first();
> $user->notify(new App\Notifications\WelcomeNotification($user));
> // Then open storage/logs/laravel.log to see the rendered HTML
```

---

## 📋 Notification Trigger Map

| User Action | Notifications Fired |
|------------|---------------------|
| New user registers (learner) | `WelcomeNotification` |
| New user registers (instructor) | `WelcomeNotification` + `InstructorSignupAdminAlert` (to all admins) |
| Learner books lesson | `BookingConfirmed` (learner), `InstructorNewBooking` (instructor), `AdminBookingAlert` (admins), `PaymentReceipt` (learner) |
| Lesson approaching (T-24h) | `LessonReminder24h` (learner + instructor) |
| Instructor arrives | `InstructorArrived` (learner) |
| Lesson marked complete | `LessonConfirmationRequest` (learner), `ReviewRequested` (learner) |
| Learner confirms lesson | (DB only — no email needed) |
| Lesson confirmation reminder | `LessonConfirmationRequest` (with `isReminder=true`) |
| Booking cancelled | `BookingCancelled` (other party), `AdminBookingAlert` (admins) |
| Booking rescheduled | `BookingProposed` (learner) + `BookingCancelled` (learner, for original) |
| Wallet top-up | `WalletCredited` (learner) |
| Weekly payout | `PayoutProcessed` (instructor) |
| Admin verifies instructor | `InstructorVerificationUpdated` (instructor) |
| Admin reviews document | `DocumentReviewed` (instructor) |
| Review approved | `ReviewApproved` (instructor) |
| Password reset request | `PasswordResetNotification` (user) |

---

## 🎨 Customizing Email Templates

To change the look of all notification emails:
1. Edit CSS at `resources/views/vendor/mail/html/themes/securelicences.css`
2. Edit header at `resources/views/vendor/mail/html/header.blade.php`
3. Edit footer at `resources/views/vendor/mail/html/footer.blade.php`
4. Run `php artisan view:clear` if changes don't appear immediately

To change copy/text of a specific notification:
1. Edit the `toMail()` method in the relevant `app/Notifications/{Name}.php` file
2. Use `MailMessage` fluent API: `->greeting()`, `->line()`, `->action()`, `->salutation()`

---

## ✅ Pre-Launch Checklist

- [ ] Set `MAIL_MAILER=smtp` in `.env`
- [ ] Configure SMTP credentials (host, port, username, password)
- [ ] Verify `MAIL_FROM_ADDRESS` is a real domain (not `hello@example.com`)
- [ ] Set up SPF + DKIM + DMARC DNS records for your sending domain (avoids spam filtering)
- [ ] Run `php artisan config:clear` after .env changes
- [ ] Set up cron job to run `schedule:run` every minute
- [ ] (Optional) Set up Vonage for SMS — add `VONAGE_KEY`, `VONAGE_SECRET`, `VONAGE_SMS_FROM`
- [ ] Test send: `Mail::raw('test', fn($m) => $m->to('your@email.com')->subject('Test'));`
- [ ] Verify email arrives in inbox (not spam)
- [ ] Check **Admin → Email Logs** to confirm logging works
- [ ] Run `php artisan lessons:remind-24h --dry-run` to preview reminder behavior
- [ ] (Optional) Switch notifications to `ShouldQueue` + run `queue:work` for async sending

---

## 🐛 Troubleshooting

### Emails not arriving
1. Check `storage/logs/laravel.log` for SMTP errors
2. Check **Admin → Email Logs** — is the email recorded as "sent" or "failed"?
3. Verify `MAIL_MAILER` is not `log` (which only writes to file)
4. Verify SMTP credentials with: `php artisan tinker > Mail::raw('test', fn($m)=>$m->to('your@email.com')->subject('Test'));`

### Emails going to spam
- Set up **SPF**, **DKIM**, and **DMARC** DNS records
- Use a verified sender domain (not Gmail/Yahoo for production)
- Avoid spam trigger words in subject lines

### Reminders not sending
- Verify cron is running: check `storage/logs/lesson-reminders.log`
- Run manually to test: `php artisan lessons:remind-24h`

### Logo / styling broken
- Run `php artisan view:clear` and `php artisan config:clear`
- Verify `config/mail.php` has `'theme' => 'securelicences'`

---

**Last updated:** April 2026
