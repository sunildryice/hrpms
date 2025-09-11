<?php

namespace App\Http\Controllers;

use App\Mail\TestEmail;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{

    /**
     * Display a listing of the log.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mail = Mail::to('adhikarysunil.1@gmail.com')
            ->send(new TestEmail());

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 1;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = config('mail.host');  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = config('mail.username');                  // SMTP username
            $mail->Password = config('mail.password');                          // SMTP password
            $mail->SMTPSecure = config('mail.encryption');                          // SMTP password
            $mail->Port = config('mail.port');                                    // TCP port to connect to

            //Recipients
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress('adhikarysunil.1@gmail.com', 'Sunil Adhikari');     // Add a recipient
            $mail->addReplyTo(config('mail.from.address'), config('mail.from.name'));

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body = 'This is the HTML message body <b>in bold!</b>';
//            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ';
            dump($mail->ErrorInfo);
        }
    }

    public function info()
    {
        phpinfo();
    }
}
