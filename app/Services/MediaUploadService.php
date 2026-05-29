<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    private string $disk   = 'public';
    private string $folder = 'quiz-media';

    public function uploadImage(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field) || ! $request->file($field)->isValid()) {
            return null;
        }
        return $this->storeFile($request->file($field));
    }

    public function uploadFile(UploadedFile $file): string
    {
        return $this->storeFile($file);
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    private function storeFile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($this->folder, $filename, $this->disk);
    }
}
