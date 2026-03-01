public function dispatch(Request $request): Response {
    $path = $request->path();
    $method = $request->method();

    // Treat HEAD requests as GET
    if (strtoupper($method) === 'HEAD') {
        $method = 'GET';
    }

    if (isset($this->routes[$method][$path])) {
        return ($this->routes[$method][$path])($request);
    }

    // Handle 404 Not Found
    return new Response('404 Not Found', 404);
}