<?php


namespace Lune\Http\Emitter;

use Psr\Http\Message\ResponseInterface;

class ResponseEmitter extends EmitterAbstract
{
    public function emit(ResponseInterface $response)
    {
        if (headers_sent()) {
            //Headers are already sent
            throw new HttpRuntimeException("Cannot emit: headers already sent.");
        }

        //Make sure the response has it's content length set
        if (!$response->hasHeader('Content-Length')) {
            //add the Content-Length if necessary
            $response = $this->withContentLengthHeader($response);
        }

        //Start emitting
        //Emit the HTTP status line
        $this->sendStatus($response);

        //Emit the HTTP headers
        $this->sendHttpHeaders($response);

        //Emit the body
        $this->sendBody($response);
    }

    /**
     * Sends the body of the response
     *
     * @param ResponseInterface $response
     */
    private function sendBody(ResponseInterface $response)
    {
        echo (string) $response->getBody();
    }
}