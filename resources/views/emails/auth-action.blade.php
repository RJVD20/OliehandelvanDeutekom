<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#eef2f5;color:#13283a;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2f5;padding:32px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;">
                <tr>
                    <td align="center" style="padding:0 0 22px;">
                        <a href="{{ url('/') }}" style="display:inline-block;text-decoration:none;">
                            <img src="{{ asset('images/logo-email.png') }}" width="290" height="113" alt="Kachels &amp; Vloeistoffen" style="display:block;width:290px;max-width:90%;height:auto;border:0;outline:none;text-decoration:none;">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="overflow:hidden;border-radius:18px;background:#ffffff;box-shadow:0 12px 35px rgba(3,24,43,.10);">
                        <div style="height:7px;background:#d9a42e;"></div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding:42px 42px 34px;">
                                    <p style="margin:0 0 10px;color:#b47f09;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">{{ $eyebrow }}</p>
                                    <h1 style="margin:0 0 22px;color:#03182b;font-size:28px;line-height:1.25;">{{ $title }}</h1>
                                    <p style="margin:0 0 16px;color:#526577;font-size:16px;line-height:1.7;">Hallo {{ $name }},</p>
                                    @foreach ($lines as $line)
                                        <p style="margin:0 0 16px;color:#526577;font-size:16px;line-height:1.7;">{{ $line }}</p>
                                    @endforeach
                                    <table role="presentation" cellspacing="0" cellpadding="0" style="margin:28px 0;">
                                        <tr>
                                            <td style="border-radius:10px;background:#03182b;">
                                                <a href="{{ $actionUrl }}" style="display:inline-block;padding:15px 24px;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;">{{ $actionText }} &nbsp;→</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:0 0 8px;color:#718191;font-size:13px;line-height:1.6;">Werkt de knop niet? Kopieer dan deze link naar je browser:</p>
                                    <p style="margin:0;word-break:break-all;font-size:12px;line-height:1.6;"><a href="{{ $actionUrl }}" style="color:#b47f09;">{{ $actionUrl }}</a></p>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-top:1px solid #e8edf1;background:#f8fafb;padding:22px 42px;color:#7b8996;font-size:13px;line-height:1.6;">
                                    {{ $footer }}<br>
                                    Met vriendelijke groet,<br><strong style="color:#33495c;">Kachels &amp; Vloeistoffen</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:20px;color:#8a98a5;font-size:12px;line-height:1.7;">Deze e-mail is automatisch verzonden.<br>KvK 77355431 · Btw-id NL003184350B48</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
