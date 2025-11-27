<?php

namespace App\Controllers;

use App\Models\EmpresaModel;
use App\Models\FacturaModel; // <--- IMPORTAR EL MODELO
use CodeIgniter\Controller;

class EmpresaController extends Controller
{
    // Método index requerido por la ruta '/empresa'
    public function index()
    {
        $model = new EmpresaModel();
        $data['empresas'] = $model->listarEmpresas();

        return view('empresa/registro', $data);
    }

    // Método para API listar empresas
    public function listar()
    {
        $model = new EmpresaModel();
        $empresas = $model->listarEmpresas();

        return $this->response->setJSON([
            'success' => true,
            'data' => $empresas
        ]);
    }

    // Método para eliminar empresa
    public function eliminar()
    {
        $json = $this->request->getJSON(true);
        if (!isset($json['id_empresa'])) {
            return $this->response->setJSON(['error' => 'Falta id_empresa']);
        }

        $model = new EmpresaModel();
        $deleted = $model->delete($json['id_empresa']);

        if ($deleted) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['error' => 'No se pudo eliminar']);
    }

    // Ver empresas activas en mantenimiento
    public function verEmpresas()
    {
        $model = new EmpresaModel();
        $data['empresas'] = $model->where('estado', 'ACTIVO')->findAll();
        return view('mantenimiento/empresa', $data);
    }

    // Listar empresas activas en venta_sire
    public function listarActivas()
    {
        $model = new EmpresaModel();
        $empresas = $model->where('estado', 'ACTIVO')->findAll();
        return view('reportes/venta_sire', ['empresas' => $empresas]);
    }

    // Listar empresas activas en ventasxml
    public function listarActivasxml()
    {
        $model = new EmpresaModel();
        $empresas = $model->where('estado', 'ACTIVO')->findAll();
        return view('reportes/ventasxml', ['empresas' => $empresas]);
    }

    // Listar todas las empresas (sin filtro)
    public function listarTodas()
    {
        $model = new EmpresaModel();
        $empresas = $model->findAll(); // Trae todas las empresas

        return view('reportes/libro_contable', ['empresas' => $empresas]);
    }

 // Obtener periodos disponibles
    public function get_periodos()
    {
        try {
            $request = \Config\Services::request();
            $empresa = $request->getGet('empresa');
            $tipo = $request->getGet('tipo');

            if (!$empresa || !$tipo) {
                return $this->response->setJSON([]);
            }

            $origen = strtolower($tipo) === "compras" ? "COMPRA" : "VENTA";

            $model = new FacturaModel();
            $periodos = $model->getPeriodos($empresa, $origen); // ['202405', '202406', ...]

            if (!is_array($periodos)) {
                $periodos = [];
            }

            // Convertir a formato legible
            $meses = [
                "01" => "Enero",
                "02" => "Febrero",
                "03" => "Marzo",
                "04" => "Abril",
                "05" => "Mayo",
                "06" => "Junio",
                "07" => "Julio",
                "08" => "Agosto",
                "09" => "Septiembre",
                "10" => "Octubre",
                "11" => "Noviembre",
                "12" => "Diciembre"
            ];

            $periodosLegibles = [];
            foreach ($periodos as $p) {
                $anio = substr($p, 0, 4);
                $mes = substr($p, 4, 2);
                $periodosLegibles[] = [
                    'value' => $p,
                    'texto' => ($meses[$mes] ?? "Mes desconocido") . ' ' . $anio
                ];
            }

            return $this->response->setJSON($periodosLegibles);

        } catch (\Exception $e) {
            log_message('error', 'Error en get_periodos: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }
    

}
