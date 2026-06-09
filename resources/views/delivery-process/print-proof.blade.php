<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengantaran #MT-{{ str_pad($delivery->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { size: A4; margin: 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 16px;
            color: #111;
            background: #fff;
            font-size: 11pt;
            line-height: 1.5;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 8px;
        }
        .no-print button, .no-print a {
            display: inline-block;
            margin: 0 6px;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-print { background: #1e3a5f; color: #fff; }
        .btn-back { background: #6b7280; color: #fff; }
        .header {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px double #111;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header img { width: 72px; height: 72px; object-fit: contain; }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p { margin: 4px 0 0; font-size: 9pt; color: #555; }
        .doc-title {
            text-align: center;
            margin: 18px 0 22px;
        }
        .doc-title h2 {
            display: inline-block;
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            border-bottom: 2px solid #111;
            padding-bottom: 4px;
        }
        .doc-title p { margin: 8px 0 0; font-size: 10pt; color: #444; }
        .meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            font-size: 10pt;
        }
        .meta-box {
            border: 1px solid #ccc;
            padding: 10px 12px;
            border-radius: 6px;
            background: #fafafa;
        }
        .section { margin-bottom: 18px; }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
        }
        .field label {
            display: block;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 2px;
        }
        .field p { margin: 0; font-weight: 600; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            background: #dcfce7;
            color: #166534;
        }
        .evidence {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .evidence img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .signature-box {
            background: #fafafa;
            border: 1px solid #ccc;
            border-radius: 6px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
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
            margin-top: 36px;
        }
        .sign-box { text-align: center; font-size: 10pt; }
        .sign-line {
            border-bottom: 1px solid #111;
            width: 200px;
            margin: 60px auto 6px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" class="btn-print" onclick="window.print()">
            Cetak Bukti Pengantaran
        </button>
        <a href="{{ route('my-deliveries.detail', $delivery) }}" class="btn-back">Kembali ke Detail</a>
    </div>

    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="MediTrack Logo">
        <div>
            <h1>Sistem MediTrack Rumah Sakit TK III IM 07.01 Lhokseumawe</h1>
            <p>Jl. Sultan Iskandar Muda No. 1, Kec. Banda Sakti, Kota Lhokseumawe, Aceh 24311</p>
        </div>
    </div>

    <div class="doc-title">
        <h2>Bukti Pengantaran Obat</h2>
        <p>Nomor: MT-DLV-{{ str_pad($delivery->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="meta">
        <div class="meta-box">
            <strong>Status:</strong>
            <span class="status-badge">{{ $delivery->status === 'delivered' ? 'Terkirim' : ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
        </div>
        <div class="meta-box">
            <strong>Tanggal Tugas:</strong> {{ $delivery->delivery_date?->format('d F Y') ?? '-' }}
        </div>
        <div class="meta-box">
            <strong>Waktu Selesai:</strong> {{ $delivery->delivered_at?->format('d F Y, H:i') ?? '-' }} WIB
        </div>
        <div class="meta-box">
            <strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i') }} WIB
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
                <label>Prioritas</label>
                <p>{{ $delivery->priority === 'urgent' ? 'Urgent' : 'Normal' }}</p>
            </div>
            <div class="field" style="grid-column: 1 / -1;">
                <label>Alamat Pengantaran</label>
                <p>{{ $delivery->delivery_address }}</p>
            </div>
        </div>
    </div>

    @if($delivery->prescription)
    <div class="section">
        <div class="section-title">Daftar Obat Yang Diantar</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
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
                    <td><strong>{{ $med['name'] ?? '-' }}</strong></td>
                    <td>{{ $med['dosage'] ?? '-' }}</td>
                    <td>{{ $med['frequency'] ?? '-' }}</td>
                    <td>{{ $med['duration'] ?? '-' }}</td>
                    <td>{{ $med['instructions'] ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Data obat tidak tersedia</td>
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
                <label>Kurir Petugas</label>
                <p>{{ $delivery->courier->name ?? '-' }}</p>
            </div>
            <div class="field">
                <label>Nama Penerima</label>
                <p>{{ $delivery->recipient_name ?? $delivery->receiver_name ?? ($delivery->assessment ? $delivery->patient->name : '-') }}</p>
            </div>
            <div class="field">
                <label>Hubungan Penerima</label>
                <p>{{ $delivery->recipient_relation ?? '-' }}</p>
            </div>
            <div class="field">
                <label>Telepon Penerima</label>
                <p>{{ $delivery->recipient_phone ?? $delivery->receiver_phone ?? '-' }}</p>
            </div>
            @if($delivery->assessment?->start_time)
            <div class="field">
                <label>Waktu Berangkat</label>
                <p>{{ $delivery->assessment->start_time->format('d/m/Y H:i') }}</p>
            </div>
            @endif
            @if($delivery->assessment?->handover_time)
            <div class="field">
                <label>Waktu Serah Terima</label>
                <p>{{ $delivery->assessment->handover_time->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
        @if($delivery->notes || $delivery->assessment?->special_notes)
        <div class="field" style="margin-top: 12px;">
            <label>Catatan</label>
            <p>{{ $delivery->notes ?? $delivery->assessment?->special_notes }}</p>
        </div>
        @endif
    </div>

    @if($delivery->status === 'delivered' && ($delivery->proof_image || $delivery->assessment?->signature_image))
    <div class="section">
        <div class="section-title">Bukti Penyerahan</div>
        <div class="evidence">
            @if($delivery->proof_image)
            <div>
                <label style="font-size: 9pt; font-weight: bold; display: block; margin-bottom: 6px;">Foto Bukti Pengantaran</label>
                <img src="{{ Storage::url($delivery->proof_image) }}" alt="Foto Bukti">
            </div>
            @endif
            @if($delivery->assessment?->signature_image)
            <div>
                <label style="font-size: 9pt; font-weight: bold; display: block; margin-bottom: 6px;">Tanda Tangan Penerima</label>
                <div class="signature-box">
                    <img src="{{ Storage::url($delivery->assessment->signature_image) }}" alt="Tanda Tangan">
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="signatures">
        <div class="sign-box">
            <p>Petugas Kurir,</p>
            <div class="sign-line"></div>
            <p><strong>{{ $delivery->courier->name ?? '-' }}</strong></p>
        </div>
        <div class="sign-box">
            <p>Penerima Obat,</p>
            <div class="sign-line"></div>
            <p><strong>{{ $delivery->recipient_name ?? $delivery->receiver_name ?? '________________' }}</strong></p>
        </div>
    </div>
</body>
</html>
