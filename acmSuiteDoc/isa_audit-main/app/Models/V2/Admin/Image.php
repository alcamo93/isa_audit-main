<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Image extends Model
{
  protected $table = 'images';
  
  /**
   * Get the parent imageable model (Customers, Corporates, Users, Audit and Backup).
   */
  public function imageable()
  {
    return $this->morphTo();
  }

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
		'original_name',
		'hash_name',
		'store_version',
		'store_origin',
		'directory',
		'extension',
    'file_type',
		'file_size',
		'usage',
		'imageable_id',
		'imageable_type',
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'full_path',
		'file_size_human',
		'directory_path',
		'name_download',
	];

  /*
	* Get Full Path
	*/
	public function getFullPathAttribute()
	{
		$productionStorage = Config('enviroment.production_storage');
		$imageProductionInDev = Config('enviroment.view_image_production_in_dev');
		$domain = Config('enviroment.aws_url_view');
		$isProduction = $productionStorage || $imageProductionInDev;
		$path = "{$this->store_version}/{$this->store_origin}/{$this->directory}/{$this->hash_name}";
		$fullPath = $isProduction ? "{$domain}/{$path}" : url($path);
		return $fullPath;
	}

	/*
	* Get Directory Path
	*/
	public function getDirectoryPathAttribute()
	{
		$path = "{$this->directory}/{$this->hash_name}";
		return $path;
	}

	/**
	 * Get size for human in MB
	 */
	public function getFileSizeHumanAttribute()
	{
		$size = $this->file_size / (1024 * 1024);
		$round = round($size, 2);
		return "{$round} MB";
	}

	/**
	 * Get name for download
	 */
	public function getNameDownloadAttribute()
	{
		return Str::of( $this->original_name )->replace(' ', '_')->replace('\\', '-')->replace('/', '-');
	}
}