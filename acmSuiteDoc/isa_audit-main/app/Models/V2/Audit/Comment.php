<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Task;
use App\Traits\V2\UtilitiesTrait;

class Comment extends Model
{
	use UtilitiesTrait;

	protected $table = 't_comments';
	protected $primaryKey = 'id_comment';

  /**
   * Get the parent commentable model (Task or EvaluateApplicabilityQuestion).
   */
  public function commentable()
  {
    return $this->morphTo();
  }

	/**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'user'
  ];

	/*
  * The attributes that are mass assignable.
  *
  * @var array
  */
	protected $fillable = [
		'id_comment',
		'comment',
		'id_user',
		'id_task',
    'commentable_id',
		'commentable_type',
	];

  /**
	 * Attributes 
	 */
	protected $appends = [
		'created_at_format',
		'updated_at_format',
	];

	/**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    // 
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'comment' => ['field' => 'comment', 'type' => 'string', 'relation' => null],
  ];

  /*
	* Get created_at format
	*/
	public function getCreatedAtFormatAttribute()
	{
    return $this->getFormatDate($this->created_at);
	}

  /* 
	* Get updated_at format
	*/
	public function getUpdatedAtFormatAttribute()
	{
    return $this->getFormatDate($this->updated_at);
	}

	/**
	 * Get the user associated with the Comment
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

	/**
	 * Get the task that owns the Comment
	 */
	public function task()
	{
		return $this->belongsTo(Task::class, 'id_task', 'id_task');
	}

  public function scopeCommentData($query)
  {
    $query->with('user.person')->orderBy('created_at', 'DESC');
  }
}