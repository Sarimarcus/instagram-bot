<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateBotInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:update-bot-information';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get bot account information and update stats';

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
     * @return mixed
     */
    public function handle()
    {
        \App\Classes\InstagramBot::runTask('updateBotInformation');
    }
}
