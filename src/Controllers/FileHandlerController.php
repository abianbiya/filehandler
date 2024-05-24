<?php

namespace Abianbiya\Filehandler\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Abianbiya\Filehandler\Models\MediaFile;

class FileHandlerController extends Controller
{
    public function fileSlug($slug)
	{
		$mediafile = MediaFile::whereSlug($slug)->first();
		Gate::allowIf($mediafile->canBeAccessed(), $mediafile->deniedMessage());
		
		if($mediafile->disk == 's3'){
			$response = Response::make(file_get_contents(Storage::temporaryUrl($mediafile->getAddress(), now()->addMinutes(30))), 200);
			$response->header("Content-Type", $mediafile->mime_type);
		}else{
			$response = Response::make(Storage::get('public/'.$mediafile->getAddress()), 200);
			$response->header("Content-Type", $mediafile->mime_type);
		}

		return $response;
	}
}
