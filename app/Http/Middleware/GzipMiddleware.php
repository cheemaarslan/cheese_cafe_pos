<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GzipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Make sure it's a normal response (not a binary file download, etc.)
        if (in_array('gzip', $request->getEncodings()) && function_exists('gzencode')) {
            $contentType = $response->headers->get('Content-Type');
            
            // Only compress HTML/JSON/Text responses
            if ($contentType && (strpos($contentType, 'text/html') !== false || strpos($contentType, 'application/json') !== false || strpos($contentType, 'text/plain') !== false)) {
                $content = $response->getContent();
                if (!empty($content)) {
                    $compressed = gzencode($content, 9);
                    $response->setContent($compressed);
                    $response->headers->add([
                        'Content-Encoding' => 'gzip',
                        'Vary' => 'Accept-Encoding',
                        'Content-Length' => strlen($compressed),
                    ]);
                }
            }
        }

        return $response;
    }
}
