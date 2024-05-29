<?php

namespace Abianbiya\Filehandler;

use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Abianbiya\Filehandler\Models\MediaFile;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Abianbiya\Filehandler\Exceptions\FileStoringFailed;

trait InteractsWithFile
{
    protected $folder = 'default';
    protected $file_name = null;
    protected $properties;
	protected $mediaFile;
	protected $isReplace = false;
	protected $disk;

	public function file(): MorphOne
    {
    	return $this->morphOne(MediaFile::class, 'model')->ofMany('version', 'max');
    }

	public function files(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'model');
    }

	public function canAccessFile()
	{
		return [true, "It's OK."];
	}

	public function hasFile($folder = null)
	{
		if($folder){
			$this->loadMissing('files')->files->contains('folder', $folder);
		}else{
			return $this->loadCount('files')->files_count > 0;
		}
	}

	public function replace()
	{
		$this->isReplace = true;
		return $this;
	}

	public function disk($disk)
	{
		$this->disk = $disk;
		return $this;
	}

	public function setProperties(array $properties)
	{
		$this->properties = $properties;
		return $this;
	}

	public function addFileFromRequest(string $key, string $folder, $filename = null) : MediaFile
    {
		if(!request()->hasFile($key)){
			throw new FileStoringFailed('Request has no file!');
		}

		$this->mediaFile = app(MediaFile::class);
		$this->mediaFile->folder = str($folder)->slug('_');
		$this->mediaFile->file_name = $filename;

		$file = request()->file($key);

		return $this->saveFile($this, $file);
    }

	public function addFileFromPath(string $path, string $folder) : MediaFile
    {
		$file = new File($path);
		if(!is_file($file)){
			throw new FileStoringFailed('File not found.');
		}

		$this->mediaFile = app(MediaFile::class);
		$this->mediaFile->folder = str($folder)->slug('_');

		return $this->saveFile($this, new UploadedFile($path, last(explode('/', $path)), $file->getMimeType()));
    }

	public function saveFile(Model $model, UploadedFile $file)
	{
		$mediaFile = $this->mediaFile;
		$mediaFile->model_type = get_class($model);
		$mediaFile->model_id = $model->getKey();
		$mediaFile->version = $this->getNextVersion($model);
		$mediaFile->slug = str(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'-'.uniqid().'-'.$mediaFile->version)->slug('-');
		$mediaFile->original_name = $file->getClientOriginalName();
		$mediaFile->file_name = $this->mediaFile->file_name ?? $mediaFile->slug.'.'.$file->extension();
		$mediaFile->disk = $this->disk ?? config('filesystems.default');
		$mediaFile->size = $file->getSize();
		$mediaFile->mime_type = $file->getMimeType();
		$mediaFile->folder = $this->mediaFile->folder ?? $this->folder;
		$mediaFile->properties = Arr::wrap($this->properties);
		$mediaFile->created_by = Auth::guest() ? 'Guest' : Auth::id();
		$mediaFile->file_path = $mediaFile->getStoreAddress(false);

		if($this->isReplace){
			MediaFile::whereModelType(get_class($model))->whereModelId($model->getKey())->whereFolder($mediaFile->folder)->delete();
		}

		if($mediaFile->disk == 'local'){
			$absolute = storage_path('app/public/'.$mediaFile->getStoreAddress(false));
			// dd($absolute);
			if(!is_dir($absolute)){
				mkdir($absolute, 0777, true);
			}

			$file->storeAs('public/'.$mediaFile->getStoreAddress(false), $mediaFile->file_name);
		}else if($mediaFile->disk == 'private'){
            $diskpath = config('filesystems.disks.private.root');
            if($diskpath){
                $absolute = $diskpath.$mediaFile->getStoreAddress(false);
            }else{
                $absolute = storage_path('app/'.$mediaFile->getStoreAddress(false));
            }
			if(!is_dir($absolute)){
				mkdir($absolute, 0700, true);
			}

			$file->storeAs($mediaFile->getStoreAddress(false), $mediaFile->file_name);
            if(config('filesystems.disks.private.visibility')){
                Storage::setVisibility($mediaFile->getStoreAddress(true), config('filesystems.disks.private.visibility'));
            }

		}else if($mediaFile->disk == 's3'){
			Storage::disk('s3')->put($mediaFile->getStoreAddress(), file_get_contents($file));
		}


		return $mediaFile;
	}

	public function getNextVersion(Model $model)
	{
		return ($this->mediaFile->whereModelType(get_class($model))->whereModelId($model->getKey())->max('version') ?? 0) + 1;
	}
}
