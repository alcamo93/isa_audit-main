<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\V2\Audit\Library;
use App\Traits\V2\UtilitiesTrait;

class File extends Model
{
	use UtilitiesTrait;

  protected $table = 'files';

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
		'is_current',
		'renewal_number',
    'load_date',
    'drop_date',
		'init_date',
    'end_date',
		'library_id'
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 
  ];

  /**
	 * Attributes 
	 */
	protected $appends = [
    'load_date_format',
		'drop_date_format',
		'init_date_format',
		'end_date_format',
		'full_path',
		'file_size_human',
		'directory_path',
		'name_download',
		'is_current_text',
	];

  /*
	* Get load_date format
	*/
	public function getLoadDateFormatAttribute()
	{
		return $this->getFormatDate($this->load_date);
	}

	/*
	* Get drop_date format
	*/
	public function getDropDateFormatAttribute()
	{
		return $this->getFormatDate($this->drop_date);
	}

	/*
	* Get init_date format
	*/
	public function getInitDateFormatAttribute()
	{
		return $this->getFormatDate($this->init_date);
	}

	/*
	* Get end_date format
	*/
	public function getEndDateFormatAttribute()
	{
		return $this->getFormatDate($this->end_date);
	}

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

	/**
	 * Get text current for download
	 */
	public function getIsCurrentTextAttribute()
	{
		$dates = "{$this->init_date_format} - {$this->end_date_format}";
		return boolval($this->is_current) ? '*' : "Histórico: {$dates}";
	}

  /**
	 * Get the Library that owns the File
	 */
	public function library()
	{
		return $this->belongsTo(Library::class);
	}

	/**
	 * Sort custom for fields
	 */
	public function sortCustom($query)
	{
		$query->sortBy([ 
			['id_requirement', 'asc'], 
			['id_subrequirement', 'asc'] 
		]);
	}
}