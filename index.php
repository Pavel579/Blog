<?php

use config\Database;

require 'vendor/autoload.php';
require_once 'config/Database.php';

  $database = new Database();
  $db = $database->getConnection();

  $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector  $r) {
    //$r->get('/blog', [PostController::class, 'getBlogById']);
    //$r->addRoute('GET', '/users', 'get_all_users_handler');
    $r->addRoute('GET', '/post/{id:\d+}', ['controllers\\PostController', 'getPostById']);
    $r->addRoute('POST', '/post/create', ['controllers\\PostController', 'createPost']);
    $r->addRoute('POST', '/post/{postId:\d+}/comments/create', ['controllers\CommentController', 'createComment']);
    $r->addRoute('GET', '/post/{postId:\d+}/comments', ['controllers\CommentController', 'getAllCommentsForPost']);
    $r->addRoute('GET', '/post/list', ['controllers\PostController', 'getListOfPosts']);
    // {id} must be a number (\d+)
    //$r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    //$r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
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
      call_user_func_array(array(new $class($db), $method), $vars);
      break;
  }
