<?php

namespace App\Controllers;

use App\Models\EmpresaModel;
use CodeIgniter\Controller;

class RegistroController extends Controller
{
    protected $empresaModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
    }

    // Registrar empresa
    public function registrar()
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON(['error' => 'No se enviaron datos']);
        }

        if (empty($json['ruc']) || empty($json['razonSocial']) || empty($json['usuarioSol']) || empty($json['claveSol'])) {
            return $this->response->setJSON(['error' => 'Faltan campos obligatorios']);
        }

        try {
            $data = [
                'ruc'              => $json['ruc'],
                'razon_social'     => $json['razonSocial'],
                'usuario_sol'      => $json['usuarioSol'],
                'clave_sol'        => $json['claveSol'],
                'api_client_id'    => $json['apiClientId'] ?? null,
                'api_client_secret'=> $json['apiClientSecret'] ?? null
            ];

            $this->empresaModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Empresa registrada correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Error al registrar empresa: ' . $e->getMessage()
            ]);
        }
    }

    // Consulta RUC usando API externa
    public function consultaRuc()
    {
        $ruc = $this->request->getGet('ruc');

        if (!$ruc || strlen($ruc) !== 11) {
            return $this->response->setJSON(['error' => 'RUC inválido']);
        }

        $url = "https://api.decolecta.com/v1/sunat/ruc/full?numero=" . $ruc;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer sk_11811.yyYsci6katJjLjASSLauYChZQ1NUxIyb"
        ]);

        // Solo para desarrollo local si hay problemas de SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return $this->response->setJSON(['error' => $error]);
        }

        curl_close($ch);

        // Retornar la respuesta de la API externa directamente
        return $this->response->setJSON(json_decode($response, true));
    }
}
