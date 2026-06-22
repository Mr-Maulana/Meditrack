<?php

namespace App\Http\Controllers;

use App\Mail\RadiologyReportMail;
use App\Models\Patient;
use App\Models\RadiologyMessage;
use App\Models\RadiologyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RadiologyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = RadiologyResult::with(['patient', 'operator', 'doctor']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('patient_code', 'like', "%{$search}%");
            });
        }

        $results = $query->latest()->paginate(10);
        
        return view('radiology.index', compact('results'));
    }

    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        return view('radiology.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            // Require at least one image file (single or multiple)
            'image_files' => 'required|array|min:1',
            'image_files.*' => 'image|mimes:jpeg,jpg,png|max:102400',
        ]);

        // Collect uploaded files (support single image_file input as well)
        $files = [];
        if ($request->hasFile('image_file')) {
            $files[] = $request->file('image_file');
        }
        if ($request->hasFile('image_files')) {
            $files = array_merge($files, $request->file('image_files'));
        }

        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('radiology/images', 'public');
        }

        // Log stored image paths for debugging
        \Log::info('Radiology stored image paths', $paths);

        RadiologyResult::create([
            'patient_id' => $request->patient_id,
            'operator_id' => auth()->id(),
            'image_path' => json_encode($paths),
            'status' => 'pending',
            'share_token' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        return redirect()->route('radiology.index')
            ->with('success', 'Hasil radiologi berhasil diunggah! Menunggu diagnosa dokter.');
    }

    public function show($id)
    {
        $result = RadiologyResult::with(['patient', 'operator', 'doctor'])->findOrFail($id);
        return view('radiology.show', compact('result'));
    }

    public function edit($id)
    {
        $result = RadiologyResult::with(['patient', 'operator', 'doctor'])->findOrFail($id);
        return view('radiology.edit', compact('result'));
    }

    public function update(Request $request, $id)
    {
        $result = RadiologyResult::findOrFail($id);
        $user = auth()->user();

        // 1. Determine validation rules based on role
        if ($user->isDokter() || $user->isAdmin()) {
            $request->validate([
                'diagnosis' => 'required|string',
                'reading_result' => 'required|string',
                'image_files' => 'nullable|array',
                'image_files.*' => 'image|mimes:jpeg,jpg,png|max:102400',
                'deleted_images' => 'nullable|array',
            ]);
        } else {
            $request->validate([
                'image_files' => 'nullable|array',
                'image_files.*' => 'image|mimes:jpeg,jpg,png|max:102400',
                'deleted_images' => 'nullable|array',
            ]);
        }

        // 2. Fetch current image paths
        $currentPaths = $result->image_paths;

        // 3. Process deletions
        $deletedPaths = $request->input('deleted_images', []);
        foreach ($deletedPaths as $path) {
            if (in_array($path, $currentPaths)) {
                Storage::disk('public')->delete($path);
                $currentPaths = array_diff($currentPaths, [$path]);
            }
        }
        $currentPaths = array_values($currentPaths);

        // 4. Process new uploads
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $currentPaths[] = $file->store('radiology/images', 'public');
            }
        }

        // 5. Ensure at least one image remains
        if (count($currentPaths) === 0) {
            return back()->withErrors(['image_files' => 'Laporan pemeriksaan radiologi harus memiliki minimal 1 gambar scan.']);
        }

        // 6. Build database updates
        $updates = [
            'image_path' => json_encode($currentPaths),
        ];

        if ($user->isDokter() || $user->isAdmin()) {
            $updates['diagnosis'] = $request->diagnosis;
            $updates['reading_result'] = $request->reading_result;
            $updates['doctor_id'] = $user->id;
            $updates['status'] = 'completed';
        } else {
            // If operator updates scan, reset status/diagnosis so doctor can re-evaluate
            $updates['status'] = 'pending';
            $updates['diagnosis'] = null;
            $updates['reading_result'] = null;
            $updates['doctor_id'] = null;
        }

        $result->update($updates);

        return redirect()->route('radiology.edit', $id)
            ->with('success', 'Data hasil radiologi berhasil diperbarui!');
    }

    public function send(Request $request, $id, $channel)
    {
        $result = RadiologyResult::with('patient')->findOrFail($id);

        if (!$result->reading_result) {
            return back()->with('error', 'Tidak dapat mengirim! Hasil baca dokter belum diisi.');
        }

        $shareLink = route('radiology.public-report', $result->share_token);

        if ($channel === 'email') {
            if (!$result->patient->email) {
                return back()->with('error', 'Pasien tidak memiliki alamat email.');
            }

            // Send actual email (using Laravel log/smtp)
            Mail::to($result->patient->email)->send(new RadiologyReportMail($result));

            // Log mock message
            RadiologyMessage::create([
                'radiology_result_id' => $result->id,
                'sender_type' => 'staff',
                'sender_id' => auth()->id(),
                'channel' => 'email',
                'message_text' => "Laporan Radiologi dan hasil baca dokter telah dikirim ke email pasien ({$result->patient->email}). Tautan laporan: {$shareLink}",
            ]);

            $result->update([
                'sent_via' => $result->sent_via === 'whatsapp' ? 'both' : 'email',
                'sent_at' => now(),
            ]);

            return back()->with('success', 'Laporan radiologi berhasil dikirim ke Gmail pasien!');
        } 
        
        if ($channel === 'whatsapp') {
            $phone = preg_replace('/[^0-9]/', '', $result->patient->phone);
            // Ensure country code format
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $pdfLink = route('radiology.public-report.pdf', $result->share_token);
            $messageText = "Halo *{$result->patient->name}*, Berikut adalah Laporan Hasil Radiologi Anda dari Rumkit TK III IM 07.01 Lhokseumawe. *Diagnosis*: {$result->diagnosis} *Hasil Baca Dokter*: {$result->reading_result} Buka tautan berikut untuk melihat gambar radiologi dan laporan lengkap Anda: {$shareLink}\n\nUnduh PDF Laporan Resmi: {$pdfLink}\n\nSemoga lekas sembuh.";

            // Log mock message
            RadiologyMessage::create([
                'radiology_result_id' => $result->id,
                'sender_type' => 'staff',
                'sender_id' => auth()->id(),
                'channel' => 'whatsapp',
                'message_text' => $messageText,
            ]);

            $result->update([
                'sent_via' => $result->sent_via === 'email' ? 'both' : 'whatsapp',
                'sent_at' => now(),
            ]);

            $encodedText = urlencode($messageText);
            $whatsappUrl = "https://api.whatsapp.com/send?phone={$phone}&text={$encodedText}";

            return redirect($whatsappUrl);
        }

        return back()->with('error', 'Saluran pengiriman tidak dikenal.');
    }

    public function chatIndex()
    {
        $results = RadiologyResult::with(['patient', 'messages' => function($q) {
            $q->latest();
        }])
        ->whereHas('messages')
        ->latest()
        ->get();

        // If no chats exist, get completed radiology reports so we can start one
        $completedResults = RadiologyResult::with('patient')
            ->where('status', 'completed')
            ->whereDoesntHave('messages')
            ->latest()
            ->get();

        return view('radiology.chat', compact('results', 'completedResults'));
    }

    public function chatShow($id)
    {
        $result = RadiologyResult::with(['patient', 'messages.sender'])->findOrFail($id);
        return response()->json([
            'result' => $result,
            'messages' => $result->messages,
        ]);
    }

    public function chatSend(Request $request, $id)
    {
        $request->validate([
            'message_text' => 'required|string',
            'channel' => 'required|in:whatsapp,email',
        ]);

        $result = RadiologyResult::with('patient')->findOrFail($id);
        $sender = auth()->user();

        $message = RadiologyMessage::create([
            'radiology_result_id' => $result->id,
            'sender_type' => 'staff',
            'sender_id' => $sender->id,
            'channel' => $request->channel,
            'message_text' => $request->message_text,
        ]);

        $responsePayload = [
            'success' => true,
            'message' => $message->load('sender'),
        ];

        // Gmail channel: send actual email
        if ($request->channel === 'email' && $result->patient->email) {
            try {
                Mail::to($result->patient->email)->send(
                    new \App\Mail\RadiologyChatMessageMail($result, $request->message_text, $sender->name)
                );
            } catch (\Exception $e) {
                // Keep it resilient if mailer is not configured
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email chat: ' . $e->getMessage());
            }
        }

        // WhatsApp channel: generate whatsapp redirect web link
        if ($request->channel === 'whatsapp' && $result->patient->phone) {
            $phone = preg_replace('/[^0-9]/', '', $result->patient->phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $encodedText = urlencode($request->message_text);
            $responsePayload['whatsapp_url'] = "https://api.whatsapp.com/send?phone={$phone}&text={$encodedText}";
        }

        return response()->json($responsePayload);
    }

    public function simulateReply(Request $request, $id)
    {
        $result = RadiologyResult::with('patient')->findOrFail($id);
        $channel = $request->input('channel', 'whatsapp');

        // Sample automatic responses
        $replies = [
            "Baik, terima kasih banyak Dok/Operator atas penjelasannya. Sangat membantu.",
            "Apakah hasil radiologi ini menunjukkan ada kelainan yang serius?",
            "Untuk membaca hasil fisik rontgen-nya kapan saya bisa datang kembali ke rumah sakit?",
            "Baik Dok, saya akan segera menebus obat yang disarankan.",
            "Terima kasih infonya, saya akan datang kontrol minggu depan sesuai jadwal.",
        ];

        $randomReply = $replies[array_rand($replies)];

        $message = RadiologyMessage::create([
            'radiology_result_id' => $result->id,
            'sender_type' => 'patient',
            'sender_id' => null,
            'channel' => $channel,
            'message_text' => $randomReply,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function publicReport($share_token)
    {
        $result = RadiologyResult::with(['patient', 'operator', 'doctor', 'messages'])
            ->where('share_token', $share_token)
            ->firstOrFail();

        return view('radiology.public', compact('result'));
    }

    public function publicReportPdf($share_token)
    {
        $result = RadiologyResult::with(['patient', 'operator', 'doctor'])
            ->where('share_token', $share_token)
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.radiology_report_pdf', compact('result'));

        return $pdf->stream('laporan_radiologi_' . strtolower(str_replace(' ', '_', $result->patient->name)) . '.pdf');
    }

    // Admin delete functionality
    public function destroy($id)
    {
        // Only admin can delete
        if (!auth()->user() || !method_exists(auth()->user(), 'isAdmin') || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $result = RadiologyResult::findOrFail($id);

        // Delete stored image files if they exist
        foreach ($result->image_paths as $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
        if ($result->preview_image_path) {
            Storage::disk('public')->delete($result->preview_image_path);
        }

        $result->delete();

        return redirect()->route('radiology.index')
            ->with('success', 'Hasil radiologi berhasil dihapus.');
    }

    
}
