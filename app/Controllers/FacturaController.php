<?php

namespace App\Controllers;

use App\Models\FacturaModel;
use App\Models\EmpresaModel;
use CodeIgniter\Controller;

class FacturaController extends Controller
{
    protected $facturaModel;
    protected $empresaModel;

    public function __construct()
    {
        $this->facturaModel = new FacturaModel();
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * Devuelve facturas en JSON por empresa
     */
    public function listarPorEmpresa($id_empresa)
    {
        $data = $this->facturaModel->listarPorEmpresa($id_empresa);
        return $this->response->setJSON($data);
    }

    /**
     * Devuelve facturas en JSON por periodo
     */
    public function listarPorPeriodo($id_empresa, $periodo)
    {
        $data = $this->facturaModel->listarPorPeriodo($id_empresa, $periodo);
        return $this->response->setJSON($data);
    }

    /**
     * Vista del Libro Contable
     * Filtra por empresa y periodo, calcula totales y envía datos a la vista
     */
    public function libroContable()
    {
        $request = service('request');
        $id_empresa = $request->getGet('id_empresa');
        $periodo    = $request->getGet('periodo'); // Ej: 202409
        $tipo       = $request->getGet('tipo') ?? 'ventas';

        if (!$id_empresa) {
            $data['empresas'] = $this->empresaModel->findAll();
            return view('libro_contable', $data);
        }

        $empresa = $this->empresaModel->find($id_empresa);

        if ($periodo) {
            $facturas = $this->facturaModel->listarPorPeriodo($id_empresa, $periodo);
        } else {
            $facturas = $this->facturaModel->listarPorEmpresa($id_empresa);
        }

        if (!$empresa || empty($facturas)) {
            return "<div class='alert alert-danger'>No se encontró empresa o facturas del periodo seleccionado.</div>";
        }

        // Totales
        $totales = [
            'base_gravadas'    => 0,
            'igv_gravadas'     => 0,
            'base_exoneradas'  => 0,
            'base_inafectas'   => 0,
            'no_gravadas'      => 0,
            'otros_tributos'   => 0
        ];

        foreach ($facturas as $f) {
            $totales['base_gravadas']   += (float)($f['base_gravadas'] ?? 0);
            $totales['igv_gravadas']    += (float)($f['igv'] ?? 0);
            $totales['base_exoneradas'] += (float)($f['base_exoneradas'] ?? 0);
            $totales['base_inafectas']  += (float)($f['base_inafectas'] ?? 0);
            $totales['no_gravadas']     += (float)($f['no_gravadas'] ?? 0);
            $totales['otros_tributos']  += (float)($f['otros_tributos'] ?? 0);
        }

        $totales['base_general']    = $totales['base_gravadas'] + $totales['base_exoneradas'] + $totales['base_inafectas'] + $totales['no_gravadas'];
        $totales['igv_general']     = $totales['igv_gravadas'];
        $totales['importe_general'] = $totales['base_general'] + $totales['igv_general'];

        // Convertir periodo a "MES AÑO"
        $meses = [
            "01"=>"ENERO","02"=>"FEBRERO","03"=>"MARZO","04"=>"ABRIL",
            "05"=>"MAYO","06"=>"JUNIO","07"=>"JULIO","08"=>"AGOSTO",
            "09"=>"SETIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE"
        ];
        $anio = substr($periodo, 0, 4);
        $mes  = substr($periodo, 4, 2);
        $nombreMes = $meses[$mes] ?? "MES DESCONOCIDO";

        $formato = $tipo === 'compras' 
                   ? 'FORMATO 8.1: REGISTRO DE COMPRAS - MONEDA NACIONAL'
                   : 'FORMATO 14.1: REGISTRO DE VENTAS - MONEDA NACIONAL';

        $data = compact('empresa', 'facturas', 'totales', 'nombreMes', 'anio', 'formato', 'tipo', 'periodo');

        return view('reportes/listado_empresas', $data);
    }
        
public function empresasVentas()
{
    $request = service('request');
    $id_empresa = $request->getGet('id_empresa');
    $periodo    = $request->getGet('periodo'); // Ej: 202409

    if (!$id_empresa) {
        $data['empresas'] = $this->empresaModel->findAll();
        return view('reportes/empresas_ventas', $data);
    }

    $empresa = $this->empresaModel->find($id_empresa);

    // Solo facturas de VENTAS
    if ($periodo) {
        $facturas = $this->facturaModel->listarPorPeriodo($id_empresa, $periodo, 'ventas');
    } else {
        $facturas = $this->facturaModel->listarPorEmpresa($id_empresa, 'ventas');
    }

    if (!$empresa || empty($facturas)) {
        return "<div class='alert alert-danger'>No se encontró empresa o facturas de ventas para el periodo seleccionado.</div>";
    }

    // Totales
    $totales = [
        'base_gravadas'    => 0,
        'igv_gravadas'     => 0,
        'base_exoneradas'  => 0,
        'base_inafectas'   => 0,
        'no_gravadas'      => 0,
        'otros_tributos'   => 0
    ];

    foreach ($facturas as $f) {
        $totales['base_gravadas']   += (float)($f['base_gravadas'] ?? 0);
        $totales['igv_gravadas']    += (float)($f['igv'] ?? 0);
        $totales['base_exoneradas'] += (float)($f['base_exoneradas'] ?? 0);
        $totales['base_inafectas']  += (float)($f['base_inafectas'] ?? 0);
        $totales['no_gravadas']     += (float)($f['no_gravadas'] ?? 0);
        $totales['otros_tributos']  += (float)($f['otros_tributos'] ?? 0);
    }

    $totales['base_general']    = $totales['base_gravadas'] + $totales['base_exoneradas'] + $totales['base_inafectas'] + $totales['no_gravadas'];
    $totales['igv_general']     = $totales['igv_gravadas'];
    $totales['importe_general'] = $totales['base_general'] + $totales['igv_general'];

    // Mes y año
    $meses = [
        "01"=>"ENERO","02"=>"FEBRERO","03"=>"MARZO","04"=>"ABRIL",
        "05"=>"MAYO","06"=>"JUNIO","07"=>"JULIO","08"=>"AGOSTO",
        "09"=>"SETIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE"
    ];
    $anio = substr($periodo, 0, 4);
    $mes  = substr($periodo, 4, 2);
    $nombreMes = $meses[$mes] ?? "MES DESCONOCIDO";

    // Formato específico para ventas
    $formato = 'FORMATO 14.1: REGISTRO DE VENTAS - MONEDA NACIONAL';

    $data = compact('empresa', 'facturas', 'totales', 'nombreMes', 'anio', 'formato', 'periodo');

    return view('reportes/empresas_ventas', $data);
}

}
