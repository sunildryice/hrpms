<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<div>


    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="margin: 20px">
        <tr>
            <td align="left" valign="top" style="padding:10px;">

                <table cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td>
                            <p>Dear <strong>{!! $user->full_name !!},</strong> <br/>
                                <br/>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! env('APP_NAME') !!} recently received a request for a forgotten password.</td>
                    </tr>
                    <tr>
                        <td>To change your password, please enter the provided code in your mobile app</a>.</td>
                    </tr>
                    <tr>
                        <td>Code : {!! $user->reset_token !!}</td>
                    </tr>
                    <tr>
                        <td>If you did not request this change, you do not need to do anything.</td>
                    </tr>
                </table>

                <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">
                    <tr>
                        <td>
                            Kind Regards, <br/>
                            {!! env('APP_NAME') !!}
                        </td>
                </table>

            </td>
        </tr>
    </table>
</div>
</body>
</html>
