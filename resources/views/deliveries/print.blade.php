<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Antar #{{ $delivery->id }}</title>
    <style>
        @page { size: A4; margin: 16mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #111;
            background: #fff;
            line-height: 1.5;
            font-size: 11pt;
        }
        .page {
            padding: 20px 24px;
        }
        .no-print {
            text-align: center;
            padding: 14px 0;
            background: #f3f4f6;
            margin-bottom: 18px;
        }
        .no-print button {
            padding: 10px 20px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #111;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 900;
        }
        .header h2 {
            margin: 6px 0 0;
            font-size: 14pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 9pt;
            color: #111;
            line-height: 1.4;
        }
        .doc-title {
            text-align: center;
            margin: 16px 0 12px;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-block;
            border-bottom: 2px solid #111;
            padding-bottom: 4px;
        }
        .doc-title p {
            margin: 8px 0 0;
            font-size: 10pt;
            color: #444;
        }
        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }
        .meta-box {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fafafa;
            font-size: 10pt;
        }
        .meta-box strong {
            display: block;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 4px;
        }
        .section {
            margin-bottom: 18px;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #333;
            margin-bottom: 10px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .field {
            margin-bottom: 10px;
        }
        .field label {
            display: block;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
        }
        .field p {
            margin: 0;
            font-weight: 600;
            color: #111;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f9fafb;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            background: #d1fae5;
            color: #166534;
        }
        .evidence {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .evidence img {
            width: 100%;
            max-height: 240px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .signature-box {
            background: #fafafa;
            border: 1px solid #ccc;
            border-radius: 8px;
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }
        .signature-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        .sign-box { text-align: center; font-size: 10pt; }
        .sign-line {
            width: 220px;
            border-bottom: 1px solid #111;
            margin: 60px auto 8px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">Cetak Laporan Pengiriman</button>
    </div>

    <div class="page">
        <div class="header">
            <h1>MEDITRACK RUMAH SAKIT TK III IM 07.01</h1>
            <h2>LHOKSEUMAWE</h2>
            <p>Jl. Sultan Iskandar Muda No. 1, Kec. Banda Sakti, Kota Lhokseumawe, Aceh 24311</p>
        </div>

        <div class="doc-title">
            <h2>Laporan Hasil Pengiriman Obat</h2>
            <p>Nomor: MT-DLV-{{ str_pad($delivery->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="meta">
            <div class="meta-box">
                <strong>Status</strong>
                <p>{{ $delivery->status === 'delivered' ? 'Terkirim' : ucfirst(str_replace('_', ' ', $delivery->status)) }}</p>
            </div>
            <div class="meta-box">
                <strong>Tanggal Tugas</strong>
                <p>{{ $delivery->delivery_date?->format('d F Y') ?? '-' }}</p>
            </div>
            <div class="meta-box">
                <strong>Waktu Berangkat</strong>
                <p>{{ $delivery->assessment?->start_time?->format('d F Y, H:i') ?? ($delivery->departure_time?->format('d F Y, H:i') ?? '-') }} WIB</p>
            </div>
            <div class="meta-box">
                <strong>Waktu Serah Terima</strong>
                <p>{{ $delivery->assessment?->handover_time?->format('d F Y, H:i') ?? ($delivery->delivered_at?->format('d F Y, H:i') ?? '-') }} WIB</p>
            </div>
        </div>

        <div class="meta" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            <div class="meta-box">
                <strong>Lama Waktu Antar</strong>
                <p>
                    @if($delivery->assessment?->duration_minutes)
                        {{ $delivery->assessment->duration_minutes }} menit
                    @elseif($delivery->departure_time && $delivery->delivered_at)
                        {{ $delivery->departure_time->diffInMinutes($delivery->delivered_at) }} menit
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="meta-box">
                <strong>Dicetak</strong>
                <p>{{ now()->format('d F Y, H:i') }} WIB</p>
            </div>
            <div class="meta-box">
                <strong>Prioritas</strong>
                <p>{{ $delivery->priority === 'urgent' ? 'Urgent' : 'Normal' }}</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Informasi Pasien</div>
            <div class="grid-2">
                <div class="field">
                    <label>Nama Pasien</label>
                    <p>{{ $delivery->patient->name ?? '-' }}</p>
                </div>
                <div class="field">
                    <label>No. Rekam Medis</label>
                    <p>{{ $delivery->patient->patient_code ?? '-' }}</p>
                </div>
                <div class="field">
                    <label>Telepon</label>
                    <p>{{ $delivery->patient->phone ?? '-' }}</p>
                </div>
                <div class="field">
                    <label>Alamat</label>
                    <p>{{ $delivery->delivery_address ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if($delivery->prescription)
        <div class="section">
            <div class="section-title">Obat yang Diterima</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Dosis</th>
                        <th>Frekuensi</th>
                        <th>Durasi</th>
                        <th>Instruksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($delivery->prescription->getMedicationList() as $index => $med)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $med['name'] ?? '-' }}</td>
                        <td>{{ $med['dosage'] ?? '-' }}</td>
                        <td>{{ $med['frequency'] ?? '-' }}</td>
                        <td>{{ $med['duration'] ?? '-' }}</td>
                        <td>{{ $med['instructions'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:#666;">Data obat tidak tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        <div class="section">
            <div class="section-title">Informasi Pengantaran</div>
            <div class="grid-2">
                <div class="field">
                    <label>Kurir</label>
                    <p>{{ $delivery->courier->name ?? '-' }}</p>
                </div>
                <div class="field">
                    <label>Nama Penerima</label>
                    <p>{{ $delivery->recipient_name ?? $delivery->receiver_name ?? $delivery->patient->name ?? '-' }}</p>
                </div>
                <div class="field">
                    <label>Hubungan Penerima</label>
                    <p>{{ $delivery->recipient_relation ?? '-' }}</p>
                </div>
                <div class="field">
                    <label>Telepon Penerima</label>
                    <p>{{ $delivery->recipient_phone ?? $delivery->receiver_phone ?? '-' }}</p>
                </div>
                <div class="field" style="grid-column: 1 / -1;">
                    <label>Catatan Pengantaran</label>
                    <p>{{ $delivery->notes ?? $delivery->assessment?->special_notes ?? '-' }}</p>
                </div>
            </div>
        </div>

        @php
            $photoUrl = $delivery->assessment?->handover_photo ? Storage::url($delivery->assessment->handover_photo) : ($delivery->proof_image ? Storage::url($delivery->proof_image) : null);
            $signatureUrl = $delivery->assessment?->signature_image ? Storage::url($delivery->assessment->signature_image) : ($delivery->signature ? Storage::url($delivery->signature) : null);
        @endphp

        @if($photoUrl || $signatureUrl)
        <div class="section">
            <div class="section-title">Dokumentasi & Tanda Tangan</div>
            <div class="evidence">
                @if($photoUrl)
                <div>
                    <label style="font-size: 9pt; font-weight: bold; display: block; margin-bottom: 6px;">Foto Dokumentasi</label>
                    <img src="{{ $photoUrl }}" alt="Foto Dokumentasi">
                </div>
                @endif
                @if($signatureUrl)
                <div>
                    <label style="font-size: 9pt; font-weight: bold; display: block; margin-bottom: 6px;">Tanda Tangan Penerima</label>
                    <div class="signature-box">
                        <img src="{{ $signatureUrl }}" alt="Tanda Tangan Penerima">
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="signatures">
            <div class="sign-box">
                <p>Petugas Kurir</p>
                <div class="sign-line"></div>
                <p><strong>{{ $delivery->courier->name ?? '-' }}</strong></p>
            </div>
            <div class="sign-box">
                <p>Penerima Obat</p>
                <div class="sign-line"></div>
                <p><strong>{{ $delivery->recipient_name ?? $delivery->receiver_name ?? '________________' }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
