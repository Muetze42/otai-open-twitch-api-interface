<?php

namespace App\Console\Commands\Scrap;

use App\Exceptions\TwitchScraperException;
use App\Services\TwitchApiReference;
use Illuminate\Console\Command;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class TwitchApiReferenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:twitch-api-reference';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap and update Twitch API Reference from `https://dev.twitch.tv/docs/api/reference/`.';

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
        $this->info('Start scraping Twitch API reference');

        TwitchApiReference::scrap();

        $this->info('Finished scraping Twitch API reference');
    }
}
