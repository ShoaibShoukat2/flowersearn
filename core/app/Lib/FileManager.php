<?php

namespace App\Lib;

use App\Traits\FileInfo;
use Intervention\Image\Facades\Image;

class FileManager
{
    /*
    |--------------------------------------------------------------------------
    | File Manager
    |--------------------------------------------------------------------------
    |
    | FileManager class is using to manage edit, update, remove files. Developer
    | can manage any kind of files from here. But some limitations is here for image.
    | This class using a trait to manage the file paths and sizes. Developer can also
    | use this class as a helper function.
    |
    */

	use FileInfo;

    /**
    * The file which will be uploaded
    *
    *
    * @var object
    */
	protected $file;

    /**
    * The path where will be uploaded
    *
    * @var string
    */
	public $path;

    /**
    * The size, if the file is image
    *
    * @var string
    */
	public $size;

    /**
    * Check the file is image or not
    *
    * @var boolean
    */
	protected $isImage;

    /**
    * Thumbnail version size, if required
    * and if the file is image
    *
    * @var string
    */
	public $thumb;

    /**
    * Old filename, which will be removed
    *
    * @var string
    */
	public $old;

    /**
    * Current filename, which is uploading
    *
    * @var string
    */
	public $filename;


    /**
    * Set the file and file type to properties if exist
    *
    * @param $file
    * @return void
    */
	public function __construct($file = null){
		$this->file = $file;
		if ($file) {
			$imageExtensions = ['jpg','jpeg','png','webp','gif','JPG','JPEG','PNG','WEBP','GIF'];
			if (in_array($file->getClientOriginalExtension(), $imageExtensions)) {
				$this->isImage = true;
			}else{
				$this->isImage = false;
			}
		}
	}

    /**
    * File upload process
    *
    * @return void
    */
	public function upload(){
        $this->assertSafeUpload();

        $this->path = $this->resolvePath($this->path);

        //create the directory if doesn't exists
		$path = $this->makeDirectory();
		if (!$path) throw new \Exception('File could not been created.');

        //remove the old file if exist
		if ($this->old) {
            $this->removeFile();
	    }

        //get the filename
	    $filename = $this->getFileName();
	    $this->filename = $filename;

        //upload file or image
	    if ($this->isImage == true) {
	    	$this->uploadImage();
	    }else{
	    	$this->uploadFile();
	    }
	}

    /**
    * Upload the file if this is image
    *
    * @return void
    */
	protected function uploadImage(){
		// Simple file upload without image processing
		// This avoids GD dependency issues
		$this->file->move($this->path, $this->filename);
		
		// If thumbnail is required, copy the original file as thumbnail
		if ($this->thumb) {
			if ($this->old) {
				$this->removeFile($this->path . '/thumb_' . $this->old);
			}
			// Copy the uploaded file as thumbnail
			copy($this->path . '/' . $this->filename, $this->path . '/thumb_' . $this->filename);
		}
	}


    /**
    * Upload the file if this is not a image
    *
    * @return void
    */
	protected function uploadFile(){
	    $this->file->move($this->path,$this->filename);
	}

    /**
    * Make directory doesn't exists
    * Developer can also call this method statically
    *
    * @param $location
    * @return string
    */
	public function makeDirectory($location = null){
		if (!$location) $location = $this->path;
		$location = $this->resolvePath($location);
		if (file_exists($location)) return true;
    	return mkdir($location, 0775, true);
	}

	protected function resolvePath($location){
		if (!$location || $location[0] === '/' || preg_match('/^[A-Za-z]:[\\\\\\/]/', $location)) {
			return $location;
		}
		if ($location === 'assets' || strpos($location, 'assets/') === 0) {
			return public_path($location);
		}
		return $location;
	}

    /**
    * Remove all directory inside the location
    * Developer can also call this method statically
    *
    * @param $location
    * @return void
    */
	public function removeDirectory($location = null){
		if (!$location) $location = $this->path;
		if (! is_dir($location)) {
	        throw new \InvalidArgumentException("$location must be a directory");
	    }
	    if (substr($location, strlen($location) - 1, 1) != '/') {
	        $location .= '/';
	    }
	    $files = glob($location . '*', GLOB_MARK);
	    foreach ($files as $file) {
	        if (is_dir($file)) {
	            static::removeDirectory($file);
	        } else {
	            unlink($file);
	        }
	    }
	    rmdir($location);
	}

    /**
    * Remove the file if exists
    * Developer can also call this method statically
    *
    * @param $path
    * @return void
    */
	public function removeFile($path = null)
	{
		if (!$path) $path = $this->path . '/' . $this->old;

	    file_exists($path) && is_file($path) ? @unlink($path) : false;

	    if ($this->thumb) {
	    	if (!$path) $path = $this->path . '/thumb_' . $this->old;
	    	file_exists($path) && is_file($path) ? @unlink($path) : false;
	    }
	}

    /**
    * Generating the filename which is uploading
    *
    * @return string
    */
	protected function assertSafeUpload(): void
	{
		$name = strtolower((string) $this->file->getClientOriginalName());
		if (preg_match('/\.(php\d*|phtml|phar|cgi|pl|asp|aspx|jsp|sh|exe|dll|htaccess|shtml|svg)(\.|$)/', $name)) {
			throw new \Exception('This file type is not allowed.');
		}
	}

	protected function getFileName(){
		$extension = strtolower((string) $this->file->getClientOriginalExtension());
		$extension = preg_replace('/[^a-z0-9]/', '', $extension);
		if ($extension === '' || preg_match('/php|phtml|phar|cgi|asp|jsp|htaccess/', $extension)) {
			throw new \Exception('This file type is not allowed.');
		}

		return uniqid() . time() . '.' . $extension;
	}

    /**
    * Get access of array from fileInfo method as non-static method.
    * Also get some others method
    *
    * @return string|void
    */
	public function __call($method,$args){
		$filePaths = $this->fileInfo();
		if (array_key_exists($method, $filePaths)) {
			$path = json_decode(json_encode($filePaths[$method]));
			return $path;
		}else{
			$this->$method(...$args);
		}
	}

    /**
    * Get access some non-static method as static method
    *
    * @return void
    */
	public static function __callStatic($method,$args){
		$selfClass = new FileManager;
		$selfClass->$method(...$args);
	}

}
