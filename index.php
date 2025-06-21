<?php

require 'vendor/autoload.php';

use Jeremias\ControlAsistencia\Controllers\FileController;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['accion'] === 'uploadfile'){
        if (!isset($_FILES['file'])){
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
            exit;
        }
    
        $file = new FileController();
        $response = $file->uploadFile($_FILES['file']);
        echo json_encode($response);
        exit;
    }

    if ($_POST['accion'] == 'saveAsistents') {

        if (empty($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'No selecciono ningun excel']);
            exit;
        }

        if (empty($_POST['asistentes'])){
            echo json_encode(['status' => 'error', 'message' => 'No ingreso ningun asistente']);
            exit;
        }
        
        $file = new FileController();
        $response = $file->saveAsistents($_POST['id'], $_POST['asistentes']);
        echo json_encode($response);
        exit;
    }


}else if($_SERVER['REQUEST_METHOD'] == 'GET'){

    if (isset($_GET['file'])) {

        if ($_GET['file'] == 0){
            echo json_encode(['status' => 'No se ha seleccionado una planilla']);
            exit;
        }

        $form = new FileController();
        $form = $form->showAsistents($_GET['file']);
        echo json_encode($form);
        exit;
    }
    
    if (!empty($_GET['accion']) && $_GET['accion'] == 'showforms') {
        $forms = new FileController();
        $response = $forms->showFiles();
        echo json_encode($response);
        exit;
    }


}

require_once 'public/templates/header.php';

$_GET['view'] = !empty($_GET['view']) ? $_GET['view'] : 'asistents';

if (file_exists('src/views/' . $_GET['view'] . '.php')) {
    require 'src/views/' . $_GET['view'] . '.php';
}

require_once 'public/templates/footer.php';