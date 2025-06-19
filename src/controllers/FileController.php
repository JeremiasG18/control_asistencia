<?php

namespace Jeremias\ControlAsistencia\Controllers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function searchFile(string $id){
        
        $urlFile = 'public/data.json';
        $file = file_get_contents($urlFile);
        $file = json_decode($file, true);

        $destination = '';
        for ($i=0; $i < count($file); $i++) { 
            foreach ($file[$i] as $key => $value) {
                if ($key == 'id' && $value == $id) {
                    $destination = $file[$i]['destination'];
                    break;
                }
            }
        }

        if (empty($destination) || !file_exists($destination)) {
            return ['status' => 'error', 'message' => 'No se encontro el archivo.'];
        }

        return $destination;
    }

    public function showAsistents(string $id){

        $destination = $this->searchFile($id);

        if (!empty($destination['status'])) return $destination;

        $document = IOFactory::load($destination);
        $hoja = $document->getActiveSheet();
        $valor = $hoja->getCell('A11')->getValue();
        $valor = strtolower($valor);

        $asistentes[] = ['id' => $id];

        if ($valor == 'asistentes') {
            $a = 14;
            
            for ($i=1; $i < $a; $i++) { 
                $valor = trim(str_replace("$i)", '', $hoja->getCell('A' . $a)->getValue()));
                
                if ($valor == ''){
                    return $asistentes;
                }

                $asistentes[$i]["id_asistente"] = $a;
                $asistentes[$i]["asistente"] = $valor;
                $asistentes[$i]["fecha_nacimiento"] = !empty($hoja->getCell('B' . $a)->getFormattedValue()) 
                ? date("Y-m-d", strtotime(str_replace("-", "/", $hoja->getCell('B' . $a)->getFormattedValue()))): null;
                $asistentes[$i]["bautismo"] = !empty($hoja->getCell('C' . $a)->getValue())
                ? strtolower($hoja->getCell('C' . $a)->getValue()) : null;
                $asistentes[$i]["encuentro"] = !empty($hoja->getCell('D' . $a)->getValue()) 
                ? strtolower($hoja->getCell('D' . $a)->getValue()) : null;
                $asistentes[$i]["abc"] = !empty($hoja->getCell('E' . $a)->getValue())
                ? strtolower($hoja->getCell('E' . $a)->getValue()) : null;
                $asistentes[$i]["nivel1"] = !empty($hoja->getCell('F' . $a)->getValue())
                ? strtolower($hoja->getCell('F' . $a)->getValue()) : null;
                $asistentes[$i]["nivel2"] = !empty($hoja->getCell('G' . $a)->getValue())
                ? strtolower($hoja->getCell('G' . $a)->getValue()) : null;
                $asistentes[$i]["mentores"] = !empty($hoja->getCell('H' . $a)->getValue())
                ? strtolower($hoja->getCell('H' . $a)->getValue()) : null;
                $a++;
            }

        }

        return ['status' => 'error', 'message' => 'La planilla no esta en el formato correcto'];
    }

    public function saveAsistents(string $id, array $asistents){

        $destination = $this->searchFile($id);

        return $asistents;

        if (!empty($destination['status'])) return $destination;

        $document = IOFactory::load($destination); // Cargar el Excel
        $hoja = $document->getActiveSheet();

        $j = 0;
        $asistentes = [];

        return $asistents;

        for ($i = 14; $i < 14 + count($asistents); $i++) {
            $hoja->setCellValue('A' . $i, $j + 1 . ') ' . $asistents["nombre"][$j]);
            $hoja->setCellValue('B' . $i, $j + 1 . ') ' . $asistents["fecha"][$j]);
            $hoja->setCellValue('C' . $i, $j + 1 . ') ' . $asistents["bautismo"][$j]);
            $hoja->setCellValue('D' . $i, $j + 1 . ') ' . $asistents["encuentro"][$j]);
            $hoja->setCellValue('E' . $i, $j + 1 . ') ' . $asistents["abc"][$j]);
            $hoja->setCellValue('F' . $i, $j + 1 . ') ' . $asistents["nivel1"][$j]);
            $hoja->setCellValue('G' . $i, $j + 1 . ') ' . $asistents["nivel2"][$j]);
            $hoja->setCellValue('H' . $i, $j + 1 . ') ' . $asistents["mentores"][$j]);

            // Limpiar el valor como hacías antes
            $valor = trim(str_replace($j + 1 . ")", '', $hoja->getCell('A' . $i)->getValue()));

            if ($valor != '') {
                $asistentes[] = $valor;
            }

            $j++;
        }

        // Guardar el archivo modificado
        $writer = new Xlsx($document);
        $writer->save($destination); // Sobrescribe el archivo original

        return $asistentes;

    }

}