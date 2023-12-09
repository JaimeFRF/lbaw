<h3>Hi {{ $mailData['username'] }},</h3>
<p>We hope this message finds you well. We wanted to inform you that a request has been initiated to 
reset the password for your Antiquus account associated with the email address: {{ $mailData['email']}}.</p>
<p>To complete the password reset process, please follow the link provided below:</p>
<a href="{{ route('reset_password', ['token' => $mailData['token']]) }}">Reset Password</a>
<h5>-------</h5>
<h5>Antiquus Staff</h5>