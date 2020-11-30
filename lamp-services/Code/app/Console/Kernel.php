<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
                            Commands\Inspire::class,
                            Commands\TestEmail::class,
                            Commands\MailConsole::class,
                            Commands\TaxmailConsole::class,
                            
                            // RAVI
                            // =======================================================
                            'App\Console\Commands\Reports',
                            'App\Console\Commands\DailySummaryReports',
                            'App\Console\Commands\DataMismatchReports',
                            'App\Console\Commands\FfReport',
                            'App\Console\Commands\AttendanceReport',
                            'App\Console\Commands\FfReportComplete',
                            'App\Console\Commands\InvDataMismatchReports',
                            'App\Console\Commands\DataAnalysisReports',
                            'App\Console\Commands\AllPIMDownloadReportConsole',
                            
                            
                            // =======================================================
                            // SANDEEP
                            // =======================================================
                            'App\Console\Commands\InventoryReportMail',
                            'App\Console\Commands\SONotify',
							'App\Console\Commands\MessageQueuesConsole',
                            
                            // =======================================================
                            //ROHIT
                            // =======================================================
                            'App\Console\Commands\CopyInventoryConsole',
                            'App\Console\Commands\DmsReportMailConsole',
                            'App\Console\Commands\DiMiCiReportConsole',
                            'App\Console\Commands\InventoryReportConsole',
                            'App\Console\Commands\CreateReplenishmentsConsole',
                            'App\Console\Commands\DailyInventoryReport',
                            'App\Console\Commands\CrateUtilizationReport',
                            'App\Console\Commands\OutwardSupplyReportConsole',
                            'App\Console\Commands\HsnOutwardSupplyReportConsole',
                            'App\Console\Commands\InventoryFinalApprovalMail',
                            'App\Console\Commands\EmpAttendanceConsole',
                            'App\Console\Commands\EmpDetailsConsole',
                            'App\Console\Commands\DeviceAttendanceConsole',
                            'App\Console\Commands\GetDevicesConsole',
                            'App\Console\Commands\LeaveApprovalsNotificationConsole',
                            
                            
                            // SOMNATH 
                            // =======================================================
                            'App\Console\Commands\tallyPushLedger',
                            'App\Console\Commands\tallyPushVoucher',
                            'App\Console\Commands\tallyFetchLedger',
                            'App\Console\Commands\pricingupdate',
                            'App\Console\Commands\tallyGenerateTallyVSEPReport',
                            
                            // =======================================================
                            //CHANDRA
                            // =======================================================
                            'App\Console\Commands\queuetestConsole',
                            'App\Console\Commands\ResqueConsole',
                            'App\Console\Commands\ResqueJob',
                            'App\Console\Commands\DmapiConsole',
                            'App\Console\Commands\TestEmail',
                            'App\Console\Commands\TaxmailConsole',
                            'App\Console\Commands\CacheWarmFixedTables',
                            'App\Console\Commands\returnsVoucherPopulate',
                            'App\Console\Commands\returnVoucherTestPopulate',
                            'App\Console\Commands\DmapiVer2Console',
                            'App\Console\Commands\updateGdsTax',
                            'App\Console\Commands\updateTaxClass',
                            'App\Console\Commands\NotificationConsole',
                            'App\Console\Commands\InventoryLogConsole',
                            'App\Console\Commands\fixReturnVouchers',
                            'App\Console\Commands\RoutingDataCron',
                            // =======================================================
                            // AVINASH
                            // =======================================================
                            'App\Console\Commands\UpdateSalesVouchers',
                            'App\Console\Commands\UpdateResquetaskConsole',
                            'App\Console\Commands\PutawayListUpdatesTrigger',
                            'App\Console\Commands\MailUtilityConsole',
                            'App\Console\Commands\AutoEmail',
                            'App\Console\Commands\autoAssignPickerChecker',
                            'App\Console\Commands\autoSavePicklist',
                            'App\Console\Commands\RolesUpdateSave',
                            'App\Console\Commands\RetailerSMS',

                            // =======================================================
                            // RASHEED
                            // =======================================================
                            'App\Console\Commands\autosmsnotify',
                            'App\Console\Commands\autocpenalble',
                            'App\Console\Commands\birthdayTemplate',
                            'App\Console\Commands\clearcache',
                            'App\Console\Commands\oneyearTemplate',
                            'App\Console\Commands\newJoinUsersEmailTemplate',

                            // =======================================================
                            // PAVAN
                            // =======================================================
                            'App\Console\Commands\FailOrderV1Console',
                             'App\Console\Commands\RetailersQuarterlyReport',
                             'App\Console\Commands\AutoEmailProcedure',


                             //All cache flush 
                             'App\Console\Commands\flushAllDcCache',
                             'App\Console\Commands\flushAllCacheProducts',
                             'App\Console\Commands\flushAllCache',
                             'App\Console\Commands\flushAllCacheDcProducts',
                             'App\Console\Commands\flushAllCacheCustomerProducts',
                             'App\Console\Commands\flushAllDcCustomerCache',
                             


    ];  

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //$schedule->command('CopyInventory')
                 //->dailyAt('23:00');
    }
}
