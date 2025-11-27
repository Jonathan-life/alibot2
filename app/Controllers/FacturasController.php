<?php

namespace App\Controllers;

use App\Models\FacturaModel;
use App\Models\ArchivoFacturaModel;
use App\Models\EmpresaModel;
use CodeIgniter\Controller;

class FacturasController extends Controller
{
    protected $empresaModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * Método interno para obtener facturas con sus archivos
     */
    private function obtenerFacturas($idEmpresa, $fecha_inicio = null, $fecha_fin = null)
    {
        $facturaModel = new FacturaModel();
        $archivoModel = new ArchivoFacturaModel();

        $facturas = $facturaModel
            ->where('id_empresa', $idEmpresa)
            ->where('origen', 'COMPRA')
            ->orderBy('id_factura', 'DESC')
            ->findAll();

        // Filtrar por fecha si aplica
        if ($fecha_inicio) {
            $facturas = array_filter($facturas, fn($f) => $f['fecha_emision'] >= $fecha_inicio);
        }
        if ($fecha_fin) {
            $facturas = array_filter($facturas, fn($f) => $f['fecha_emision'] <= $fecha_fin);
        }

        // Adjuntar archivos a cada factura
        foreach ($facturas as &$factura) {
            $factura['archivos'] = $archivoModel
                ->where('id_factura', $factura['id_factura'])
                ->orderBy('tipo')
                ->findAll();
        }

        return $facturas;
    }

    /**
     * Listar todas las facturas (PDF y ZIP)
     */
    public function listarCompras($idEmpresa = null)
    {
        $request = \Config\Services::request();

        if (!$idEmpresa) {
            return redirect()->back()->with('error', 'Empresa no especificada.');
        }

        $fecha_inicio = $request->getGet('fecha_inicio') ?? '';
        $fecha_fin = $request->getGet('fecha_fin') ?? '';

        $empresa = $this->empresaModel->find($idEmpresa);
        if (!$empresa) {
            return redirect()->back()->with('error', 'Empresa no encontrada.');
        }

        $facturas = $this->obtenerFacturas($idEmpresa, $fecha_inicio, $fecha_fin);

        return view('reportes/facturas_empresa', [
            'empresa' => $empresa,
            'facturas' => $facturas,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'idEmpresa' => $idEmpresa
        ]);
    }

    /**
     * Listar solo facturas con archivos XML (ZIP)
     */
    public function listarActivasxml($idEmpresa = null)
    {
        $request = \Config\Services::request();

        if (!$idEmpresa) {
            return redirect()->back()->with('error', 'Empresa no especificada.');
        }

        $fecha_inicio = $request->getGet('fecha_inicio') ?? '';
        $fecha_fin = $request->getGet('fecha_fin') ?? '';

        $empresa = $this->empresaModel->find($idEmpresa);
        if (!$empresa) {
            return redirect()->back()->with('error', 'Empresa no encontrada.');
        }

        $facturas = $this->obtenerFacturas($idEmpresa, $fecha_inicio, $fecha_fin);

        // Filtrar archivos para que solo queden ZIP
        foreach ($facturas as &$factura) {
            if (!empty($factura['archivos'])) {
                $factura['archivos'] = array_filter($factura['archivos'], function ($archivo) {
                    $tipo = strtoupper(trim($archivo['tipo'] ?? ''));
                    return $tipo === 'ZIP';
                });
            }
        }

        return view('reportes/facturas_empresaxml', [
            'empresa' => $empresa,
            'facturas' => $facturas,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'idEmpresa' => $idEmpresa
        ]);
    }
    
}
