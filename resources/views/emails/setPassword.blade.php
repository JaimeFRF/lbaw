<h3>Hi {{ $mailData['username'] }},</h3>
<p>We hope this message finds you well.
We wanted to inform you that an account has been created for you associated with the email address: {{ $mailData['email']}}.</p>
<p>To access your account, please follow the link to set your password:</p>
<a href="{{ route('reset_password', ['token' => $mailData['token']]) }}">Reset Password</a>
<h5>-------</h5>
<h5>Antiquus Staff</h5>