<?php

namespace MarketDragon\LivewireExtra\Commands;

use Illuminate\Console\Command;
use MarketDragon\LivewireExtra\LivewireComponentsFinder;

class DiscoverLivewire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketdragon:livewire_discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LivewireComponentsFinder $finder)
    {

        $finder->build();

        $this->info('Livewire auto-discovery manifest rebuilt!');
        return 0;
    }
}
