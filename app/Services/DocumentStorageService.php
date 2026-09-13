<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentStorageService
{
    /**
     * Allowed MIME types for customer sensitive documents.
     */
    protected array $allowedMimes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Maximum file size in bytes (10MB).
     */
    protected int $maxFileSize = 10485760;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Upload customer document into private vault.
     */
    public function upload(
        Customer $customer,
        UploadedFile $file,
        string $documentType,
        User $uploader
    ): CustomerDocument {
        // Validate MIME type
        $mime = $file->getMimeType();
        if (!in_array($mime, $this->allowedMimes, true)) {
            throw new InvalidArgumentException("Tipe berkas tidak didukung: {$mime}. Hanya diizinkan PDF, JPG, PNG, atau WEBP.");
        }

        // Validate size
        $size = $file->getSize();
        if ($size > $this->maxFileSize) {
            throw new InvalidArgumentException("Ukuran berkas melebihi batas maksimal 10MB.");
        }

        return DB::transaction(function () use ($customer, $file, $documentType, $uploader, $mime, $size) {
            // Generate randomized UUID filename
            $extension = $file->getClientOriginalExtension();
            $storageFilename = (string) Str::uuid() . '.' . $extension;

            // Store strictly in private directory
            $path = $file->storeAs("documents/{$customer->id}", $storageFilename, 'local');

            $document = CustomerDocument::create([
                'customer_id' => $customer->id,
                'uploaded_by_id' => $uploader->id,
                'document_type' => $documentType,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $mime,
                'file_size' => $size,
            ]);

            $this->auditLogService->log(
                action: 'customer_document_uploaded',
                entity: $document,
                before: null,
                after: [
                    'customer_id' => $customer->id,
                    'document_type' => $documentType,
                    'file_name' => $document->file_name,
                ]
            );

            return $document;
        });
    }

    /**
     * Securely stream customer document for authorized access with audit trail.
     */
    public function download(CustomerDocument $document, User $accessor): StreamedResponse
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Berkas tidak ditemukan di penyimpanan privat.');
        }

        $this->auditLogService->log(
            action: 'customer_document_accessed',
            entity: $document,
            before: null,
            after: [
                'document_id' => $document->id,
                'document_type' => $document->document_type,
                'accessed_by' => $accessor->name,
            ]
        );

        return Storage::download(
            $document->file_path,
            $document->file_name,
            ['Content-Type' => $document->mime_type]
        );
    }
}
