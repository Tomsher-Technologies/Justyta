<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ServiceRequestFileService
{
    public function __construct()
    {
        //
    }

    public function uploadCompletedFile(ServiceRequest $serviceRequest, UploadedFile $file): ?string
    {
        $uniqueName = 'completed_file_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filename = "uploads/{$serviceRequest->service_slug}/{$serviceRequest->id}/" . $uniqueName;

        $fileContents = file_get_contents($file);
        Storage::disk('public')->put($filename, $fileContents);
        $uploadedFileUrl = Storage::url($filename);

        $this->addCompletedFile($serviceRequest, $uploadedFileUrl);

        return $uploadedFileUrl;
    }
    protected function addCompletedFile(ServiceRequest $serviceRequest, string $fileUrl): void
    {
        $completedFiles = $serviceRequest->completed_files ?? [];

        if (!is_array($completedFiles)) {
            $completedFiles = [];
        }

        $completedFiles[] = $fileUrl;
        $serviceRequest->update(['completed_files' => $completedFiles]);
    }

    public function download($id)
    {
        $serviceRequest = ServiceRequest::with('service', 'statusHistories')->findOrFail($id);

        if ($serviceRequest->status !== 'completed') {
            abort(403, __('frontend.download_not_allowed'));
        }

        $completedFiles = $serviceRequest->completed_files ?? [];

        if (empty($completedFiles)) {
            abort(404, __('frontend.no_files_available'));
        }

        if (count($completedFiles) === 1) {
            return $this->downloadSingleFile($completedFiles[0]);
        }

        return $this->downloadMultipleFiles($serviceRequest, $completedFiles);
    }

    protected function downloadSingleFile($fileUrl)
    {
        $storagePath = str_replace('/storage/', 'public/', $fileUrl);
        $fullPath = storage_path("app/{$storagePath}");

        if (file_exists($fullPath)) {
            return response()->download($fullPath);
        }

        abort(404, __('frontend.file_not_found'));
    }

    protected function downloadMultipleFiles($serviceRequest, $completedFiles)
    {
        $zipFileName = "completed_service_{$serviceRequest->id}.zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");

        if (!file_exists(storage_path("app/temp"))) {
            mkdir(storage_path("app/temp"), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($completedFiles as $index => $fileUrl) {
                $storagePath = str_replace('/storage/', 'public/', $fileUrl);
                $fullPath = storage_path("app/{$storagePath}");
                $fileName = basename($fileUrl);

                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $fileName);
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        abort(500, 'Could not create archive');
    }

    public function deleteCompletedFile(ServiceRequest $serviceRequest, string $fileUrl): bool
    {
        $storagePath = str_replace('/storage/', 'public/', $fileUrl);

        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }

        $completedFiles = $serviceRequest->completed_files ?? [];
        $completedFiles = array_filter($completedFiles, fn($file) => $file !== $fileUrl);
        $serviceRequest->update(['completed_files' => array_values($completedFiles)]);

        return true;
    }

    public function getCompletedFiles(ServiceRequest $serviceRequest): array
    {
        return $serviceRequest->completed_files ?? [];
    }

    public function hasCompletedFiles(ServiceRequest $serviceRequest): bool
    {
        $files = $this->getCompletedFiles($serviceRequest);
        return !empty($files);
    }

    public function getCompletedFilesCount(ServiceRequest $serviceRequest): int
    {
        return count($this->getCompletedFiles($serviceRequest));
    }
}
