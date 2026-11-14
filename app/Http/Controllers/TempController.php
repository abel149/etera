<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class TempController extends Controller
{
    public function uploadPartImage(Request $request)
    {
        Log::info('UploadPartImage: started', [
            'user_id' => auth()->id(),
            'type' => 'image',
            'all_files' => array_keys($request->allFiles()),
            'all_inputs' => $request->except(['_token'])
        ]);

        try {
            $allFiles = $request->allFiles();
            $flattenedFiles = $this->flattenFilesArray($allFiles);
            $uploadedPaths = [];

            foreach ($flattenedFiles as $fieldName => $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    // Save temporarily first
                    $path = $file->store('uploads/temp', 'public');
                    $uploadedPaths[] = [
                        'field' => $fieldName,
                        'original_name' => $file->getClientOriginalName(),
                        'temp_path' => $path,
                        'url' => Storage::disk('public')->url($path),
                    ];

                    Log::info('UploadPartImage: file uploaded to temp', [
                        'field' => $fieldName,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType(),
                    ]);
                } else {
                    Log::warning('UploadPartImage: invalid file received', [
                        'user_id' => auth()->id(),
                        'field' => $fieldName,
                        'upload_error' => $file instanceof UploadedFile ? $file->getError() : 'not an UploadedFile',
                        'error_message' => $file instanceof UploadedFile ? $file->getErrorMessage() : null,
                        'client_name' => $file instanceof UploadedFile ? $file->getClientOriginalName() : null,
                        'client_size' => $file instanceof UploadedFile ? $file->getSize() : null,
                        'content_length' => $request->header('Content-Length'),
                    ]);
                }
            }

            if (count($uploadedPaths) > 0) {
                return response()->json(['success' => true, 'files' => $uploadedPaths]);
            }

            Log::error('UploadPartImage: no valid file found', [
                'user_id' => auth()->id(),
                'files_received' => array_keys($flattenedFiles),
                'content_length' => $request->header('Content-Length'),
                'php_upload_max' => ini_get('upload_max_filesize'),
                'php_post_max' => ini_get('post_max_size'),
            ]);
            return response()->json(['success' => false, 'message' => 'No valid file uploaded'], 400);

        } catch (\Exception $e) {
            Log::error('UploadPartImage: failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function flattenFilesArray(array $files, string $prefix = '')
    {
        $flat = [];
        foreach ($files as $key => $value) {
            $fieldName = $prefix ? "{$prefix}.{$key}" : $key;
            if (is_array($value)) {
                $flat = array_merge($flat, $this->flattenFilesArray($value, $fieldName));
            } else {
                $flat[$fieldName] = $value;
            }
        }
        return $flat;
    }

    /**
     * Receives client-side upload diagnostics (FilePond errors, file metadata)
     * so browser-side failures that never reach the server are still visible
     * in storage/logs/laravel.log.
     */
    public function clientUploadLog(Request $request)
    {
        $event = substr((string) $request->input('event'), 0, 50);
        $context = [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 300),
            'event' => $event,
            'page' => substr((string) $request->input('page'), 0, 200),
            'data' => array_map(
                fn ($v) => is_scalar($v) ? substr((string) $v, 0, 300) : null,
                $request->only([
                    'input', 'error_main', 'error_sub', 'error_code', 'file_name',
                    'file_size', 'file_type', 'last_modified', 'server_id',
                ])
            ),
        ];

        in_array($event, ['file_added', 'process_success'], true)
            ? Log::info('ClientUpload: ' . $event, $context)
            : Log::warning('ClientUpload: ' . $event, $context);

        return response()->json(['ok' => true]);
    }

    public function revert(Request $request)
    {
        try {
            $path = trim($request->getContent(), '"');
            Log::info('DeletePartImage: attempting', ['path' => $path]);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('DeletePartImage: success', ['path' => $path]);
                return response()->json(['success' => true]);
            }

            Log::warning('DeletePartImage: file not found', ['path' => $path]);
            return response()->json(['success' => false, 'message' => 'File not found'], 404);

        } catch (\Exception $e) {
            Log::error('DeletePartImage: failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
