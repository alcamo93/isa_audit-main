<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Catalogues\BasisModel;
use App\Classes\ImageDecodeToFile;
use App\Classes\StatusConstants;

class DecodeImageBase64 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $idLegalBasis, $legalQuote;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($idLegalBasis, $legalQuote)
    {
        $this->idLegalBasis = $idLegalBasis;
        $this->legalQuote = $legalQuote;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $storeDisk = 'legals';
        $decodeImgObject = new ImageDecodeToFile($storeDisk, $this->idLegalBasis, false);
        $decodeImg = $decodeImgObject->decodeImg64ToLink($this->legalQuote);
        if (!$decodeImgObject->allCreate) {
            dump('warning: Cancelado, No se pudo crear archivos en: '. $this->idLegalBasis);
        }
        DB::beginTransaction();
        $updateQuote = BasisModel::UpdateQuote($this->idLegalBasis, $decodeImgObject->richText);
        if ($updateQuote['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            dump('error: '. $this->idLegalBasis);
        } else {
            DB::commit();
            dump('success: '. $this->idLegalBasis);
        }
    }
}
