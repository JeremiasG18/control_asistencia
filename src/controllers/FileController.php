<?php

namespace Jeremias\ControlAsistencia\Controllers;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FileController{

    public function uploadFile($file): array{

        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {

            $AllowedTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ];

            $tmpName = $file['tmp_name'];

            if (!in_array(mime_content_type($tmpName), $AllowedTypes)) {
                return ['status' => 'error', 'message' => 'El archivo no es del tipo solicitado. Solo son permitidos archivos excel.'];
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                return ['status' => 'error', 'message' => 'El archivo es demasiado grande. El tamaño máximo permitido es de 5MB.'];
            }

            $nameF = basename($file['name']);
            $name = preg_replace('/\s+/', '_', $nameF);
            $name = uniqid() . $name;

            $uploadDir = 'public/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $destination = $uploadDir . $name;

            if (move_uploaded_file($tmpName, $destination)) {
                return $this->register($nameF, $destination);

            } else {
                return ['status' => 'error', 'message' => 'Failed to move uploaded file.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'File upload error: ' . $file['error']];
        }

    }

    public function register($nameFile, $destination){

        $nameFile = strtolower($nameFile);
        $typeMeet = '';
        
        if (str_contains($nameFile, 'célula') || str_contains($nameFile, 'celula')){
            $typeMeet = 'Célula';
            $nameFile = str_replace(['célula', 'celula', '()' ,'.xlsx'], '', $nameFile);
        }
        
        if (str_contains($nameFile, 'discipulado')){
            $typeMeet = 'Discipulado';
            $nameFile = str_replace(['discipulado', '()' ,'.xlsx'], '', $nameFile);
        }
        
        $nameFile = trim(ucwords($nameFile));

        $data = [
            'id' => uniqid(),
            'name_file' => $nameFile,
            'type_meet' => $typeMeet,
            'destination' => $destination
        ];

        $urlFile = 'public/data.json';
        $archivo = file_get_contents($urlFile);
        $archivo = json_decode($archivo, true);

        $archivo[] = $data;
        $jsonUpdate = json_encode($archivo, JSON_PRETTY_PRINT);

        file_put_contents($urlFile, $jsonUpdate);

        return $archivo;

    }

    public function showFiles(): array{
        $urlFile = 'public/data.json';
        $data = file_exists($urlFile) ? json_decode(file_get_contents($urlFile), true) : [];
        return $data;
    }

    public function showAsistents(string $id){

        $urlFile = 'public/data.json';
        $file = file_get_contents($urlFile);
        $file = json_decode($file, true);

        $destination = '';
        for ($i=0; $i < count($file); $i++) { 
            foreach ($file[$i] as $key => $value) {
                if ($key == 'id' && $value == $id) {
                    $destination = $file[$i]['destination'];
                    break;
                }else{
                }
            }
        }

        if (empty($destination) || !file_exists($destination)) {
            return ['status' => 'error', 'message' => 'No se encontro el archivo.'];
        }

        $document = IOFactory::load($destination);
        $valor = $document->getActiveSheet()->getCell('A11')->getValue();
        $valor = strtolower($valor);

        $asistentes = [];
        if ($valor == 'asistentes') {
            $a = 14;
            for ($i=1; $i < $a; $i++) { 
                $valor = trim(str_replace("$i)", '', $document->getActiveSheet()->getCell('A' . $a)->getValue()));
                if ($valor == ''){
                    return [$asistentes];
                }
                $asistentes[] = $valor;
                $a++;
            }
            return $asistentes;
        }
    }

}