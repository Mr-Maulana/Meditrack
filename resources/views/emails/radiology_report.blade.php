<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Radiologi</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f6f9; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e1e8ed; }
        .header { background: #0f172a; color: #fff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; color: #fff; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #fbbf24; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        .content { padding: 30px; }
        .patient-info { background: #f8fafc; border-radius: 8px; padding: 15px; margin-bottom: 25px; border-left: 4px solid #0f172a; }
        .patient-info table { width: 100%; border-collapse: collapse; }
        .patient-info td { padding: 4px 0; font-size: 14px; }
        .patient-info td.label { font-weight: bold; color: #64748b; width: 120px; }
        .patient-info td.value { color: #0f172a; }
        .scan-container { text-align: center; margin: 25px 0; background: #f1f5f9; padding: 10px; border-radius: 8px; }
        .scan-image { max-width: 100%; height: auto; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-top: 25px; margin-bottom: 12px; color: #0f172a; }
        .result-box { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px; color: #334155; white-space: pre-line; }
        .footer { background: #f8fafc; text-align: center; padding: 20px; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #0f172a; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rumkit TK III IM 07.01 Lhokseumawe</h1>
            <p>Laporan Hasil Pemeriksaan Radiologi</p>
        </div>
        <div class="content">
            <div class="patient-info">
                <table>
                    <tr>
                        <td class="label">Nama Pasien</td>
                        <td class="value">: {{ $result->patient->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Rekam Medis</td>
                        <td class="value">: {{ $result->patient->patient_code }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Kelamin</td>
                        <td class="value">: {{ $result->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Lahir</td>
                        <td class="value">: {{ $result->patient->date_of_birth->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </div>

            @if($result->preview_image_path && file_exists(storage_path('app/public/' . $result->preview_image_path)))
            <div class="section-title">Hasil Pemindaian (Scan)</div>
            <div class="scan-container">
                <img src="{{ $message->embed(storage_path('app/public/' . $result->preview_image_path)) }}" alt="Scan Radiologi" class="scan-image" />
            </div>
            @endif

            <div class="section-title">Diagnosis</div>
            <div class="result-box">
                {{ $result->diagnosis }}
            </div>

            <div class="section-title">Hasil Baca Dokter (Ekspertise)</div>
            <div class="result-box">
                {{ $result->reading_result }}
            </div>

            <div style="text-align: center;">
                <a href="{{ route('radiology.public-report', $result->share_token) }}" class="btn">Buka Portal Hasil Pasien</a>
            </div>
        </div>
        <div class="footer">
            <p>Laporan ini dihasilkan secara otomatis oleh Sistem Meditrack Rumkit TK III IM 07.01 Lhokseumawe.<br>Jika ada pertanyaan lebih lanjut, silakan hubungi bagian Informasi Rumah Sakit.</p>
        </div>
    </div>
</body>
</html>
