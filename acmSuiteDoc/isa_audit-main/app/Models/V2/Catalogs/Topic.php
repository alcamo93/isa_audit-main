<?php

namespace App\Models\V2\Catalogs;

use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\News\News;

class Topic extends Model
{
    use HasFactory, UtilitiesTrait;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
      // 'guidelines'
    ];

    /**
     * Get the news.
     */
    public function news()
    {
        return $this->belongsToMany(News::class);
    }

    /**
     * Get the guidelines.
     */
    public function guidelines()
    {
        return $this->belongsToMany(Guideline::class, 'guideline_topic', 'topic_id', 'guideline_id');
    }

  /**
   * get topics for relations with a guideline
   */
  public function scopeGetRelationsForGuideline($query, $idGuideline)
  {
    $filters = request('filters');

    $query->with(['guidelines' => function($query) use ($idGuideline) {
      $query->where('guideline_id', $idGuideline);
    }]);

    if(isset($filters['has_relation']) && boolval($filters['has_relation'])){
      $query->whereHas('guidelines', function($subquery) use ($idGuideline) {
        $subquery->where('guideline_id', $idGuideline);
      });
    } else if (isset($filters['has_relation']) && !boolval($filters['has_relation'])){
      $query->whereDoesntHave('guidelines', function($subquery) use ($idGuideline) {
        $subquery->where('guideline_id', $idGuideline);
      });
    }

    if (isset($filters['topic_name']) && !empty($filters['topic_name'])) {
      $query->where('name', 'like', '%' . $filters['topic_name'] . '%');
    }
  }
}
