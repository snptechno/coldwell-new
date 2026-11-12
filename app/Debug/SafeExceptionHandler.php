<?php

namespace App\Debug;

use CodeIgniter\Debug\ExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Exceptions as ExceptionsConfig;
use Throwable;

/**
 * Wraps CI4's (final) ExceptionHandler.
 *
 * For non-HTML requests (curl, bots, AJAX) CI4 returns the full trace as JSON,
 * including argument values. Those include the Request/Router objects, whose
 * $_SERVER copy holds every .env value (encryption key, DB/SMTP passwords), and
 * Config\Exceptions::$sensitiveDataInTrace can only mask two levels deep.
 *
 * Here non-HTML errors get a compact JSON body: the trace keeps file/line/function
 * but never argument values. Browser (HTML) errors go to CI4's normal handler,
 * whose error_exception.php view was changed to print objects/arrays by type only.
 */
final class SafeExceptionHandler implements ExceptionHandlerInterface
{
    public function __construct(private readonly ExceptionsConfig $config)
    {
    }

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode,
    ): void {
        if ($request instanceof IncomingRequest
            && ! str_contains($request->getHeaderLine('accept'), 'text/html')) {
            try {
                $response->setStatusCode($statusCode);
            } catch (HTTPException) {
                $statusCode = 500;
                $response->setStatusCode($statusCode);
            }

            $body = ['status' => $statusCode, 'error' => $response->getReasonPhrase()];

            // Details only where errors are displayed (never in production).
            if (in_array(strtolower((string) ini_get('display_errors')), ['1', 'true', 'on', 'yes', 'stdout'], true)) {
                $body['type']    = $exception::class;
                $body['message'] = $exception->getMessage();
                $body['file']    = $exception->getFile();
                $body['line']    = $exception->getLine();
                $body['trace']   = array_map(
                    static fn (array $f): array => array_intersect_key($f, array_flip(['file', 'line', 'class', 'type', 'function'])),
                    $exception->getTrace(),
                );
            }

            $response->setJSON($body)->send();

            if (ENVIRONMENT !== 'testing') {
                exit($exitCode); // @codeCoverageIgnore
            }

            return;
        }

        (new ExceptionHandler($this->config))->handle($exception, $request, $response, $statusCode, $exitCode);
    }
}
