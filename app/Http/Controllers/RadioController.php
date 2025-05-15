<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Radio;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use FFMpeg;
use ZipArchive;
use Illuminate\Support\Facades\Response;

use FFMpeg\Format\Audio\Mp3;
use Illuminate\Support\Facades\Storage;

class RadioController extends Controller{
 
public function upload($id){
    $radio = Radio::findOrFail($id);
    if (empty($radio->soundfile_name)) {
        return back()->with('error', "Missing sound file name for radio ID: {$radio->title_id}");
    }
    $filePath = 'audio_file/' . $radio->soundfile_name; // Adjust the folder name here
    if (!Storage::disk('public')->exists($filePath)) {
        return back()->with('error', "File {$filePath} not found.");
    }
    // Clean the title
    $title = $radio->title ?? 'radio';
    $cleanTitle = preg_replace('/[\/\\\\\:\*\?\"\<\>\|\s]+/u', '_', $title);
    $cleanTitle = preg_replace('/[^\p{Arabic}\p{Latin}\p{N}_-]/u', '', $cleanTitle);
    $cleanTitle = trim($cleanTitle, '_');
    $extension = pathinfo($radio->soundfile_name, PATHINFO_EXTENSION) ?: 'wav';
    $filename = $cleanTitle . '.' . $extension;
    return Storage::disk('public')->download($filePath, $filename);
}



public function downloadMultiple(Request $request){
    $selectedIds = $request->input('selected_radios', []);
    if (empty($selectedIds)) {
        return back()->with('error', 'No radios selected.');
    }
    $radios = Radio::whereIn('title_id', $selectedIds)->get();
    if ($radios->isEmpty()) {
        return back()->with('error', 'No radios found.');
    }
   //one file selected:
    if ($radios->count() === 1) {
        $radio = $radios->first();

        if (empty($radio->soundfile_name)) {
            return back()->with('error', "Missing sound file name for radio ID: {$radio->title_id}");
        }

        $filePath = 'audio_file/' . $radio->soundfile_name;

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', "File {$filePath} not found.");
        }

        // Clean the title
        $title = $radio->title ?? 'radio';
        $cleanTitle = preg_replace('/[\/\\\\\:\*\?\"\<\>\|\s]+/u', '_', $title);
        $cleanTitle = preg_replace('/[^\p{Arabic}\p{Latin}\p{N}_-]/u', '', $cleanTitle);
        $cleanTitle = trim($cleanTitle, '_');

        $extension = pathinfo($radio->soundfile_name, PATHINFO_EXTENSION) ?: 'wav';
        $filename = $cleanTitle . '.' . $extension;

        return Storage::disk('public')->download($filePath, $filename);
    }

    // Multiple files: create a ZIP
    $zip = new \ZipArchive();
    $timestamp = now()->format('Ymd-His');
    $zipFileName = "radios-{$timestamp}.zip";

    $tempDir = storage_path('app/temp');
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    $zipPath = "{$tempDir}/{$zipFileName}";

    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
        return back()->with('error', 'Could not create ZIP file.');
    }

    $addedFiles = 0;

    foreach ($radios as $radio) {
        if (empty($radio->soundfile_name)) {
            \Log::warning("Skipping radio with empty soundfile_name. Title ID: {$radio->title_id}");
            continue;
        }

        $relativePath = 'audio_file/' . $radio->soundfile_name;
        $absolutePath = Storage::disk('public')->path($relativePath);

        \Log::info("Checking file: $absolutePath");

        if (file_exists($absolutePath) && is_readable($absolutePath)) {
            $title = $radio->title ?? 'radio';
            $cleanTitle = preg_replace('/[\/\\\\\:\*\?\"\<\>\|\s]+/u', '_', $title);
            $cleanTitle = preg_replace('/[^\p{Arabic}\p{Latin}\p{N}_-]/u', '', $cleanTitle);
            $cleanTitle = trim($cleanTitle, '_');

            $extension = pathinfo($radio->soundfile_name, PATHINFO_EXTENSION) ?: 'wav';

            $uniqueFilename = $radio->title_id . '_' . $cleanTitle . '.' . $extension;

            $zip->addFile($absolutePath, $uniqueFilename);
            $addedFiles++;
        } else {
            \Log::error("File missing or unreadable: {$absolutePath}");
        }
    }

    $zip->close();

    if ($addedFiles === 0) {
        return back()->with('error', 'No valid audio files were found to download.');
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
}

