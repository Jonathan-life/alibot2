


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturas de Compras (XML) - <?= htmlspecialchars($empresa['razon_social']) ?> (<?= htmlspecialchars($empresa['ruc']) ?>)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { padding-top: 40px; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3>🧾 Facturas de <strong>COMPRAS</strong> (XML) de <?= esc($empresa['razon_social']) ?> (<?= esc($empresa['ruc']) ?>)</h3>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="id_empresa" value="<?= esc($idEmpresa) ?>">
        <div class="col-auto">
            <label for="fecha_inicio" class="form-label">Fecha inicio</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= esc($fecha_inicio) ?>">
        </div>
        <div class="col-auto">
            <label for="fecha_fin" class="form-label">Fecha fin</label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= esc($fecha_fin) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?= base_url('reportes/facturas_empresaxml/' . $idEmpresa) ?>" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <?php if (!empty($facturas)): ?>
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nro CPE</th>
                    <th>Emisor</th>
                    <th>Importe</th>
                    <th>Moneda</th>
                    <th>Fecha Emisión</th>
                    <th>Estado SUNAT</th>
                    <th>Archivo XML</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                    <tr>
                        <td><?= esc($factura['id_factura']) ?></td>
                        <td><?= esc($factura['nro_cpe'] ?? '-') ?></td>
                        <td>
                            <?= esc($factura['nombre_emisor'] ?? '-') ?><br>
                            <small class="text-muted"><?= esc($factura['ruc_emisor'] ?? '-') ?></small>
                        </td>
                        <td><?= number_format((float)$factura['importe_total'], 2) ?></td>
                        <td><?= esc($factura['moneda'] ?? '-') ?></td>
                        <td><?= esc($factura['fecha_emision'] ?? '-') ?></td>
                        <td>
                            <?php
                            $estado = esc($factura['estado_sunat'] ?? 'Desconocido');
                            $badge_class = match ($estado) {
                                'ACEPTADO' => 'success',
                                'RECHAZADO' => 'danger',
                                'OBSERVADO' => 'warning',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?= $badge_class ?>"><?= $estado ?></span>
                <td>
    <?php
    $xml_encontrado = false;

    if (!empty($factura['archivos']) && is_array($factura['archivos'])) {
        foreach ($factura['archivos'] as $archivo) {
            $tipo = strtoupper(trim($archivo['tipo'] ?? ''));
            if ($tipo === 'ZIP') {
                echo "<a href='" . base_url('descarga?id=' . $archivo['id_archivo']) . "' class='btn btn-sm btn-outline-success'>📥 Descargar XML</a>";
                $xml_encontrado = true;
                break; // solo el primero que encuentre ZIP
            }
        }
    }

    if (!$xml_encontrado) {
        echo "<span class='text-muted'>Sin XML</span>";
    }
    ?>
</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">⚠ No hay facturas de <strong>COMPRA</strong> registradas para esta empresa en el rango de fechas seleccionado.</div>
    <?php endif; ?>

    <a href="<?= base_url('reportes/ventasxml') ?>" class="btn btn-secondary mt-3">← Volver al listado de empresas</a>
</div>

</body>
</html>


 