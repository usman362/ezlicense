<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="auth-input-wrap">
        <i class="bi bi-envelope"></i>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
        @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="auth-input-wrap">
        <i class="bi bi-lock"></i>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
        @error('password')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="form-check auth-remember mb-4">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">Remember me for 60 days</label>
    </div>

    <button type="submit" class="btn auth-btn-login mb-3">Login</button>

    @if (Route::has('password.request'))
        <div class="text-center">
            <a class="auth-forgot" href="{{ route('password.request') }}">Forgot password?</a>
        </div>
    @endif

    <div class="text-center mt-4 pt-3" style="border-top: 1px solid #eee;">
        <p class="text-muted mb-2">Don't have an account?</p>
        <a href="{{ route('register') }}" class="btn btn-outline-dark w-100">Create Account</a>
    </div>
</form>
