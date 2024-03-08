<?php

namespace App\Console\Commands\Scrap;

use App\Exceptions\TwitchScraperException;
use App\Services\TwitchEventSub;
use Illuminate\Console\Command;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class TwitchEventSubsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:twitch-event-subs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap and update Twitch API EventSub\'s.';

    /**
     * Execute the console command.
     *
     * @throws CurlException
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     * @throws TwitchScraperException
     */
    public function handle()
    {
        $this->info('Start scraping Twitch EventSub\'s');

        TwitchEventSub::scrap();

        $this->info('Finished scraping Twitch EventSub\'s');
    }
}
