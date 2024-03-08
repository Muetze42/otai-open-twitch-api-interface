<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Api;
use App\Models\Endpoint;
use App\Models\EventSub;
use App\Models\Resource;
use App\Models\Scope;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use NormanHuth\Helpers\ConstantsInterface;
use NormanHuth\Helpers\Arr;
use NormanHuth\Helpers\Markdown\Table;

class UpdateGitRepo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-git-repo';

    /**
     * The Git commit message.
     *
     * @var string
     */
    protected string $commitMessage = 'Automatic update from production system';

    /**
     * @var string
     */
    protected string $gitRepositoryDirectory = 'otai-open-twitch-api-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the public GIT repository.';

    /**
     * @var bool
     */
    protected bool $changesDetected = false;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!Storage::directoryExists($this->gitRepositoryDirectory)) {
            Process::path(Storage::path(''))->run('git clone git@github.com:Muetze42/otai-open-twitch-api-interface.git');
        }

        Process::path(Storage::path($this->gitRepositoryDirectory))->run('git pull');

//        $this->createDataObjectFiles();
//        $this->createShortJsonFiles();
        $this->updateReadMe();

        $this->gitCommitPush();
    }

    protected function createShortJsonFiles(): void
    {
        $data = jsonPrettyEncode(
            EventSub::orderBy('name')
                ->pluck('version', 'name')
                ->toArray()
        );

        Storage::put($this->gitRepositoryDirectory . '/data/event-sub-versions.json', trim($data) . "\n");

        $items = [
            'event-sub' => EventSub::class,
            'endpoints' => Endpoint::class,
        ];

        foreach ($items as $file => $item) {
            $data = [];

            /* @var EventSub|Endpoint $item */
            $item::orderBy('name')->each(function (Model $model) use (&$data) {
                /* @var EventSub|Endpoint $model */
                $data[$model->name] = $model->scopes()->pluck('name');
            });

            Storage::put(
                $this->gitRepositoryDirectory . '/data/'.$file.'-scopes.json',
                trim(jsonPrettyEncode($data)) . "\n"
            );
        }
    }

    /**
     * @return void
     */
    protected function updateReadMe(): void
    {
        $files = ['composer.json', 'composer.lock', 'package.json'];
        $readmeFile = $this->gitRepositoryDirectory . '/README.md';

        foreach ($files as $file) {
            $oldContents = Storage::get($this->gitRepositoryDirectory . '/data/packages/' . $file);
            $contents = file_get_contents(base_path($file));

            if (!$oldContents || trim($oldContents) != trim($contents)) {
                Storage::put($this->gitRepositoryDirectory . '/data/packages/' . $file, trim($contents) . "\n");
            }
        }

        $oldContents = Storage::get($readmeFile);
        $contents = trim((string)Storage::get($this->gitRepositoryDirectory . '/templates/README.prepend.md'));

        for ($i = 1; $i <= 2; $i++) {
            $contents .= "\n\n";
            $word = $i == 1 ? 'Active' : 'Inactive';
            $contents .= '### ' . $word . ' Endpoints';
            $contents .= "\n\n";

            $endpoints = Endpoint::where('active', $i == 1)
                ->orderBy('name')
                ->get()
                ->map(function (Endpoint $endpoint) use ($i) {
                    $return = [
                        'name' => $endpoint->name,
                        'route' => '`' . $endpoint->route . '`',
                    ];

                    if ($i == 1) {
                        $return[] = implode('<br>', array_map(function ($item) {
                            return '`' . $item . '`';
                        }, $endpoint->scopes()->pluck('name')->toArray()));
                    }

                    return $return;
                })->toArray();

            $table = new Table();
            $table->addCell('Name');
            $table->addCell('Route');
            if ($i == 1) {
                $table->addCell('Scopes', Table::CELL_ALIGN_CENTER);
            }
            $table->addRows($endpoints);

            $contents .= $table->render();
        }

        $contents = trim($contents);
        $contents .= "\n\n";
        $contents .= trim((string)Storage::get($this->gitRepositoryDirectory . '/templates/README.append.md'));

        $contents = str_replace('{{year}}', now()->format('Y'), $contents);

        if (!$oldContents || trim($oldContents) != trim($contents)) {
            Storage::put($readmeFile, trim($contents) . "\n");
        }
    }

    /**
     * @return void
     */
    protected function createDataObjectFiles(): void
    {
//        $this->creatObjectFile(Activity::class);
        $this->creatObjectFile(Api::class);
        $this->createEndpointFile();
        $this->creatObjectFile(EventSub::class);
        $this->createResourceFile();
        $this->createScopeFile();
    }

    /**
     * @return void
     */
    protected function gitCommitPush(): void
    {
        $gitCommitPrefix = 'git -c user.name="' . config('git.commit.user.name') . '" -c user.email="' . config('git.commit.user.email') . '"';

        $gitCommands = [
            'add -A -f',
            'commit -m "' . $this->commitMessage . '"',
            'push'
        ];
        $gitCommands = array_map(function ($command) use ($gitCommitPrefix) {
            return $gitCommitPrefix . ' ' . $command;
        }, $gitCommands);

        $gitCommand = implode(' && ', $gitCommands);

        Process::path(Storage::path($this->gitRepositoryDirectory))->run($gitCommand);
    }

    /**
     * @return void
     */
    protected function createScopeFile(): void
    {
        $file = $this->gitRepositoryDirectory . '/data/objects/' . class_basename(Scope::class) . '.json';

        $contents = Scope::orderByDesc('id')
            ->get()
            ->map(function (Scope $scope) {
                return Arr::keyValueInsertToPosition(
                    $scope->toArray(),
                    'endpoints',
                    $scope->endpoints()->pluck('name'),
                    2
                );
            })
            ->toJson(ConstantsInterface::JSON_PRETTY_FLAGS);

        $this->handleFile($contents, $file);
    }

    /**
     * @return void
     */
    protected function createResourceFile(): void
    {
        $file = $this->gitRepositoryDirectory . '/data/objects/' . class_basename(Resource::class) . '.json';

        $contents = Resource::orderByDesc('id')
            ->get()
            ->map(function (Resource $resource) {
                $result = Arr::keyValueInsertToPosition(
                    $resource->toArray(),
                    'endpoints',
                    $resource->endpoints()->pluck('name'),
                    2
                );

                return Arr::keyValueInsertToPosition(
                    $result,
                    'api',
                    $resource->api->name,
                    1
                );
            })
            ->toJson(ConstantsInterface::JSON_PRETTY_FLAGS);

        $this->handleFile($contents, $file);
    }

    /**
     * @return void
     */
    protected function createEndpointFile(): void
    {
        $file = $this->gitRepositoryDirectory . '/data/objects/' . class_basename(Endpoint::class) . '.json';

        $contents = Endpoint::orderByDesc('id')
            ->get()
            ->map(function (Endpoint $endpoint) {
                return Arr::keyValueInsertToPosition(
                    $endpoint->toArray(),
                    'scopes',
                    $endpoint->scopes()->pluck('name'),
                    2
                );
            })
            ->toJson(ConstantsInterface::JSON_PRETTY_FLAGS);

        $this->handleFile($contents, $file);
    }

    /**
     * @param Model|string $model
     *
     * @return void
     */
    protected function creatObjectFile(Model|string $model): void
    {
        $file = $this->gitRepositoryDirectory . '/data/objects/' . class_basename($model) . '.json';

        $contents = app($model)::orderByDesc('id');

        if (in_array(SoftDeletes::class, class_uses($model))) {
            $contents = $contents->withTrashed();
        }

        $contents = $contents->get()
            ->toJson(ConstantsInterface::JSON_PRETTY_FLAGS);

        $this->handleFile($contents, $file);
    }

    /**
     * @param string $contents
     * @param string $file
     *
     * @return void
     */
    protected function handleFile(string $contents, string $file): void
    {
        if (!$this->changesDetected) {
            $oldContents = Storage::get($file);

            if (!$oldContents || trim($oldContents) != trim($contents)) {
                $this->changesDetected = true;
                Storage::put($file, trim($contents) . "\n");
            }
        }
    }
}
