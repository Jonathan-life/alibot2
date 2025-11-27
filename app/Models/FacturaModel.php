<?php

namespace App\Models;

use CodeIgniter\Model;

class FacturaModel extends Model
{
    protected $table = 'facturas';
    protected $primaryKey = 'id_factura';
    protected $allowedFields = [
        'id_empresa',
        'nro_cpe',
        'nombre_emisor',
        'ruc_emisor',
        'importe_total',
        'moneda',
        'fecha_emision',
        'estado_sunat',
        'origen'
    ];

    /**
     * Listar facturas por empresa
     */
    public function listarPorEmpresa($id_empresa)
    {
        return $this->where('id_empresa', $id_empresa)
                    ->orderBy('fecha_emision', 'ASC')
                    ->findAll();
    }

    /**
     * Listar facturas por periodo (YYYYMM o YYYY-MM)
     */
    public function listarPorPeriodo($empresaId, $periodo)
    {
        // Convertir 202501 -> 2025-01
        if (preg_match('/^\d{6}$/', $periodo)) {
            $periodo = substr($periodo, 0, 4) . '-' . substr($periodo, 4, 2);
        }

        return $this->where('id_empresa', $empresaId)
                    ->where("DATE_FORMAT(fecha_emision, '%Y-%m')", $periodo)
                    ->findAll();
    }

    /**
     * Devuelve los periodos (YYYYMM) disponibles para una empresa según origen (COMPRA/VENTA)
     */
    public function getPeriodos($idEmpresa, $origen)
    {
        $query = $this->select("DISTINCT DATE_FORMAT(fecha_emision, '%Y%m') AS periodo", false)
                    ->where('id_empresa', $idEmpresa)
                    ->where('origen', $origen)
                    ->orderBy('periodo', 'DESC')
                    ->get();

        $rows = $query->getResultArray();

        return array_map(fn($r) => $r['periodo'], $rows);
    }
public function listarVentasPorPeriodo($id_empresa, $periodo)
{
    return $this->where('id_empresa', $id_empresa)
                ->where('origen', 'VENTA')       // <- Solo ventas
                ->where('DATE_FORMAT(fecha_emision, "%Y%m")', $periodo)
                ->findAll();
}


}

