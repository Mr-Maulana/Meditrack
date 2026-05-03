<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Pengantaran #{{ $delivery->id }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 3mm;
            background: #fff;
            color: #000;
            line-height: 1.2;
        }
        .receipt {
            width: 74mm;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
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
        .section {
            margin-bottom: 4mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
        }
        .section-title {
            font-size: 8pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 1mm;
        }
        .recipient-name {
            font-size: 12pt;
            font-weight: black;
            display: block;
        }
        .address {
            font-size: 10pt;
            font-weight: bold;
            display: block;
        }
        .phone {
            font-size: 11pt;
            font-weight: black;
        }
        .med-list {
            font-size: 9pt;
            font-weight: bold;
        }
        .med-item {
            margin-bottom: 1mm;
            display: flex;
            justify-content: flex-start;
        }
        .priority-box {
            border: 3px solid #000;
            text-align: center;
            padding: 2mm;
            font-size: 14pt;
            font-weight: 900;
            margin-bottom: 4mm;
        }
        .urgent {
            background: #000;
            color: #fff;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 8pt;
            font-weight: bold;
        }
        .barcode {
            font-size: 12pt;
            font-weight: black;
            border: 1px solid #000;
            padding: 2mm;
            margin: 3mm 0;
            display: inline-block;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #eee; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer;">CETAK RESI</button>
    </div>

    <div class="receipt">
        <div class="header">
            <h1>Rumkit TK III IM 07.01 Lhokseumawe</h1>
            <p>RESI PENGANTARAN OBAT</p>
            <p>ID: #{{ str_pad($delivery->id, 8, '0', STR_PAD_LEFT) }}</p>
        </div>

        @if($delivery->priority === 'urgent')
        <div class="priority-box urgent">*** URGENT ***</div>
        @else
        <div class="priority-box">REGULER</div>
        @endif

        <div class="section">
            <div class="section-title">PENERIMA:</div>
            <span class="recipient-name">{{ strtoupper($delivery->patient->name) }}</span>
            <span class="phone">TELP: {{ $delivery->patient->phone }}</span>
            <span class="address">{{ strtoupper($delivery->delivery_address) }}</span>
        </div>

        <div class="section">
            <div class="section-title">DAFTAR OBAT:</div>
            <div class="med-list">
                @php
                    $meds = $delivery->prescription->medications ?? [
                        ['name' => $delivery->prescription->medication_name, 'dosage' => $delivery->prescription->dosage]
                    ];
                @endphp
                @foreach($meds as $med)
                <div class="med-item">[ ] {{ strtoupper($med['name']) }} ({{ $med['dosage'] }})</div>
                @endforeach
            </div>
        </div>

        <div class="section">
            <div class="section-title">PETUGAS:</div>
            <span style="font-size: 10pt; font-weight: bold;">{{ strtoupper($delivery->courier->name ?? 'BELUM ADA') }}</span>
        </div>

        <div class="footer">
            <div class="barcode">*MT-{{ $delivery->id }}*</div>
            <p>DICETAK: {{ now()->format('d/m/Y H:i') }}</p>
            <p>MEDITRACK - Rumkit TK III IM 07.01 Lhokseumawe</p>
            <p>===============================</p>
        </div>
    </div>
</body>
</html>
