<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use App\Containers\Common\Models\Data;
use App\Containers\Common\Models\Contact;
use App\Containers\Files\Models\Image;
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

        $errorFlag = false;

        DB::beginTransaction();
        try {
            $this->deleteSoftDeletedImages($dateBeforeOneMonth); // delete soft deleted images before one month
            $this->deleteSoftDeletedUsers($dateBeforeTwoMonth); // delete soft deleted users before two months
            $this->deleteSoftDeletedContacts($dateBeforeTwoMonth); // delete soft deleted contacts before two months
            $this->deleteSoftDeletedData($dateBeforeThreeMonth); // delete soft deleted data before three months
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
     * This function deletes soft deleted images before specific date
     * 
     * @param Carbon $date
     * @return void
     */
    private function deleteSoftDeletedImages(Carbon $date): void
    {
        Image::onlyTrashed()->where('deleted_at', '<=', $date)->forcedelete();
    }

    /**
     * This function deletes soft deleted users before specific date
     * 
     * @param Carbon $date
     * @return void
     */
    private function deleteSoftDeletedUsers(Carbon $date): void
    {
        User::onlyTrashed()->where('deleted_at', '<=', $date)->forcedelete();
    }

    /**
     * This function deletes soft deleted data before specific date
     * Model is: App\Containers\Common\Models\Data
     * 
     * @param Carbon $date
     * @return void
     */
    private function deleteSoftDeletedData(Carbon $date): void
    {
        Data::onlyTrashed()->where('deleted_at', '<=', $date)->forcedelete();
    }

    /**
     * This function deletes soft deleted contacts before specific date
     * 
     * @param Carbon $date
     * @return void
     */
    private function deleteSoftDeletedContacts(Carbon $date): void
    {
        Contact::onlyTrashed()->where('deleted_at', '<=', $date)->forcedelete();
    }
}
