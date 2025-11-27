<?php

namespace App\Controllers;

use App\Models\EmpresaModel;

class DashboardController extends BaseController
{
    public function ver($idEmpresa)
    {
        $empresaModel = new EmpresaModel();
        $empresa = $empresaModel->find($idEmpresa);

        if (!$empresa) {
            return redirect()->to('/empresa')->with('error', 'Empresa no encontrada.');
        }

        return view('mantenimiento/dashboard', [
            'empresa' => $empresa
        ]);
    }
}
