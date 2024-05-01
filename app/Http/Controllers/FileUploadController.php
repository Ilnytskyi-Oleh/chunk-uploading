<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    /**
     * @throws \Exception
     */
    public function upload(Request $request)
    {
        $fileId = $request->input('dzuuid');
        $chunkNumber = $request->input('dzchunkindex');
        $totalChunks = $request->input('dztotalchunkcount');

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        if (Storage::fileExists( 'uploads/' . $fileName)) {
            $fileName = time() . '_' .$fileName;
        }
        $chunkName = $fileId . '_' . $chunkNumber;

        $file->storeAs('chunks', $chunkName);

        if ($chunkNumber == $totalChunks - 1) {
            $this->assembleFile($fileId, $totalChunks, $fileName);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @throws \Exception
     */
    private function assembleFile($fileId, $totalChunks, $fileName)
    {
        Storage::createDirectory('uploads/');
        $filepath = storage_path('app/uploads/' . $fileName);

        $assembledFile = fopen($filepath, 'ab');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunk = file_get_contents(storage_path('app/chunks/' . $fileId . '_' . $i));
            fwrite($assembledFile, $chunk);
            unlink(storage_path('app/chunks/' . $fileId . '_' . $i));
        }

        fclose($assembledFile);
    }
}
