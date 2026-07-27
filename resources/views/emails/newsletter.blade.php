<!DOCTYPE html>
<html lang="nl" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $newsletter->subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#F7F8FA;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#F7F8FA;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;">

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#03182B;border-radius:12px 12px 0 0;padding:28px 36px 24px;">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td>
                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#D9A42E;">Kachels &amp; Vloeistoffen</p>
                                    <p style="margin:0;font-size:20px;font-weight:700;color:#ffffff;letter-spacing:-0.02em;">Oliehandel van Deutekom</p>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <div style="width:36px;height:36px;background-color:#D9A42E;border-radius:50%;display:inline-block;text-align:center;line-height:36px;font-size:18px;color:#03182B;font-weight:700;">🔥</div>
                                </td>
                            </tr>
                        </table>
                        <div style="margin-top:18px;height:2px;background:linear-gradient(to right,#D9A42E,#F0BA3C,transparent);border-radius:2px;"></div>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="background-color:#ffffff;padding:36px 36px 28px;font-size:15px;color:#10263D;line-height:1.7;">
                        {!! $html !!}
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#03182B;border-radius:0 0 12px 12px;padding:24px 36px;">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td>
                                    <p style="margin:0 0 6px 0;font-size:13px;font-weight:700;color:#D9A42E;">Oliehandel van Deutekom</p>
                                    <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.6);line-height:1.7;">
                                        Specialist in kachels en kachelvloeistof<br>
                                        <a href="mailto:info@oliehandelvandeutekom.nl" style="color:#D9A42E;text-decoration:none;">info@oliehandelvandeutekom.nl</a>
                                    </p>
                                    @if(isset($unsubscribeUrl))
                                    <p style="margin:10px 0 0 0;font-size:11px;color:rgba(255,255,255,0.35);">
                                        <a href="{{ $unsubscribeUrl }}" style="color:rgba(255,255,255,0.35);text-decoration:underline;">Uitschrijven van nieuwsbrief</a>
                                    </p>
                                    @endif
                                </td>
                                <td align="right" style="vertical-align:top;">
                                    <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.35);text-align:right;">
                                        © {{ date('Y') }} Oliehandel van Deutekom<br>
                                        Alle rechten voorbehouden.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>
</body>
</html>
