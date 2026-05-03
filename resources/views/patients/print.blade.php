<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Pasien - {{ $patient->name }}</title>
    <style>
        @page {
            size: 80mm auto; /* Thermal width, auto height */
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Monospace is often clearer on thermal */
            margin: 0;
            padding: 5mm;
            background: #fff;
            color: #000;
        }
        .thermal-label {
            width: 70mm;
            border: 2px solid #000;
            padding: 2mm;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: black;
        }
        .header p {
            font-size: 8pt;
            margin: 0;
            font-weight: bold;
        }
        .content {
            margin-bottom: 2mm;
        }
        .field {
            margin-bottom: 1.5mm;
        }
        .label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .value {
            font-size: 11pt;
            font-weight: black;
            display: block;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 1mm;
            font-size: 7pt;
            font-weight: bold;
        }
        .barcode-placeholder {
            text-align: center;
            font-size: 10pt;
            margin: 2mm 0;
            font-weight: bold;
            border: 1px solid #000;
            padding: 1mm;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #eee; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer;">CETAK THERMAL</button>
    </div>

    <div class="thermal-label">
        <div class="header">
            <h1>Rumkit TK III IM 07.01 Lhokseumawe</h1>
            <p>MEDITRACK SYSTEM</p>
        </div>

        <div class="content">
            <div class="field">
                <span class="label">NAMA PASIEN:</span>
                <span class="value">{{ strtoupper($patient->name) }}</span>
            </div>
            <div class="field">
                <span class="label">NOMOR RM:</span>
                <span class="value">{{ $patient->patient_code ?? 'MT-'.str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="field">
                <span class="label">TGL LAHIR:</span>
                <span class="value">{{ $patient->date_of_birth ? $patient->date_of_birth->format('d/m/Y') : '-' }}</span>
            </div>
            <div class="field">
                <span class="label">JENIS KELAMIN:</span>
                <span class="value">{{ $patient->gender == 'male' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</span>
            </div>
        </div>

        <div class="barcode-placeholder">
            * {{ $patient->patient_code ?? $patient->id }} *
        </div>

        <div class="footer">
            VERIFIKASI SISTEM TERENKRIPSI<br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
