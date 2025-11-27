<?php

namespace App\Models;
use CodeIgniter\Model;

class ArchivoFacturaModel extends Model
{
    protected $table = 'archivos_factura';
    protected $primaryKey = 'id_archivo';
    protected $allowedFields = ['id_factura', 'tipo', 'ruta'];
}
