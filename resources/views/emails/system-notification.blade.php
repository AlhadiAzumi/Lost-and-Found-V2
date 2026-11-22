<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notificationTitle }} - Lost &amp; Found</title>
</head>
<body style="margin: 0; padding: 0; background-color: #E3ECE3; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #141923;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #E3ECE3; padding: 36px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 560px; background-color: #ffffff; border-radius: 24px; overflow: hidden; border: 1px solid #D2DFD3; box-shadow: 0 12px 32px rgba(20, 25, 35, 0.06);">
                    
                    <!-- Header Emerald -->
                    <tr>
                        <td style="background-color: #1B4332; padding: 26px 32px;">
                            <span style="display: inline-block; background-color: rgba(255, 255, 255, 0.14); color: #A7F3D0; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 4px 12px; border-radius: 999px; margin-bottom: 10px;">
                                Notifikasi Sistem
                            </span>
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: -0.02em;">
                                {{ $notificationTitle }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 32px;">
                            <p style="margin: 0 0 14px; font-size: 15px; line-height: 1.6; color: #1E293B;">
                                Halo <strong style="color: #141923;">{{ $recipientName }}</strong>,
                            </p>

                            <div style="background-color: #F4F7F4; border-left: 4px solid #1B4332; border-radius: 12px; padding: 16px 18px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 14px; line-height: 1.65; color: #1E293B;">
                                    {{ $notificationMessage }}
                                </p>
                            </div>

                            @if ($actionUrl)
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td align="center" style="border-radius: 999px; background-color: #141923;">
                                            <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; padding: 13px 26px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 999px;">
                                                Buka di Portal Kampus &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 18px 32px; background-color: #F8FAF8; border-top: 1px solid #E8EFE8; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #64748B;">
                                Email otomatis dari <strong style="color: #141923;">Lost &amp; Found Kampus</strong>. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>