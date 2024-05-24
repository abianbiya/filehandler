<?php

namespace Abianbiya\Filehandler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaFile extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = "sys_file";
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = ['properties' => 'array'];
    protected $fillable = ['model_type', 'model_id', 'slug', 'folder', 'name', 'file_name', 'mime_type', 'driver', 'disk', 'size', 'version', 'created_by', 'updated_by', 'deleted_by'];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getStoreAddress($withName = true)
    {

        return implode('/', [date('Y'), date('m'), date('d')]).'/'.$this->folder.($withName ? '/'.$this->file_name : '');
    }

    public function getAddress($withName = true)
    {

        return $this->file_path.($withName ? '/'.$this->file_name : '');
    }

    public function getMaskedUrl()
    {
        return route('public.file.read', $this->slug);
    }

    public function getUrl()
    {
        return $this->generateUrl();
    }

    public function getFullUrl()
    {
        return asset($this->generateUrl());
    }

    public function getThumbnail($width = 100)
    {
        if(str($this->mime_type)->contains('image')){
            $html = '<a href="'.$this->getFullUrl().'" data-toggle="lightbox" data-caption="'.$this->file_name.'">
                        <img src="'.$this->getFullUrl().'" class="img-thumbnail rounded mx-auto d-block" width="'.$width.'">
                    </a>';
            // '<img src="'.$this->getFullUrl().'" class="img-thumbnail rounded mx-auto d-block" alt="'.$this->file_name.'" width="'.$width.'">';
        }else{
            $html = '<a target="_blank" href="'.$this->getMaskedUrl().'" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" aria-label="'.__('Unduh').'"> <i class="bi bi-download"></i> </a>';
        }

        return $html;
    }

    public function generateUrl()
    {
        if($this->disk == 's3'){
            return Storage::temporaryUrl($this->getAddress(), now()->addMinutes(30));
        }else{
            return str($this->getAddress())->prepend('storage/');
        }
    }

    public function getPath()
    {
        return Storage::path($this->getAddress());
    }

    public function getHumanReadableSize(): string
    {
        $sizeInBytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes == 0) {
            return '0 '.$units[1];
        }

        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2).' '.$units[$i];
    }

    public function setFolder(string $folder) : MediaFile
    {
        $this->folder = $folder;
        return $this;
    }

    public function canBeAccessed()
    {
        $access = $this->model->canAccessFile();
        if(is_bool($access)){
            return $access;
        }elseif(is_array($access)){
            return $access[0];
        }
    }

    public function deniedMessage()
    {
        $access = $this->model->canAccessFile();
        if(is_bool($access)){
            return "Forbidden";
        }elseif(is_array($access)){
            return @$access[1];
        }
    }
    

}
