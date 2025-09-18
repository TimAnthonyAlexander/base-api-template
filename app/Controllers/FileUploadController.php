<?php

namespace App\Controllers;

use BaseApi\Controllers\Controller;
use BaseApi\Http\JsonResponse;
use BaseApi\Http\UploadedFile;
use BaseApi\Http\Attributes\ResponseType;
use BaseApi\Http\Attributes\Tag;
use BaseApi\Http\Validation\Attributes\Required;
use BaseApi\Http\Validation\Attributes\File;
use BaseApi\Http\Validation\Attributes\Mimes;
use BaseApi\Http\Validation\Attributes\Size;
use BaseApi\Storage\Storage;

#[Tag('Files')]
class FileUploadController extends Controller
{
    #[Required]
    #[File]
    #[Mimes(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'])]
    #[Size(5)] // 5MB max
    public UploadedFile $file;

    #[ResponseType([
        'path' => 'string',
        'url' => 'string',
        'size' => 'int',
        'type' => 'string'
    ])]
    public function post(): JsonResponse
    {
        // Validate the uploaded file
        $this->validate($this);

        // Store the file with auto-generated name
        $path = $this->file->store('uploads');

        // Get the public URL for the file
        $url = Storage::url($path);

        return JsonResponse::created([
            'path' => $path,
            'url' => $url,
            'size' => $this->file->getSize(),
            'type' => $this->file->getMimeType(),
            'original_name' => $this->file->name
        ]);
    }

    /**
     * Upload a file to public storage (accessible via web).
     */
    public function uploadPublic(): JsonResponse
    {
        // Validate the uploaded file
        $this->validate($this);

        // Store the file in public storage
        $path = $this->file->storePublicly('public/uploads');

        // Get the public URL for the file
        $url = Storage::disk('public')->url($path);

        return JsonResponse::created([
            'path' => $path,
            'url' => $url,
            'size' => $this->file->getSize(),
            'type' => $this->file->getMimeType(),
            'original_name' => $this->file->name
        ]);
    }

    /**
     * Upload a file with a custom name.
     */
    public function uploadWithCustomName(): JsonResponse
    {
        // Validate the uploaded file
        $this->validate($this);

        // Generate a custom filename
        $extension = $this->file->getExtension();
        $customName = 'custom_' . date('Y_m_d_H_i_s') . '.' . $extension;

        // Store the file with custom name
        $path = $this->file->storeAs('uploads', $customName);

        // Get the public URL for the file
        $url = Storage::url($path);

        return JsonResponse::created([
            'path' => $path,
            'url' => $url,
            'size' => $this->file->getSize(),
            'type' => $this->file->getMimeType(),
            'original_name' => $this->file->name,
            'stored_name' => $customName
        ]);
    }

    /**
     * Get information about a stored file.
     */
    public function getFileInfo(): JsonResponse
    {
        $path = $this->request->query['path'] ?? '';

        if (!$path || !Storage::exists($path)) {
            return JsonResponse::notFound('File not found');
        }

        return JsonResponse::ok([
            'path' => $path,
            'url' => Storage::url($path),
            'size' => Storage::size($path),
            'exists' => Storage::exists($path)
        ]);
    }

    /**
     * Delete a stored file.
     */
    public function deleteFile(): JsonResponse
    {
        $path = $this->request->body['path'] ?? '';

        if (!$path || !Storage::exists($path)) {
            return JsonResponse::notFound('File not found');
        }

        $deleted = Storage::delete($path);

        if ($deleted) {
            return JsonResponse::ok(['message' => 'File deleted successfully']);
        }

        return JsonResponse::error('Failed to delete file');
    }
}
