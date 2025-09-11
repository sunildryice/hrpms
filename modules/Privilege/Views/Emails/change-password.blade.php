<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{!! env('APP_NAME') !!}</title>
</head>
<body>
<div style="font-size:12px; font-family:Arial, Helvetica, sans-serif; width:800px;">
    Dear {!! $user->full_name !!},
    <br/><br/>
    Your password has been changed by {!! $authUser->full_name !!}.
    <br/><br/>
    Your new password is {!! $password !!}.
    <br/><br/>
    You can change your password after logged in.
    <br/><br/>
    Kind Regards, <br/>
    {!! env('MAIL_FROM_NAME') !!}
</div>
</body>
</html>