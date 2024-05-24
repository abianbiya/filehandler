<?php

namespace Abianbiya\Filehandler;

use Illuminate\Support\Collection;
use Abianbiya\Filehandler\Models\MediaFile;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface HasFile
{   
    public function file(): MorphOne;
    
    public function files(): MorphMany;

    public function addFileFromRequest(string $file, string $folder): MediaFile;

    // public function copyMedia(string|UploadedFile $file): FileAdder;

    // public function hasMedia(string $collectionName = ''): bool;

    // public function getMedia(string $collectionName = 'default', array|callable $filters = []): Collection;

    // public function clearMediaCollection(string $collectionName = 'default'): HasMedia;

    // public function clearMediaCollectionExcept(string $collectionName = 'default', array|Collection $excludedMedia = []): HasMedia;

    // public function shouldDeletePreservingMedia(): bool;

    // public function loadMedia(string $collectionName);

    // public function addMediaConversion(string $name): Conversion;

    // public function registerMediaConversions(Media $media = null): void;

    // public function registerMediaCollections(): void;

    // public function registerAllMediaConversions(): void;
}
