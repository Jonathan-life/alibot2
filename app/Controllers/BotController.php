<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use mysqli;

class BotController extends Controller
{
    private $db;
    private $python;
    private $bot_dir;
    private $descargas_dir;

    public function __construct()
    {
        // Conexión DB (CI4 también tiene su modelo, pero mantenemos tu método)
        $this->db = new mysqli("localhost", "root", "", "sistema_contable");
        if ($this->db->connect_errno) {
            die("Error MySQL: " . $this->db->connect_error);
        }

        // Ruta del ejecutable de Python
        $this->python = "C:\\Users\\JONATHAN\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";

        // Rutas seguras en CodeIgniter
        $this->bot_dir = ROOTPATH . "bot";
        $this->descargas_dir = WRITEPATH . "descargas_sunat";

        // Crear carpetas si no existen
        if (!is_dir($this->descargas_dir)) {
            mkdir($this->descargas_dir, 0777, true);
        }
        if (!is_dir($this->bot_dir . "/logs")) {
            mkdir($this->bot_dir . "/logs", 0777, true);
        }
    }

    public function ejecutar()
    {
        $req = $this->request->getJSON(true);

        $id_empresa = $req['id_empresa'];
        $fecha_inicio = $req['fecha_inicio'];
        $fecha_fin = $req['fecha_fin'];
        $tipo_descarga = $req['tipo_descarga'];

        return $this->ejecutarBot($id_empresa, $fecha_inicio, $fecha_fin, $tipo_descarga);
    }

    private function ejecutarBot($id_empresa, $fecha_inicio, $fecha_fin, $tipo_descarga)
    {
        // Obtener datos de empresa
        $stmt = $this->db->prepare("SELECT id_empresa, ruc, usuario_sol, clave_sol, razon_social 
                                    FROM empresas 
                                    WHERE id_empresa=? AND estado='ACTIVO'");
        $stmt->bind_param("i", $id_empresa);
        $stmt->execute();
        $empresa = $stmt->get_result()->fetch_assoc();

        if (!$empresa) {
            return $this->response->setJSON(["status" => "error", "mensaje" => "Empresa no encontrada."]);
        }

        // Crear JSON
        $json_file = $this->bot_dir . "/data.json";
        $log_file = $this->bot_dir . "/logs/log_" . time() . ".txt";

        $data = [
            "empresa" => $empresa,
            "fecha_inicio" => date("d/m/Y", strtotime($fecha_inicio)),
            "fecha_fin" => date("d/m/Y", strtotime($fecha_fin)),
            "ruta_descargas" => $this->descargas_dir
        ];

        file_put_contents($json_file, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Selección de bots
        $bots = [];
        if ($tipo_descarga === "emitidas") 
            $bots[] = $this->bot_dir . "/ventaselenium.py";
        elseif ($tipo_descarga === "recibidas") 
            $bots[] = $this->bot_dir . "/selenium_bot.py";
        elseif ($tipo_descarga === "ambas") 
            $bots = [
                $this->bot_dir . "/ventaselenium.py",
                $this->bot_dir . "/selenium_bot.py"
            ];

        $outputs = [];
        $return_codes = [];

        // Ejecución de bots
        foreach ($bots as $script) {
            $cmd = "\"{$this->python}\" \"$script\" \"$json_file\" 2>&1";
            exec($cmd, $out, $ret);
            $outputs[] = implode("\n", $out);
            $return_codes[] = $ret;

            file_put_contents($log_file,
                "=== " . basename($script) . " ===\n" . implode("\n", $out) . "\n\n",
                FILE_APPEND
            );
        }

        // Procesamiento final
        $procesar_script = $this->bot_dir . "/procesar_archivos.py";
        $cmd_proc = "\"{$this->python}\" \"$procesar_script\" \"$json_file\" 2>&1";
        exec($cmd_proc, $out_proc, $ret_proc);

        $outputs[] = implode("\n", $out_proc);
        $return_codes[] = $ret_proc;

        file_put_contents($log_file,
            "=== procesar_archivos.py ===\n" . implode("\n", $out_proc) . "\n\n",
            FILE_APPEND
        );

        $todo_ok = !in_array(1, $return_codes);

        return $this->response->setJSON([
            "status" => $todo_ok ? "success" : "error",
            "mensaje" => $todo_ok
                ? "¡Proceso completado correctamente! 😃"
                : "⚠ Hubo algunos errores, revisa el log.",
            "log" => $log_file,
            "salida" => $outputs
        ]);
    }

    public function __destruct()
    {
        $this->db->close();
    }
    public function index()
{
    $empresas = $this->db->query("SELECT id_empresa, razon_social FROM empresas WHERE estado='ACTIVO'")
                         ->fetch_all(MYSQLI_ASSOC);

    return view('mantenimiento/sunat-og', [
        'empresas' => $empresas
    ]);
}
}
