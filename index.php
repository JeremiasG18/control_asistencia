<?php

// Cargo el autoload para la carga automatica de clases

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use App\Sca\controllers\ControllersView;

require 'vendor/autoload.php';

// Cargo las variables de entorno
$dovenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dovenv->load();

define('APP_URL', $_ENV['APP_URL']);


// Definimos las rutas
$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {

    // Ruta para la página de inicio
    $r->addRoute('GET', '/', function(){
        require './src/views/login.html';
    });

    $r->addRoute('GET', '/login', function() {
        
    });

    // Ruta para saludar a un usuario
    $r->addRoute('GET', '/saludar/{nombre}', function($params) {
        echo "¡Hola, " . htmlspecialchars($params['nombre']) . "!";
    });

    // Ruta para mostrar información de un usuario por ID
    $r->addRoute('GET', '/usuario/{id:\d+}', function($params) {
        echo "ID del usuario: " . htmlspecialchars($params['id']);
    });
});

// Obtenemos el método HTTP y la URI
$basePath = '/control_asistencia'; // Define la ruta base de tu proyecto
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Elimina la ruta base de la URI
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}


// Eliminamos los parámetros de la URI (como ?foo=bar)
if (($pos = strpos($uri, '?')) !== false) {
    $uri = substr($uri, 0, $pos);
}

// Despachamos la ruta
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        echo "404 - Página no encontrada";
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        echo "405 - Método no permitido";
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $params = $routeInfo[2];
        $handler($params);
        break;
}

?>