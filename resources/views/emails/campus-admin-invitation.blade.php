<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Portal Kampus - Lost &amp; Found</title>
</head>
<body style="margin: 0; padding: 0; background-color: #E3ECE3; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #141923; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #E3ECE3; padding: 36px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 560px; background-color: #ffffff; border-radius: 24px; overflow: hidden; border: 1px solid #D2DFD3; box-shadow: 0 12px 32px rgba(20, 25, 35, 0.06);">
                    
                    <!-- Header Emerald -->
                    <tr>
                        <td style="background-color: #1B4332; padding: 28px 32px; text-align: left;">
                            <span style="display: inline-block; background-color: rgba(255, 255, 255, 0.14); color: #A7F3D0; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px; margin-bottom: 12px;">
                                Portal Disetujui
                            </span>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.02em;">
                                Lost &amp; Found Kampus
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #1E293B;">
                                Halo,
                            </p>

                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #1E293B;">
                                Permohonan portal <strong style="color: #141923;">{{ $application->name }}</strong> telah disetujui.
                            </p>

                            <p style="margin: 0 0 26px; font-size: 14px; line-height: 1.65; color: #475569;">
                                Gunakan tautan berikut untuk membuat akun admin portal. Tautan berlaku selama <strong style="color: #141923;">7 hari</strong> dan hanya dapat digunakan <strong style="color: #141923;">sekali</strong>.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 26px;">
                                <tr>
                                    <td align="center" style="border-radius: 999px; background-color: #141923;">
                                        <a href="{{ $setupUrl }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 999px;">
                                            Buat akun admin kampus &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback Link Box -->
                            <div style="background-color: #F4F7F4; border: 1px solid #E2EAE2; border-radius: 16px; padding: 14px 16px;">
                                <p style="margin: 0 0 6px; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">
                                    Atau salin tautan ini ke browser:
                                </p>
                                <p style="margin: 0; font-size: 12px; line-height: 1.5; word-break: break-all;">
                                    <a href="{{ $setupUrl }}" style="color: #1B4332; text-decoration: underline;">{{ $setupUrl }}</a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; background-color: #F8FAF8; border-top: 1px solid #E8EFE8; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #64748B;">
                                Email ini dikirim otomatis oleh sistem <strong style="color: #141923;">Lost &amp; Found</strong>. Abaikan jika Anda tidak merasa mengajukan permohonan ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>