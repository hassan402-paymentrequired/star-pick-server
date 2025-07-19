<?php

namespace App\Utils\Traits;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

trait ResponseTrait
{

    /**
     * The current path of resource to respond
     *
     * @var string
     */
    protected string $resourceItem;

    /**
     * The current path of collection resource to respond
     *
     * @var string
     */
    protected string $resourceCollection;

    /**
     *
     * @param $data
     * @param $status
     * @return JsonResponse
     */
    protected function respondWithCustomData($data, $status = 200): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], $status);
    }

    /**
     *
     * @param $data
     * @param $status
     * @return JsonResponse
     */
    protected function respondWithProxyData($data, $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    protected function getTimestampInMilliseconds(): int
    {
        return intdiv((int)now()->format('Uu'), 1000);
    }

    /**
     *
     * Return no content for delete requests
     */
    protected function respondWithNoContent(): JsonResponse
    {
        return new JsonResponse([
            'data' => null,
            'message' => 'No Content',
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     *
     * Return collection response from the application
     */
    protected function respondWithCollection(LengthAwarePaginator|CursorPaginator|Collection $collection)
    {
        return (new $this->resourceCollection($collection))->additional(
            ['meta' => ['timestamp' => $this->getTimestampInMilliseconds()]]
        );
    }

    /**
     *
     * Return single item response from the application
     */
    protected function respondWithItem(Model|array $item, $additionalData = []): mixed
    {
        return (new $this->resourceItem($item))->additional(
            [...$additionalData, 'meta' => ['timestamp' => $this->getTimestampInMilliseconds()]]
        );
    }



    /**
     * @param string $error
     * @return JsonResponse
     */
    protected function responseWithCustomError(string $error, $status): JsonResponse
    {
        return new JsonResponse([
            'error' => $error,
            'message' => 'failed',
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], $status);
    }


    protected function responseWithSuccessMessage(string $msg, int $status): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $msg,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], $status);
    }

    protected function responseWithError(string $message,  $error, int|string $code = 200): JsonResponse
    {
        $response = [
            'response_message' => $message,
            'response_code' => $code,
            'errors' => $error,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ];
        return new JsonResponse($response, $code);
    }

    protected function responseWithErrorMessage(string $message,  $error, int|string $code = 200): JsonResponse
    {
        $response = [
            'response_message' => $message,
            'response_code' => $code,
            'error' => $error,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ];
        return new JsonResponse($response, $code);
    }
}
