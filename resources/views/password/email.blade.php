<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Send Password Reset Link</button>
</form>