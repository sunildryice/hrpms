<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#8d8e90" style="margin: 20px">

    </table>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="margin: 20px">
        <tr>
            <td align="left" valign="top" style="padding:10px;">

                <table cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td>
                            <p>Dear <strong>{!! $user->getFullName() !!},</strong> <br/>
                                <br/>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>Did you forget your password ?</td>
                    </tr>
                    <tr>
                        <td>To reset your password, please click on this <a
                                href="{!! route('reset.password.create', $user->reset_token) !!}">link</a>.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            If you don't want to change your password or didn't request this, please ignore and delete this message.
                        </td>
                    </tr>

                </table>

                <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">
                    <tr>
                        <td>
                            Kind Regards, <br/>
                            {!! config('app.name') !!}
                        </td>
                </table>

            </td>
        </tr>
    </table>
</div>
</body>
</html>
