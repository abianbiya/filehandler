# Abianbiya/Filehandler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Simple package for handling upload file and versioning to local disk or AWS S3.

## Installation

Via Composer

``` bash
$ composer require abianbiya/filehandler
```

## Usage

#### Main todo
1. Make your model (that gonna have attached file into it) implements `HasFile` and use `InteractsWithFile` trait
2. That's it.

#### Configure the default disk

1. Set `FILESYSTEM_DISK` env variable (local or s3)
2. Fill the configuration detail if you use s3
	```
	AWS_ACCESS_KEY_ID=
	AWS_SECRET_ACCESS_KEY=
	AWS_DEFAULT_REGION=ap-southeast-3
	AWS_BUCKET=
	```



### Storing file
#### Storing uploaded file from request

1. Set your request validation
2. Catch the file field by calling the model and then save it
	``` php
	$model->addFileFromRequest('fieldname', 'foldername')->save();
	```
	- fieldname is the form `name`
	- foldername is.. you know, the *kind* of file or whatever that categories the file into some sh*t (actually this will be used as the folder name in the storage)

#### Storing file from path

``` php
$model->addFileFromPath('path', 'foldername')->save();
```
- path is the path
- foldername is.. you know, the `kind` of file or whatever that categories the file into some sh*t (actually this will be used as the folder name in the storage)

#### Setting file properties
Set some properties to the model's attached file with this function right before `->addFileFrom{What}` called.
``` php
$model->disk('local')->setProperties($array)->replace()->addFileFrom
```
- `disk()`   for specifying the disk, default disk is env FILESYSTEM_DISK
- `setProperties()`   for additional information, put array here, this'll be store as json
- `replace()`   if the model has only one file stored to specified folder, then on update we can call this

### Retrieving the file
Access the file with adding the `file` or `files` eager loading ORM relation to you model. `file` function stands for the single attached file and `files` for multiple files.
``` php
$data['yuhu'] = Model::with('file')->whateverQueryYouWant();
```
this relation return MediaFile object instance so you can filter it using collection for multiple files or do some operation such as:
``` php
	@foreach($data as $item)
		$item->file->getPath(); // returns absolute path to the file
		$item->file->getUrl(); // returns direct url path without domain
		$item->file->getFullUrl(); // returns direct full url with domain, recommended for showing file inside html
		$item->file->getMaskedUrl(); // returns masked url with slug routing, recommended for file direct access
		$item->file->getThumbnail(int $width); // * returns <img> html tag (with lightbox) for image mimetype or a href link for others, can be used to render image or link inside table.
	@endforeach
```
* Publish asset to use lightbox within getThumbnail() method
``` bash
$ php artisan vendor:publish --tag=filehandler.assets
```
then load the assets
```html
<script src="{{ asset('build/vendor/filehandler/js/lightbox-b5.js') }}"></script>
```
additional option for wrapping file upload form with drag-and-drop input
```html

<link href="{{ asset('build/libs/dropify/css/dropify.min.css') }}" rel="stylesheet" >

<script src="{{ asset('build/libs/dropify/js/dropify.min.js') }}"></script>
```
then use class `.dropify` 

### Intercepting File Access (using masked URL)
The default access to the file is `true`, otherwise you would like to add a gate to the masked URL route, add this method into your implemented InteractsWithFile model:
```php
public function canAccessFile() : array|bool
{
	// do some complicated checking algorithm to authorized role or file ownership
	// you may use Auth::user(), or $this->created_by to check this model record's owner, or whatever
	return $allowAccess; // returning boolean
	// OR
	return [$allowAccess, 'Such a denied message to show in a 403 page']; // returning array (default message: Forbidden.)
}
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please poke me at @abianbiya (wherever) instead of using the issue tracker.

## Credits

-   [Abi Anbiya](https://github.com/abianbiya)

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dsiunnes/filehandler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dsiunnes/filehandler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/dsiunnes/filehandler/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/dsiunnes/filehandler
[link-downloads]: https://packagist.org/packages/dsiunnes/filehandler
[link-travis]: https://travis-ci.org/dsiunnes/filehandler
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/abianbiya
[link-contributors]: ../../contributors