public function convertToMp3($filename){
    $wavPath = storage_path('app/public/audio_file/' . $filename);
    $mp3Name = pathinfo($filename, PATHINFO_FILENAME) . '.mp3';
    $mp3Path = storage_path('app/public/audio_files/' . $mp3Name);
    if (!file_exists($mp3Path)) {
        
            $ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries'  => env('FFMPEG_BINARIES', 'ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
            ]);

            $audio = $ffmpeg->open($wavPath);
            $format = new Mp3();
            $format->setAudioKiloBitrate(192);

            // Log FFmpeg output
            $listeners = [
                'listeners' => function ($type, $message) {
                    \Log::info("FFmpeg: [$type] $message");
                }
            ];
            $audio->save($format, $mp3Path, $listeners);
    }
    return $mp3Name;
}

public function data(){
    return View::make('form');
}
    

public function show(Radio $rad){
        // Convert the sound file to MP3 and get the MP3 file name
        $mp3 = $this->convertToMp3($rad->soundfile_name);
        
        // Return the view with radio details and the MP3 file
        return View::make('plusrad', ['rad' => $rad, 'mp3file' => $mp3]);
}
public function index(Request $request)
{
    $searchTerm = $request->search;
    $query = Radio::query();

    if (!empty($searchTerm)) {
        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
              ->orWhere('author', 'like', "%{$searchTerm}%")
              ->orWhere('interpret', 'like', "%{$searchTerm}%");
        });
    }

    $radios = $query->paginate(10)->withQueryString();

    if ($request->download == 'excel') {
        return $this->downloadExcel($query->get());
    }

    return view('radio', [
        'radios' => $radios,
        // Pass explicitly to view
    ]);
}
public function downloadExcel($radios)
{
    // FastExcel will create the Excel file
    return (new FastExcel($radios))->download('radios_search_results.xlsx',function($radio){
     return[
        'title' => $radio->title,
        'author' => $radio->author,
        'Durée' => $radio->Durée,
        'interpret' => $radio->interpret
        ];
    });
}

public function import(Request $request){
        // Validate and handle file import
        $request->validate(['test' => 'required|file|mimes:xlsx,csv']);
        $maxRows = 100; // Set your maximum limit here
        $importedRows = 0;

        (new FastExcel)->import($request->file('test')->getRealPath(), function($line) use (&$importedRows, $maxRows) {
            if ($importedRows >= $maxRows) {
                return; // Stop importing after max rows
            }
            
            // Format the date correctly
            if ($line['Durée'] instanceof \DateTimeImmutable) {
                $line['Durée'] = $line['Durée']->format('H:i:s');  // Format it to a string
            }
             if ($line['last_modif_time'] instanceof \DateTimeImmutable) {
                $line['last_modif_time'] = $line['last_modif_time']->format('Y-m-d H:i:s');  // Format it to a string
            }
            
            // Insert or update radio data
            Radio::firstOrCreate([
                'title_id' => $line['Title_id'] ?? $line['title_id'] ?? null,
                'title' => $line['Title'] ?? $line['title'] ?? null,
                'soundfile_name' => $line['Soundfile_name'] ?? $line['soundfile_name'] ?? null,
                'author' => $line['Author'] ?? $line['author'] ?? null,
                'durée(ms)' => $line['Durée(ms)'] ?? $line['durée(ms)'] ?? null,
                'Durée' => $line['Durée'] ?? $line['Durée'] ?? null,
                'interpret' => $line['interpret'] ?? $line['interpret'] ?? null,
                'last_modif_time' => $line['Last_modif_time'] ?? $line['last_modif_time'] ?? null,
                'commentaire1' => $line['Commentaire1'] ?? $line['commentaire1'] ?? null,
                'commentaire2' => $line['Commentaire2'] ?? $line['commentaire2'] ?? null,
                'commentaire3' => $line['Commentaire3'] ?? $line['commentaire3'] ?? null
            ]);
            
            $importedRows++;
        });
        
        // Redirect back with success message
        return redirect('radio')->with('success', 'Radio imported successfully!');
    }

    public function export()
    {
        // Export all radios to Excel
        return (new FastExcel(Radio::all()))->download('Daletplus_14_10_2020.xlsx', function ($radio) {
            return [
                'title_id' => $radio->title_id,
                'title' => $radio->title,
                'soundfile_name' => $radio->soundfile_name,
                'author' => $radio->author,
                'durée(ms)' => $radio['durée(ms)'],
                'Durée' => $radio->Durée,
                'interpret' => $radio->interpret,
                'last_modif_time' => $radio->last_modif_time,
                'commentaire1' => $radio->commentaire1,
                'commentaire2' => $radio->commentaire2,
                'commentaire3' => $radio->commentaire3,
            ];
        });
    }
}
