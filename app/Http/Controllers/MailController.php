<?php

namespace App\Http\Controllers;

use Mail;
use Illuminate\Http\Request;
use App\Mail\MailModel;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use TransportException;
use Exception;

class MailController extends Controller
{
    function send(Request $request) {
        Log::info($request);
        $missingVariables = [];
        $requiredEnvVariables = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
        ];
    
        foreach ($requiredEnvVariables as $envVar) {
            if (empty(env($envVar))) {
                $missingVariables[] = $envVar;
            }
        }
    
        if (empty($missingVariables)) {

            $token = encrypt($request->email);

            $mailData = [
                'username' => $request->username,
                'email' => $request->email,
                'token' => $token,
            ];
            Log::info($request->type);
            $templateName = $request->type == 1 ? 'setPassword' : 'example';
            try {
                Mail::to($request->email)->send(new MailModel($mailData, $templateName));                
                $status = 'Success!';
                $message = $request->name . ', an email has been sent to ' . $request->email;
            } catch (TransportException $e) {
                $status = 'Error!';
                $message = 'SMTP connection error occurred during the email sending process to ' . $request->email;
            } catch (Exception $e) {
                $status = 'Error!';
                $message = 'An unhandled exception occurred during the email sending process to ' . $request->email;
            }

        } else {
            $status = 'Error!';
            $message = 'The SMTP server cannot be reached due to missing environment variables:';
        }

        $request->session()->flash('status', $status);
        $request->session()->flash('message', $message);
        $request->session()->flash('details', $missingVariables);
        return redirect()->route('home');
    }

    public function sendEmailSetPassword(Request $request){
        Log::info($request);
        $missingVariables = [];
        $requiredEnvVariables = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
        ];

        foreach ($requiredEnvVariables as $envVar) {
            if (empty(env($envVar))) {
                $missingVariables[] = $envVar;
            }
        }

        if (empty($missingVariables)) {

            $token = encrypt($request->email);

            $mailData = [
                'username' => $request->username,
                'email' => $request->email,
                'token' => $token,
            ];
            $templateName = $request->type == 1 ? 'setPassword' : 'example';
            try {
                Mail::to($request->email)->send(new MailModel($mailData, $templateName));                
                $status = 'Success!';
                $message = $request->name . ', an email has been sent to ' . $request->email;
            } catch (TransportException $e) {
                $status = 'Error!';
                $message = 'SMTP connection error occurred during the email sending process to ' . $request->email;
            } catch (Exception $e) {
                $status = 'Error!';
                $message = 'An unhandled exception occurred during the email sending process to ' . $request->email;
            }
        }
        return response()->json(['message' => 'Email to set password sent for new user!', 'username' => $request->username], 200);
}

    public function showRecoverPasswordForm(){
        return view('auth.recoverPassword');
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        try {
            $email = decrypt($token);
        } catch (DecryptException $e) {
            return redirect()->route('recover_password')->withErrors(['token' => 'The token is invalid.']);
        }
    
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return redirect()->route('recover_password')->withErrors(['email' => 'No user found with this email address.']);
        }
    
        return view('auth.resetPassword')->with(
            ['email' => $email]
        );
    }
}
