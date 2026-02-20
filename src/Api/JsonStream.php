<?php
namespace Skinny\Api;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class JsonStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /**
     * @var StreamInterface The underlying stream
     */
    private $stream;

    /**
     * Convert a json response to an object class.
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function json()
    {
        $contents = (string) $this->getContents();

        if ($contents === '') {
            return null;
        }

        $decodedContents = json_decode($contents);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Error trying to decode response: ' . json_last_error_msg());
        }

        return $decodedContents;
    }
}
