<?php

namespace App\Http\Controllers;

use App\Exceptions\TwitchApiException;
use App\Http\Clients\TwitchHelix;
use App\Models\Endpoint;
use App\Models\Request as RequestModel;
use App\Support\TwitchEndpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;

class RequestController extends Controller
{
    /**
     * Get a subset of the Requests model's attributes.
     *
     * @var array
     */
    protected array $requestAttributes = [
        'id',
        'url',
        'notice',
        'request_body',
        'response_body',
        'response_code',
        'status_text',
    ];

    /**
     * @var array
     */
    protected array $requestBody = [];

    /**
     * @var array
     */
    protected array $requestQueryParameters = [];

    /**
     * @var array
     */
    protected array $arrayable = [];

    /**
     * @var array
     */
    protected array $requires = [];

//    protected int $broadcasterId;

    /**
     * @param RequestModel $request
     *
     * @return array
     */
    public function show(RequestModel $request)
    {
        return $request->only($this->requestAttributes);
    }

    public function delete(Request $request, RequestModel $model)
    {
        if ($request->user()->getKey() != $model->user_id) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $model->delete();
    }

    /**
     * @param Request  $request
     * @param Endpoint $endpoint
     *
     * @return array|JsonResponse
     */
    public function store(Request $request, Endpoint $endpoint)
    {
        $user = $request->user();

        $rules = $this->getRules($endpoint, $request);

        $request->validate($rules);

        $api = new TwitchHelix($endpoint);
        $requestBody = $this->getRequestBody($request);
        $requestQueryString = $this->getRequestQueryString($request);

        try {
            return $api->userRequest($user, $requestBody, $requestQueryString)
                ->only($this->requestAttributes);
        } catch (TwitchApiException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_FAILED_DEPENDENCY);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getRequestBody(Request $request): array
    {
        $body = [];
        $fields = $request->input(['fields']);
        foreach ($this->requestBody as $key) {
            $value = data_get($fields, $key);

            if (is_null($value) ||
                (!in_array($key, $this->requires) && !data_get($request->input('useFields'), $key))) {
                continue;
            }
            $body[$key] = $value;
        }

        return $body;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getRequestQueryString(Request $request): string
    {
        $queryString = '';
        $fields = $request->input(['fields']);

        foreach ($this->requestQueryParameters as $parameter) {
            $value = data_get($fields, $parameter);

            if (is_null($value) ||
                (!in_array($parameter, $this->requires) && !data_get($request->input('useFields'), $parameter))) {
                continue;
            }
            if ($queryString) {
                $queryString.= '&';
            }
            if (in_array($parameter, $this->arrayable)) {
                $value = explode(',', $value);
                $value = array_map('trim', $value);
                $queryString.= $parameter.'=';
                $queryString.= implode('&'.$parameter.'=', $value);

                continue;
            }

            if (is_bool($value)) {
                $value = (int) $value;
            }

            $queryString.= $parameter.'='.$value;
        }

        return $queryString;
    }

    /**
     * @param Endpoint $endpoint
     * @param Request  $request
     * @param array    $rules
     *
     * @return array
     */
    protected function getRules(Endpoint $endpoint, Request $request, array $rules = []): array
    {
        $formatted = (new TwitchEndpoint($endpoint))->getOutput();
        $sections = ['request_body', 'request_query_parameters'];

        foreach ($sections as $section) {
            $items = data_get($formatted, $section . '.fields', []);
            foreach ($items as $item) {
                $section == 'request_body' ? $this->requestBody[] = $item['id'] :
                    $this->requestQueryParameters[] = $item['id'];

                if ($item['arrayable']) {
                    $this->arrayable[] = $item['id'];
                }

                if ($item['required']) {
                    $rules['fields.' . $item['id']][] = 'required';
                    $this->requires[] = $item['id'];
                } else {
                    $rules['fields.' . $item['id']][] = 'nullable';
                }

                $type = $item['attributes']['type'];
                //if ($type != 'string' || !is_numeric(data_get($request->input('fields'), $item['id']))) {
                if ($type != 'string') {
                    $rules['fields.' . $item['id']][] = str_replace('number', 'int', $item['attributes']['type']);
                }
            }
        }

        return $rules;
    }

    public function index(Request $request, Endpoint $endpoint)
    {
        return $endpoint
            ->requests()
            ->where('user_id', $request->user()->getKey())
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString()
            ->through(fn(RequestModel $requestModel) => [
                'id' => $requestModel->getKey(),
                'notice' => $requestModel->notice,
                'response_code' => $requestModel->response_code,
                'status_text' => $requestModel->status_text,
                'created_at' => $requestModel->created_at->toDateTimeString(),
            ]);
    }
}
