<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Radiologi - {{ $result->patient->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.5;
            color: #1e293b;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .kop-logo {
            width: 15%;
            text-align: left;
            vertical-align: middle;
        }
        .kop-logo-circle {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            border: 2px solid #1e3a8a;
            display: inline-block;
            text-align: center;
            line-height: 65px;
            background-color: #f1f5f9;
        }
        .kop-logo-text {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .kop-text {
            width: 85%;
            text-align: center;
            vertical-align: middle;
            padding-right: 10%;
        }
        .kop-instansi {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.5px;
        }
        .kop-nama-rs {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 1px 0;
        }
        .kop-kota {
            font-size: 15px;
            font-weight: 850;
            text-transform: uppercase;
            color: #16a34a;
        }
        .kop-detail {
            font-size: 8px;
            color: #4b5563;
            margin-top: 3px;
            font-style: italic;
        }
        .line-double {
            border-top: 2px solid #1e3a8a;
            border-bottom: 1px solid #16a34a;
            height: 2px;
            margin-bottom: 20px;
        }
        .title-doc {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            padding-bottom: 15px;
            color: #1e3a8a;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 18%;
            color: #475569;
        }
        .info-colon {
            width: 2%;
            color: #475569;
        }
        .info-val {
            width: 30%;
            color: #0f172a;
        }
        .section-title {
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1.5px solid #1e3a8a;
            padding-bottom: 3px;
            margin-bottom: 6px;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .content-box {
            background: #f8fafc;
            padding: 10px 12px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            color: #1e293b;
            margin-bottom: 15px;
            min-height: 40px;
            white-space: pre-wrap;
        }
        .content-expertise {
            min-height: 120px;
        }
        .sig-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .sig-col-left {
            width: 60%;
        }
        .sig-col-right {
            width: 40%;
            text-align: center;
            font-size: 11px;
            color: #1e293b;
        }
        .footer-notice {
            margin-top: 25px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
            font-size: 8px;
            color: #64748b;
            text-align: center;
            line-height: 1.3;
        }
        a {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Kop Surat (Letterhead) -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <div class="kop-logo-circle">
                    <span class="kop-logo-text">TNI AD</span>
                </div>
            </td>
            <td class="kop-text">
                <div class="kop-instansi">Detasemen Kesehatan Wilayah IM 04.01</div>
                <div class="kop-nama-rs">Rumah Sakit Tentara Tk. III Iskandar Muda 07.01</div>
                <div class="kop-kota">Lhokseumawe</div>
                <div class="kop-detail">
                    Jl. KKA No. 1, Lancang Garam, Kec. Banda Sakti, Kota Lhokseumawe, Aceh<br>
                    Telp: (0645) 42023 | Email: rst.lhokseumawe@kemhan.go.id
                </div>
            </td>
        </tr>
    </table>
    <!-- Double divider line -->
    <div class="line-double"></div>

    <!-- Title -->
    <div class="title-doc">LAPORAN HASIL PEMERIKSAAN RADIOLOGI</div>

    <!-- Patient & Examination Info Grid -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Pasien</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->patient->name }}</td>
            <td class="info-label">No. Rontgen / ID</td>
            <td class="info-colon">:</td>
            <td class="info-val">RAD-{{ str_pad($result->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="info-label">No. Rekam Medis</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->patient->patient_code }}</td>
            <td class="info-label">Tanggal Periksa</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->created_at->format('d-m-Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="info-label">Jenis Kelamin</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td class="info-label">Dokter Pemeriksa</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->doctor ? $result->doctor->name : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Lahir</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->patient->date_of_birth->format('d-m-Y') }}</td>
            <td class="info-label">Operator Scan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $result->operator ? $result->operator->name : '-' }}</td>
        </tr>
    </table>

    <!-- Diagnosis & Expertise Section -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; margin-top: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                <div class="section-title">Klinis / Diagnosis Awal</div>
                <div class="content-box" style="margin-bottom: 0; min-height: 35px;">{{ $result->diagnosis }}</div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 10px; border-left: 1px dashed #cbd5e1;">
                <div class="section-title">Hasil Ekspertise Sp.Rad</div>
                <div class="content-box content-expertise" style="margin-bottom: 0; min-height: 120px;">{{ $result->reading_result }}</div>
            </td>
        </tr>
    </table>

    <!-- Scan Images Section (each image in its own block for proper page breaking) -->
    <div class="section-title">Hasil Pemindaian Scan / Rontgen ({{ count($result->image_paths) }} Gambar)</div>
    @foreach($result->image_paths as $index => $imgPath)
        @php
            $imagePath = storage_path('app/public/' . $imgPath);
            $base64 = '';
            if (file_exists($imagePath)) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        @endphp
        @if($base64)
            <div style="page-break-inside: avoid; margin-bottom: 12px;">
                <div style="font-size: 9px; color: #64748b; margin-bottom: 4px; font-weight: bold;">Gambar {{ $index + 1 }} dari {{ count($result->image_paths) }}</div>
                <div style="text-align: center;">
                    <img src="{{ $base64 }}" style="width: 100%; height: auto; border-radius: 4px; border: 1px solid #cbd5e1; display: inline-block;" />
                </div>
            </div>
        @endif
    @endforeach

    <!-- Signature -->
    <table class="sig-table">
        <tr>
            <td class="sig-col-left"></td>
            <td class="sig-col-right">
                Lhokseumawe, {{ $result->updated_at->format('d-m-Y') }}<br>
                Dokter Pemeriksa / Spesialis Radiologi,<br><br><br><br><br>
                <strong><u>{{ $result->doctor ? $result->doctor->name : 'Pemeriksa Radiologi' }}</u></strong><br>
                NIP/NRP. {{ $result->doctor ? $result->doctor->id . '070100' : '-------------------' }}
            </td>
        </tr>
    </table>

    <!-- Digital authenticity note -->
    <div class="footer-notice">
        Dokumen ini sah dikeluarkan secara digital oleh Sistem Meditrack Rumkit TK III IM 07.01 Lhokseumawe.<br>
        Untuk verifikasi keaslian dan melihat berkas gambar pemindaian asli secara online, silakan kunjungi:<br>
        <a href="{{ route('radiology.public-report', $result->share_token) }}">{{ route('radiology.public-report', $result->share_token) }}</a>
    </div>
</div>
</body>
</html>
