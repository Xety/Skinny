<?php
namespace Skinny\Api\Services;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\GuzzleException;
use Skinny\Api\JsonStream;
use Skinny\Core\Configure;

/**
 * The trait used in any service to make a request and send back the response.
 *
 * @property \GuzzleHttp\Client $client The client used to make the request.
 */
trait ServicesTrait
{
    /**
     * The Client used to do the request.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Register a middleware response then initialize the Client.
     */
    public function __construct()
    {
        $handler = new HandlerStack();
        $handler->setHandler(new CurlHandler());

        // If the response is a json response, return a JsonStream.
        $handler->push(Middleware::mapResponse(function (ResponseInterface $response) {
            if (isset($response->getHeader('Content-Type')[0]) &&
                $response->getHeader('Content-Type')[0] == 'application/json') {
                $jsonStream = new JsonStream($response->getBody());

                return $response->withBody($jsonStream);
            } else {
                return $response->withStatus(404);
            }
        }));

        $this->client = new Client([
            'handler' => $handler,
            'base_uri' => Configure::read('API.url'),
            'http_errors' => true,
        ]);
    }

    /**
     * Build the request and call the API then return the response data.
     *
     * This method extracts the 'data' property from the response if it exists,
     * otherwise returns the full response object.
     *
     * @param string $method HTTP method used (GET, POST, PUT, DELETE, etc.).
     * @param string $uri URI used to call the API.
     * @param array $data Data to send in the request body.
     * @param array $headers Additional headers to include in the request.
     *
     * @return \stdClass|array|null The response data or null if empty.
     *
     * @throws \GuzzleHttp\Exception\ClientException For 4xx HTTP errors.
     * @throws \GuzzleHttp\Exception\ServerException For 5xx HTTP errors.
     * @throws \Exception For other errors.
     */
    public function build(string $method, string $uri, array $data = [], array $headers = [])
    {
        $options = [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . Configure::read('API.token'),
                'Accept' => 'application/json',
            ], $headers),
        ];

        if (!empty($data)) {
            $options['json'] = $data;
        }

        try {
            $response = $this->client->request($method, $uri, $options);
            /*$statusCode = $response->getStatusCode();

            // Check if response is an error even if no exception was thrown
            if ($statusCode >= 400) {
                throw new \Exception("HTTP {$statusCode} error from API");
            }*/

            $body = $response->getBody();

            // Check if the body is a JsonStream (has json() method)
            if ($body instanceof JsonStream) {
                $jsonData = $body->json();
            } else {
                // Fallback: decode JSON manually
                $content = (string) $body;
                $jsonData = json_decode($content);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Failed to decode JSON response: ' . json_last_error_msg());
                }
            }

            // Check if the decoded JSON contains an error property (API error response)
           /* if (is_object($jsonData) && property_exists($jsonData, 'error')) {
                throw new \Exception($jsonData->message ?? 'API returned an error');
            }*/

            // Extract data property if it exists, otherwise return full object
            if (is_object($jsonData) && property_exists($jsonData, 'data')) {
                return $jsonData->data;
            }

            // If it's an array, return as-is
            if (is_array($jsonData)) {
                return empty($jsonData) ? null : $jsonData;
            }

            // Return the full object
            return $jsonData;

        } catch (ClientException $e) {
            // Re-throw client errors (4xx) so services can handle them
            throw $e;
        } catch (ServerException $e) {
            // Re-throw server errors (5xx) so services can handle them
            throw $e;
        } catch (GuzzleException $e) {
            throw new \Exception('Service ' . get_class($this) . ' failed: ' . $e->getMessage());
        }

        /*if (is_array($datas) && empty($datas)) {
            return null;
        }*/

        debug($datas);

        //return $datas;
    }
}
