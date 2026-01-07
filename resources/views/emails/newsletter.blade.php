<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 16px;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background: #ffffff; padding: 24px; border-radius: 8px;">
                <tr>
                    <td>
                        {!! $html !!}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
