<?php

namespace App\Support;

use App\Models\Endpoint;
use Illuminate\Support\Str;

class TwitchEndpoint
{
    /**
     * The Endpoint instance.
     *
     * @var Endpoint
     */
    protected Endpoint $endpoint;

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return [
            'name' => $this->endpoint->name,
            'slug' => $this->endpoint->slug,
            'route' => 'https://api.twitch.tv/helix'.$this->endpoint->route,
            'method' => $this->endpoint->method,
            'instruction' => $this->endpoint->instruction,
            'authorization' => $this->endpoint->authorization,
            'request_body' => $this->formatForOutput($this->endpoint->request_body),
            'request_query_parameters' => $this->formatForOutput($this->endpoint->request_query_parameters),
//            'response_body' => $this->formatForOutput($this->endpoint->response_body),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function formatForOutput(array $data): array
    {
        $items = data_get($data, 'items', []);

        return [
            'text' => data_get($data, 'text'),
            'fields' => $this->fields($items),
        ];
    }

    /**
     * @param array $items
     * @return array
     */
    protected function fields(array $items): array
    {
        $structured = [];
        foreach ($items as $name => $item) {
            if (!isset($item['required'])) {
                dd($item);
            }
            $structured[] = [
                'id' => trim($name),
                'required' => $item['required'],
                'instruction' => $item['instruction'],
                'type' => $item['type'],
                'arrayable' => str_contains($item['instruction'], 'parameter for each'),
                'attributes' => ['type' => $this->castField($item)],
                'children' => !empty($item['children']) ? $this->fields($item['children']) : null
            ];;
        }

        return $structured;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function castField(array $item): string
    {
        $type = Str::lower(trim($item['type']));

        return match ($type) {
            'object[]', 'map[string]string' => 'json',
            'int64', 'integer' => 'number',
            'boolean' => 'bool',
            default => 'string',
        };
    }
}
