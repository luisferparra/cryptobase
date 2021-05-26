<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefressAllAfterPull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptobase:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Execute after every Pull from Git Repository';

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
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        $this->call('optimize:clear');
        $this->call('config:cache');
        $this->call('route:cache');

        $this->call('view:cache');


        return 0;
    }
}
