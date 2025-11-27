<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';
    
    // Todos los campos permitidos para inserción/actualización
    protected $allowedFields = [
        'ruc',
        'razon_social',
        'estado',
        'usuario_sol',
        'clave_sol',
        'api_client_id',
        'api_client_secret'
    ];

    // Listar todas las empresas
    public function listarEmpresas()
    {
        return $this->select('id_empresa, ruc, razon_social, estado')
                    ->orderBy('razon_social', 'ASC')
                    ->findAll();
    }

    // Obtener empresa por ID
    public function obtenerEmpresaPorId($id)
    {
        return $this->where('id_empresa', $id)->first();
    }

    // Obtener empresa junto con sus facturas y archivos
    public function obtenerEmpresaConFacturas($idEmpresa)
    {
        $db = \Config\Database::connect();

        $empresa = $this->obtenerEmpresaPorId($idEmpresa);
        if (!$empresa) return null;

        $sql = "
            SELECT f.id_factura, f.nro_cpe, f.emisor_ruc, f.emisor_nombre,
                   f.receptor_ruc, f.receptor_nombre, f.importe_total,
                   f.moneda, f.fecha_emision, f.estado,
                   a.id_archivo, a.nombre_archivo
            FROM facturas f
            LEFT JOIN archivos_factura a ON f.id_factura = a.id_factura
            WHERE f.id_empresa = ?
            ORDER BY f.fecha_emision DESC
        ";

        $facturas = $db->query($sql, [$idEmpresa])->getResultArray();

        $empresa['facturas'] = $facturas;
        return $empresa;
    }

    // Contar facturas de una empresa
    public function contarFacturas($idEmpresa)
    {
        return $this->db->table('facturas')
                        ->where('id_empresa', $idEmpresa)
                        ->countAllResults();
    }

    // Sumar importe_total de facturas de una empresa
    public function sumarFacturas($idEmpresa)
    {
        return $this->db->table('facturas')
                        ->selectSum('importe_total', 'total')
                        ->where('id_empresa', $idEmpresa)
                        ->get()
                        ->getRow()
                        ->total ?? 0;
    }
}
