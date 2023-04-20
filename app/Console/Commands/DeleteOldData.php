<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use App\Containers\Common\Models\Data;
use App\Containers\Common\Models\Contact;
use App\Containers\Files\Models\Image;
use App\Containers\Currencies\Models\CurrencyConversionHistory;
use App\Models\User;
use Exception;

class DeleteOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:olds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command force deletes soft deleted data and some prefixed other data before certain prefixed dates.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dateBeforeOneMonth = $this->subtractDays(30);
        $dateBeforeTwoMonth = $this->subtractDays(60);
        $dateBeforeThreeMonth = $this->subtractDays(90);
        $dateBeforeFourMonth = $this->subtractDays(120);
        $dateBeforeFiveMonth = $this->subtractDays(150);

        $errorFlag = false;

        DB::beginTransaction();
        try {
            
            $this->deleteSoftDeletedModel(Image::class, $dateBeforeOneMonth); // delete soft deleted images before one month
            $this->deleteSoftDeletedModel(User::class, $dateBeforeTwoMonth); // delete soft deleted users before two months
            $this->deleteSoftDeletedModel(Contact::class, $dateBeforeTwoMonth); // delete soft deleted contacts before two months
            $this->deleteSoftDeletedModel(Data::class, $dateBeforeThreeMonth); // delete soft deleted data before three months
            $this->deleteSoftDeletedModel(CurrencyConversionHistory::class, $dateBeforeFiveMonth); // delete soft deleted conversions before 5 months
            
            DB::commit();
        } catch (Exception $e) {
            $errorFlag = true;
            DB::rollBack();
        }
        
        if(!$errorFlag) {
            $this->info('Successfully deleted old data.');
            return Command::SUCCESS;
        } else {
            $this->info('Data delete failed.');
            return Command::FAILURE;
        }
    }

    /**
     * This function subtracts days from the current date
     * and returns the date
     * 
     * @param int $days
     * @return Carbon $output
     */
    private function subtractDays(int $days): Carbon
    {
        $now = Carbon::now();

        $output = $now->subDays($days);

        return $output;
    }

    /**
     * This function deletes soft deleted Model before specific date
     * 
     * @param Model
     * @param Carbon $date
     * @return void
     */
    private function deleteSoftDeletedModel($model, Carbon $date): void
    {
        $model::onlyTrashed()->where('deleted_at', '<=', $date)->forcedelete();
    }
}
