<?php

namespace App\Middleware;

use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class CORS implements \CodeIgniter\HTTP\MiddlewareInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(RequestInterface $request, \Closure $next)
    {
        $response = Services::response();

        // Allow all origins
        $response->setHeader('Access-Control-Allow-Origin', '*');

        // Allow the following methods
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        // Allow the following headers
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Requested-With, Authorization');

        // Allow cookies to be sent across domains
        $response->setHeader('Access-Control-Allow-Credentials', 'true');

        // If this is a preflight request, return the response with no content
        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }

        // Call the next middleware
        $response = $next($request, $response);

        return $response;
    }
}
