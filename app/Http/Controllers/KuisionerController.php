<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Setting; // <-- Tambahkan ini
use Symfony\Component\HttpFoundation\StreamedResponse;

class KuisionerController extends Controller
{

    public function storeQuestion(Request $request)
    {
        $request->validate(['pertanyaan' => 'required|string|max:255']);
        Question::create(['pertanyaan' => $request->pertanyaan]);
        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $request->validate(['pertanyaan' => 'required|string|max:255']);
        $question->update(['pertanyaan' => $request->pertanyaan]);
        return back()->with('success', 'Pertanyaan berhasil diupdate.');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    // --- FITUR ADMIN: Hasil & Download CSV ---
    public function adminResults()
    {
        $responses = QuestionnaireResponse::with(['user', 'answers.question'])->latest()->get();
        return Inertia::render('Admin/Kuisioner/Results', compact('responses'));
    }

    public function exportCsv()
    {
        $responses = QuestionnaireResponse::with(['user', 'answers.question'])->latest()->get();
        $questions = Question::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=hasil_kuisioner.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Petugas', 'Tanggal Pengisian'];
        foreach ($questions as $q) {
            $columns[] = $q->pertanyaan;
        }

        $callback = function() use($responses, $questions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // Tulis header kolom

            foreach ($responses as $response) {
                $row = [
                    $response->user->name,
                    $response->created_at->format('Y-m-d H:i:s')
                ];
                
                $answers = $response->answers->keyBy('question_id');
                foreach ($questions as $q) {
                    $row[] = isset($answers[$q->id]) ? $answers[$q->id]->jawaban : '-';
                }
                fputcsv($file, $row); // Tulis baris data
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function submitForm(Request $request)
    {
        $request->validate(['answers' => 'required|array']);

        $response = QuestionnaireResponse::create(['user_id' => auth()->id()]);

        foreach ($request->answers as $question_id => $jawaban) {
            QuestionnaireAnswer::create([
                'questionnaire_response_id' => $response->id,
                'question_id' => $question_id,
                'jawaban' => $jawaban
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Terima kasih, hasil kuisioner berhasil dikirim.');
    }

    public function adminQuestions()
    {
        $questions = Question::latest()->get();
        // Ambil status kuisioner saat ini dari tabel settings
        $isKuisionerActive = Setting::where('key', 'kuisioner_status')->value('value') == '1';
        
        return Inertia::render('Admin/Kuisioner/Questions', compact('questions', 'isKuisionerActive'));
    }

    // Fungsi baru untuk Toggle ON/OFF
    public function toggleStatus(Request $request)
    {
        $status = $request->boolean('status') ? '1' : '0';
        Setting::updateOrCreate(
            ['key' => 'kuisioner_status'],
            ['value' => $status]
        );

        return back()->with('success', 'Status kuisioner berhasil diperbarui.');
    }

    public function fillForm()
    {
        // Proteksi jika petugas memaksa masuk via URL saat kuisioner OFF
        $isActive = Setting::where('key', 'kuisioner_status')->value('value') == '1';
        if (!$isActive) {
            return redirect()->route('dashboard')->with('error', 'Kuisioner sedang ditutup.');
        }

        $questions = Question::where('is_active', true)->get();
        return Inertia::render('Kuisioner/Fill', compact('questions'));
    }
}