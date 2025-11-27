<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Descargar documentos SUNAT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { background-color: #f0f2f5; }
    .container { max-width: 600px; margin-top: 60px; }
    .card { border-radius: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
    .btn-primary { background: linear-gradient(135deg, #007bff, #0056b3); border: none; }
  </style>
</head>

<body>

<div class="container">
  <div class="card p-4">
    <h3 class="text-center mb-4">📂 Descargar documentos SUNAT</h3>

    <form id="form-descarga">
      <div class="mb-3">
        <label class="form-label">Empresa</label>
        <select id="id_empresa" class="form-select" required>
          <option value="">Seleccione una empresa</option>
          <?php foreach ($empresas as $e): ?>
              <option value="<?= $e['id_empresa'] ?>"><?= esc($e['razon_social']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Tipo de descarga</label>
        <select id="tipo_descarga" class="form-select" required>
          <option value="recibidas">Recibidas (Compras)</option>
          <option value="emitidas">Emitidas (Ventas)</option>
          <option value="ambas">Ambas</option>
        </select>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Fecha inicio</label>
          <input type="date" id="fecha_inicio" class="form-control" required>
        </div>
        <div class="col">
          <label class="form-label">Fecha fin</label>
          <input type="date" id="fecha_fin" class="form-control" required>
        </div>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">⬇ Descargar</button>
      </div>
    </form>

  </div>
</div>

<script>
document.getElementById("form-descarga").addEventListener("submit", (e) => {
    e.preventDefault();

    Swal.fire({
        title: "Descargando...",
        html: "Por favor espera, esto puede tardar unos minutos...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch("/api/bot/ejecutar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id_empresa: document.getElementById("id_empresa").value,
          tipo_descarga: document.getElementById("tipo_descarga").value,
          fecha_inicio: document.getElementById("fecha_inicio").value,
          fecha_fin: document.getElementById("fecha_fin").value
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();

        Swal.fire({
            icon: data.status === "success" ? "success" : "error",
            title: data.status === "success" ? "✔ Descarga completada" : "❌ Error",
            text: data.mensaje
        });
    });
});
</script>

</body>
</html>
