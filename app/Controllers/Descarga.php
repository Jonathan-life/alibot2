<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Descarga extends BaseController
{
    public function index()
    {
        // Obtener el id del archivo desde GET
        $id = $this->request->getGet('id');
        if (!$id) {
            return redirect()->back()->with('error', 'Archivo no especificado');
        }

        // Conectar a la base de datos
        $db = \Config\Database::connect();

        // Buscar el archivo en la tabla correcta
        $archivo = $db->table('archivos_factura')
                      ->where('id_archivo', $id)
                      ->get()
                      ->getRowArray();

        if (!$archivo) {
            return redirect()->back()->with('error', 'Archivo no encontrado');
        }

        // Detectar tipo MIME según la extensión
        $ext = pathinfo($archivo['nombre_archivo'], PATHINFO_EXTENSION);
        $mime = match(strtolower($ext)) {
            'zip' => 'application/zip',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };

        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Description', 'File Transfer')
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $archivo['nombre_archivo'] . '"')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Expires', '0')
            ->setHeader('Cache-Control', 'must-revalidate')
            ->setHeader('Pragma', 'public')
            ->setBody($archivo['archivo_binario']);
    }
}
