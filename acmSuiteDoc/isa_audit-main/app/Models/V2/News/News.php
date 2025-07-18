<?php

namespace App\Models\V2\News;

use App\Models\V2\Admin\Image;
use App\Traits\V2\RichTextTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;
use App\Models\V2\Catalogs\Topic;
use Carbon\Carbon;

class News extends Model
{
  use HasFactory, UtilitiesTrait, RichTextTrait;

  /*
    * The attributes that are mass assignable.
    *
    * @var array
    */
  protected $fillable = [
    'headline',
    'description',
    'publication_start_date',
    'publication_closing_date',
    'historical_start_date',
    'historical_closing_date',
    'published'
  ];

  /**
   * Attributes 
   */
  protected $appends = [
    'publication_start_date_format',
    'publication_closing_date_format',
    'historical_start_date_format',
    'historical_closing_date_format',
    'publication_start_date_format_text',
    'description_env',
    'date_in_range',
  ];

  /*
	 * Get start date publication format
	 */
  public function getPublicationStartDateFormatAttribute()
  {
    return $this->getFormatDate($this->publication_start_date);
  }

  /*
	 * Get closing date publication format
	 */
  public function getPublicationClosingDateFormatAttribute()
  {
    return $this->getFormatDate($this->publication_closing_date);
  }

  /*
	 * Get start date historical format
	 */
  public function getHistoricalStartDateFormatAttribute()
  {
    return $this->getFormatDate($this->historical_start_date);
  }

  /*
	 * Get closing date historical format
	 */
  public function getHistoricalClosingDateFormatAttribute()
  {
    return $this->getFormatDate($this->historical_closing_date);
  }

  /*
	 * Get start date publication format
	 */
  public function getPublicationStartDateFormatTextAttribute()
  {
    return $this->getFormatDateText($this->publication_start_date);
  }

  /**
   * get DescriptionEnv for enviroment
   */
  public function getDescriptionEnvAttribute() 
  {
    return $this->setUrlInRichText($this->description);
  }

  /**
   * get date range news
   */
  public function getDateInRangeAttribute() 
  {
    if ($this->newDateInRange($this->publication_start_date, $this->publication_closing_date)) {
      return 'Fecha vigente';
    }

    if ($this->newDateInRangeHistory($this->historical_start_date, $this->historical_closing_date)) {
      return 'En historial';
    }

    return 'Fuera de fecha';
  }

  /**
   * Get the new's image.
   */
  public function image()
  {
    return $this->morphOne(Image::class, 'imageable');
  }

  /**
   * Get the topics.
   */
  public function topics()
  {
    return $this->belongsToMany(Topic::class);
  }

  /**
	 * filter by options
	 */
	public function scopeCustomFilters($query)
	{
    $timezone = Config('enviroment.time_zone_carbon');
    $currentDate = Carbon::now($timezone);
    $currentDate = $currentDate->startOfDay();

		$query->orderBy('id', 'DESC');

		$filters = request('filters');
		if ( empty($filters) ) return;
    
    if ( isset($filters['headline']) ) {
			$query->where('headline', 'LIKE', "%{$filters['headline']}%");
		}

    if ( isset($filters['description']) ) {
			$query->where('description', 'LIKE', "%{$filters['description']}%");
		}

    if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'PUBLISHED' ) {
			$query->where('published', 1);
		}

    if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'ALL' ) {
			return;
		}

    if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'CURRENT' ) {
			$query->where('publication_start_date', '<=', $currentDate)->where('publication_closing_date', '>=', $currentDate);
		}

		if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'HISTORY' ) {
			$query->where('historical_start_date', '<=', $currentDate)->where('historical_closing_date', '>=', $currentDate);
		}

    if ( isset($filters['custom_filter']) && $filters['custom_filter'] == 'OUT_RANGE' ) {
			$query->where('publication_start_date', '>', $currentDate)->orWhereDate('historical_closing_date', '<', $currentDate);
		}

	}

  public function scopeBanner($query)
  {
    $banner = request('filters.banner');
    $banner == 'true' ? $query->where('published', 1) : '';
  }

}
