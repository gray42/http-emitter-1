<?php


namespace Lune\Http\Emitter;

use Psr\Http\Message\ResponseInterface;

abstract class EmitterAbstract
{

    /**
     * Helper function: converts header names to wordcase
     *
     * @param string $name
     * @return string
     */
    private function toWordCase(string $name):string
    {
        $uc = ucwords(str_replace('-', ' ', $name));
        return str_replace(' ', '-', $uc);
    }

    /**
     * Makes sure the response has it's content length set
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withContentLengthHeader(ResponseInterface $response):ResponseInterface
    {
        $size = $response->getBody()->getSize();
        if (!is_null($size)) {
            $response = $response->withHeader('Content-Length', (string)$size);
        }
        return $response;
    }


    /**
     * Sends the HTTP status line
     *
     * @param ResponseInterface $response
     */

    protected function sendStatus(ResponseInterface $response)
    {
        $status = vsprintf(
            'HTTP/%s %d%s', [
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            rtrim(' ' . $response->getReasonPhrase())
        ]);

        header($status);
    }

    /**
     * Loops through all headers and sends them
     *
     * @param ResponseInterface $response
     */
    protected function sendHttpHeaders(ResponseInterface $response)
    {
        $headers = $response->getHeaders();

        array_map(function ($name) use ($headers) {
            $this->sendHttpHeader(
                $name,
                $headers[$name]
            );
        }, array_keys($headers));
    }

    /**
     * Sends a single HTTP header
     *
     * @param string $name
     * @param array $values
     */

    private function sendHttpHeader(string $name, array $values)
    {
        array_map(function ($index) use ($name, $values) {
            $header = vsprintf(
                '%s: %s', [
                $this->toWordCase($name),
                $values[$index]
            ]);
            $replace = $index === 0;
            header($header, $replace);
        }, array_keys($values));
    }
}