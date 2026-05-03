<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Obat - {{ $patient->patient_code }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Barcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    
    <!-- Print Styles -->
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                width: 80mm !important;
                max-width: 80mm !important;
                margin: 0 !important;
                padding: 2mm !important;
                font-family: 'Courier New', monospace !important;
                font-size: 9pt !important;
                line-height: 1.2 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .print-label {
                width: 80mm !important;
                max-width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .break-after {
                page-break-after: always;
            }
            .barcode-container {
                text-align: center;
                margin: 2mm 0;
            }
            .warning-box {
                border: 1px solid #000 !important;
                background-color: #fff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            line-height: 1.2;
        }
        
        .print-label {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
        }
        
        .border-dot {
            border-bottom: 1px dashed #000;
        }
        
        .warning-box {
            border: 1px solid #000;
            background-color: #ffebee;
            padding: 1mm;
            margin: 1mm 0;
            font-size: 8pt;
        }
        
        .barcode {
            height: 15mm !important;
        }
    </style>
</head>
<body class="bg-white">
    <!-- Print Controls -->
    <div class="no-print fixed top-0 left-0 right-0 bg-gray-800 text-white p-4 z-50">
        <div class="max-w-2xl mx-auto flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold">Preview Label Thermal</h2>
                <p class="text-sm text-gray-300">Ukuran: 80mm. Siap untuk dicetak ke printer thermal</p>
                <p class="text-sm text-gray-300">Pasien: {{ $patient->name }}</p>
            </div>
            <div class="space-x-2">
                <button onclick="printLabels()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-print mr-2"></i> Cetak Semua
                </button>
                <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-times mr-2"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content with padding for print controls -->
    <div class="pt-20 md:pt-4">
        @php
            $delivery = $patient->deliveries()->latest()->first();
            $prescriptions = $patient->prescriptions;
        @endphp
        
        <!-- Label 1: Main Label -->
        <div class="print-label p-2">
            <!-- Header with Logo -->
            <div class="text-center mb-2">
                <div style="font-size: 12pt; font-weight: bold;">APOTEK SEHAT</div>
                <div style="font-size: 7pt;">Jl. Kesehatan No. 123, Jakarta</div>
                <div style="font-size: 7pt;">Telp: (021) 123-4567 | WA: 0812-3456-7890</div>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Patient Info -->
            <div class="mb-2">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 25%; font-weight: bold;">Nama</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 70%;">{{ strtoupper($patient->name) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Alamat</td>
                        <td>:</td>
                        <td>{{ $patient->address }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Telp</td>
                        <td>:</td>
                        <td>{{ $patient->phone }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Kode</td>
                        <td>:</td>
                        <td>{{ $patient->patient_code }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Medical Info -->
            <div class="mb-2">
                <div style="font-weight: bold;">INFORMASI MEDIS:</div>
                <table style="width: 100%; font-size: 8pt;">
                    <tr>
                        <td style="width: 40%;">Diagnosis</td>
                        <td style="width: 60%;">: {{ $patient->diagnosis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alergi</td>
                        <td>: {{ $patient->allergies ?? 'Tidak ada' }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Prescriptions -->
            <div class="mb-2">
                <div style="font-weight: bold; margin-bottom: 1mm;">RESEP OBAT:</div>
                @foreach($prescriptions as $index => $prescription)
                <div style="margin-bottom: 2mm; font-size: 8pt;">
                    <div style="font-weight: bold;">{{ $index + 1 }}. {{ $prescription->medication_name }}</div>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 25%;">Dosis</td>
                            <td style="width: 75%;">: {{ $prescription->dosage }}</td>
                        </tr>
                        <tr>
                            <td>Frekuensi</td>
                            <td>: {{ $prescription->frequency }}</td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td>: {{ $prescription->duration }}</td>
                        </tr>
                        @if($prescription->instructions)
                        <tr>
                            <td>Instruksi</td>
                            <td>: {{ $prescription->instructions }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endforeach
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Delivery Info -->
            <div class="mb-2">
                <table style="width: 100%; font-size: 8pt;">
                    <tr>
                        <td style="width: 40%; font-weight: bold;">Tanggal Resep</td>
                        <td style="width: 60%;">: {{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Tanggal Antar</td>
                        <td>: {{ $delivery->delivery_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Prioritas</td>
                        <td>: {{ $delivery->priority === 'urgent' ? 'URGENT' : 'NORMAL' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Status</td>
                        <td>: {{ strtoupper($delivery->status) }}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Barcode -->
            <div class="barcode-container">
                <svg id="barcode-{{ $patient->patient_code }}"></svg>
            </div>
            
            <!-- Warning Box -->
            <div class="warning-box">
                <div style="text-align: center; font-weight: bold; font-size: 8pt;">PERINGATAN</div>
                <div style="font-size: 7pt;">
                    1. Simpan di tempat kering dan sejuk<br>
                    2. Jauhkan dari jangkauan anak-anak<br>
                    3. Baca aturan pakai sebelum digunakan<br>
                    4. Habiskan sesuai durasi yang ditentukan
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-2" style="font-size: 7pt;">
                <div class="border-dot mb-1"></div>
                <div>** Label ini ditempel pada kemasan obat **</div>
            </div>
        </div>
        
        <!-- Spacer for cutting -->
        <div class="h-8 no-print"></div>
        
        <!-- Label 2: Medicine Label -->
        <div class="print-label p-2">
            <!-- Simplified version for medicine bottle -->
            <div class="text-center mb-2">
                <div style="font-size: 10pt; font-weight: bold;">LABEL OBAT</div>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Patient Info -->
            <div class="mb-2">
                <div style="font-weight: bold;">UNTUK:</div>
                <div>{{ strtoupper($patient->name) }}</div>
                <div style="font-size: 8pt;">{{ $patient->patient_code }}</div>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Medicine List -->
            <div class="mb-2">
                <div style="font-weight: bold;">OBAT:</div>
                @foreach($prescriptions as $index => $prescription)
                <div style="font-size: 8pt; margin-bottom: 1mm;">
                    <div>{{ $prescription->medication_name }}</div>
                    <div>{{ $prescription->dosage }} | {{ $prescription->frequency }}</div>
                </div>
                @endforeach
            </div>
            
            <!-- Barcode Small -->
            <div class="barcode-container">
                <svg id="barcode-small-{{ $patient->patient_code }}" style="height: 10mm !important;"></svg>
            </div>
            
            <!-- Expiry Warning -->
            <div class="warning-box" style="font-size: 7pt;">
                <div style="text-align: center; font-weight: bold;">PERHATIAN</div>
                <div>Gunakan sebelum: {{ now()->addDays(30)->format('d/m/Y') }}</div>
            </div>
        </div>
        
        <!-- Spacer for cutting -->
        <div class="h-8 no-print"></div>
        
        <!-- Label 3: Delivery Label -->
        <div class="print-label p-2">
            <!-- Delivery Label -->
            <div class="text-center mb-2">
                <div style="font-size: 10pt; font-weight: bold;">LABEL PENGANTARAN</div>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Delivery Info -->
            <div class="mb-2">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%; font-weight: bold;">Kode</td>
                        <td style="width: 60%;">: {{ $patient->patient_code }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Nama</td>
                        <td>: {{ strtoupper($patient->name) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Alamat</td>
                        <td>: {{ $patient->address }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Telp</td>
                        <td>: {{ $patient->phone }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="border-dot mb-2"></div>
            
            <!-- Special Instructions -->
            <div class="mb-2">
                <div style="font-weight: bold;">INSTRUKSI KHUSUS:</div>
                <div style="font-size: 8pt;">
                    @if($delivery->notes)
                    {{ $delivery->notes }}
                    @else
                    - Tidak ada instruksi khusus -
                    @endif
                </div>
            </div>
            
            <!-- QR Code Placeholder -->
            <div class="text-center mt-4">
                <div style="border: 1px solid #000; padding: 1mm; display: inline-block;">
                    <div style="width: 25mm; height: 25mm; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                        <div style="text-align: center; font-size: 6pt;">
                            <div>QR CODE</div>
                            <div>{{ $patient->patient_code }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4" style="font-size: 7pt;">
                <div class="border-dot mb-1"></div>
                <div>** Tempel di paket pengantaran **</div>
            </div>
        </div>
    </div>

    <script>
        // Generate barcodes
        document.addEventListener('DOMContentLoaded', function() {
            // Main barcode
            JsBarcode(`#barcode-{{ $patient->patient_code }}`, '{{ $patient->patient_code }}', {
                format: "CODE128",
                width: 1,
                height: 30,
                displayValue: true,
                fontSize: 10,
                margin: 0
            });
            
            // Small barcode
            JsBarcode(`#barcode-small-{{ $patient->patient_code }}`, '{{ $patient->patient_code }}', {
                format: "CODE128",
                width: 0.8,
                height: 20,
                displayValue: false,
                margin: 0
            });
            
            // Auto print after 1 second
            setTimeout(function() {
                // Uncomment untuk auto print
                // printLabels();
            }, 1000);
        });
        
        function printLabels() {
            window.print();
        }
        
        // Close window after print (optional)
        window.onafterprint = function() {
            // Optional: auto close after printing
            // window.close();
        };
    </script>
</body>
</html>