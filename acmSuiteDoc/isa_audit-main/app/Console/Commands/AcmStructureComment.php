<?php

namespace App\Console\Commands;

use App\Models\V2\Audit\Comment;
use App\Models\V2\Audit\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AcmStructureComment extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'acm-suite:structure-comments';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'ACM Suite - Migrate Structure comments';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    DB::beginTransaction();

    try {
      $comments = Comment::whereNotNull('id_task')->get();
      foreach ($comments as $comment) {
        $commentableId = $comment->id_task;
        $commentableType = Task::class;
        $comment->update([
          'commentable_id' => $commentableId, 
          'commentable_type' => $commentableType,
          'id_task' => null
        ]);
      }

      DB::commit();
      $this->info('Migración polimorfica completada correctamente.');
    } catch (\Exception $e) {
      DB::rollBack();
      $this->error('Error durante la migración: ' . $e->getMessage());
    }
  }
}
