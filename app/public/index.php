<?php

use repository\CommentRepository;
use repository\PostRepository;
use services\CommentService;
use services\Database;
use services\PostService;
use services\Validator;

require '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$validator = new Validator();

$commentRepository = new CommentRepository($db);
$commentService = new CommentService($commentRepository, $validator);
$postRepository = new PostRepository($db);
$postService = new PostService($postRepository, $validator);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/post/{id:\d+}', ['controllers\\PostController', 'getPostById']);
    $r->addRoute('POST', '/post/create', ['controllers\\PostController', 'createPost']);
    $r->addRoute('POST', '/post/{postId:\d+}/comments/create', ['controllers\CommentController', 'createComment']);
    $r->addRoute('GET', '/post/{postId:\d+}/comments', ['controllers\CommentController', 'getAllCommentsForPost']);
    $r->addRoute('GET', '/post/list', ['controllers\PostController', 'getListOfPostsWithThreeComments']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $class = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        // ... call $handler with $vars
        $service = match ($class) {
            'controllers\PostController' => $postService,
            'controllers\CommentController' => $commentService
        };

        call_user_func_array(array(new $class($service), $method), $vars);
        break;
}
