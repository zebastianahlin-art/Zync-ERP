<?php

namespace Core;

class Router {
    // Existing Router implementation...

    public function dispatch(Request $request) {
        // Check if the request method is HEAD
        if (strtoupper($request->method) === 'HEAD') {
            $request->method = 'GET'; // Treat HEAD as GET for routing
        }
        
        // Existing logic for addRoute, get, post, call, notFound, etc.
    }
}