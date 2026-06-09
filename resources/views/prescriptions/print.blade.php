<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Etiket Obat - {{ $prescription->patient->name }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 2mm;
            background: #fff;
            color: #000;
        }
        .etiquette {
            width: 76mm;
            border: 2px solid #000;
            padding: 2mm;
            box-sizing: border-box;
            page-break-after: always;
            margin-bottom: 5mm;
        }
        .hospital-name {
            text-align: center;
            font-size: 10pt;
            font-weight: black;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
            margin-bottom: 2mm;
            text-transform: uppercase;
        }
        .patient-box {
            margin-bottom: 2mm;
        }
        .patient-name {
            font-size: 11pt;
            font-weight: bold;
            display: block;
        }
        .patient-rm {
            font-size: 8pt;
        }
        .med-box {
            text-align: center;
            border: 2px solid #000;
            padding: 3mm 1mm;
            margin: 2mm 0;
        }
        .med-name {
            font-size: 12pt;
            font-weight: bold;
            display: block;
            margin-bottom: 1mm;
        }
        .usage {
            font-size: 22pt;
            font-weight: 900;
            display: block;
            line-height: 1;
        }
        .instruction {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 1mm;
            display: block;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #eee; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer;">CETAK SEMUA ETIKET</button>
    </div>

    @php
        $meds = $prescription->medications ?? [
            [
                'name' => $prescription->medication_name,
                'dosage' => $prescription->dosage,
                'frequency' => $prescription->frequency,
                'instructions' => $prescription->instructions,
                'duration' => $prescription->duration
            ]
        ];
    @endphp

    @foreach($meds as $med)
    <div class="etiquette">
        <div class="hospital-name">Rumkit TK III IM 07.01 Lhokseumawe • FARMASI</div>
        
        <div class="patient-box">
            <span class="patient-name">{{ strtoupper($prescription->patient->name) }}</span>
            <span class="patient-rm">RM: {{ $prescription->patient->patient_code ?? $prescription->patient_id }} | TGL: {{ date('d/m/y') }}</span>
        </div>

        <div class="med-box">
            <span class="med-name">{{ strtoupper($med['name'] ?? '-') }}</span>
            <span class="usage">{{ $med['frequency'] ?? '-' }}</span>
            <span class="instruction">{{ strtoupper($med['instructions'] ?? 'SESUAI PETUNJUK') }}</span>
        </div>

        <div class="footer">
            <span>MASA PAKAI: {{ $med['duration'] ?? '-' }}</span>
            <span>SEMOGA CEPAT SEMBUH</span>
        </div>
    </div>
    @endforeach
</body>
</html>
