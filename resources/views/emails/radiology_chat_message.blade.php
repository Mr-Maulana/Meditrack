<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pesan Baru dari Rumkit TK III IM Lhokseumawe</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f6f9; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e1e8ed; }
        .header { background: #0f172a; color: #fff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #fff; }
        .header p { margin: 5px 0 0; font-size: 11px; color: #fbbf24; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        .content { padding: 30px; }
        .patient-salutation { font-size: 15px; font-weight: bold; color: #0f172a; margin-bottom: 15px; }
        .message-body { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px; color: #334155; line-height: 1.6; margin-bottom: 25px; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #0f172a; color: #white !important; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; text-align: center; }
        .footer { background: #f8fafc; text-align: center; padding: 20px; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .sender-info { font-size: 12px; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rumkit TK III IM Lhokseumawe</h1>
            <p>Pesan Layanan Medis</p>
        </div>
        <div class="content">
            <div class="patient-salutation">
                Halo {{ $result->patient->name }},
            </div>
            
            <p>Anda menerima pesan baru terkait dengan laporan hasil pemeriksaan radiologi Anda:</p>
            
            <div class="message-body">{{ $messageText }}</div>
            
            <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
                <a href="{{ route('radiology.public-report', $result->share_token) }}" style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">
                    Buka Portal Hasil Radiologi Anda
                </a>
            </div>

            <div class="sender-info">
                <p>Dikirim oleh: <strong>{{ $senderName }}</strong> (Staff Radiologi)</p>
            </div>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Meditrack Rumkit TK III IM 07.01 Lhokseumawe.<br>Jangan membalas langsung ke alamat email ini.</p>
        </div>
    </div>
</body>
</html>
