<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ResumenFinancieroController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $idEmpresa   = $this->request->getVar('id_empresa');
        $fechaInicio = $this->request->getVar('fecha_inicio');
        $fechaFin    = $this->request->getVar('fecha_fin');

        if (!$idEmpresa) {
            return $this->respond([
                'success' => false,
                'message' => 'ID de empresa no proporcionado'
            ], 400);
        }

        $db = db_connect();

        $condFecha = '';
        $params = [$idEmpresa];

        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $condFecha = " AND fecha_emision BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }

        try {
            $sqlVentas = "SELECT COALESCE(SUM(importe_total),0) AS ingresos,
                                 COALESCE(SUM(igv),0) AS igv_ventas
                          FROM facturas
                          WHERE id_empresa = ? AND origen = 'VENTA' $condFecha";
            $ventas = $db->query($sqlVentas, $params)->getRowArray();

            $sqlCompras = "SELECT COALESCE(SUM(importe_total),0) AS egresos,
                                  COALESCE(SUM(igv),0) AS igv_compras
                           FROM facturas
                           WHERE id_empresa = ? AND origen = 'COMPRA' $condFecha";
            $compras = $db->query($sqlCompras, $params)->getRowArray();

            return $this->respond([
                'success' => true,
                'data' => [[
                    'ingresos'    => $ventas['ingresos'],
                    'egresos'     => $compras['egreses'],
                    'igv_ventas'  => $ventas['igv_ventas'],
                    'igv_compras' => $compras['igv_compras']
                ]]
            ]);

        } catch (\Throwable $e) {
            return $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
