<?php

namespace App\Console\Commands\Scrap;

use App\Exceptions\TwitchScraperException;
use App\Services\TwitchScope;
use Illuminate\Console\Command;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class TwitchScopes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:twitch-scopes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap and update Twitch scopes from https://dev.twitch.tv/docs/authentication/scopes/.';

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
        $this->info('Start scraping Twitch scopes');

        TwitchScope::scrap();

        $this->info('Finished scraping Twitch scopes');
    }
}
