
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Compras - Formato SUNAT 8.1</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { font-family: Arial, sans-serif; font-size: 11px; background-color: #fff; }
h4, h5, p { text-align: center; margin: 0; padding: 0; }
.table { border-collapse: collapse; width: 100%; text-align: center; }
.table th, .table td { border: 1px solid #000; padding: 3px; vertical-align: middle; }
.table thead tr th { background-color: #eaeaea; font-weight: bold; }
.header-info { text-align: center; margin-bottom: 10px; }
.resumen-totales { background-color: #f8f9fa; padding: 8px; border: 1px solid #ccc; margin-bottom: 10px; }
.resumen-totales strong { margin-right: 15px; }
tfoot td { font-weight: bold; background-color: #eaeaea; }
</style>
</head>
<body>
<div id="capturaPDF" class="container-fluid mt-3">

<div class="header-info">
    <h4><?= htmlspecialchars($empresa['razon_social']) ?></h4>
    <h5>RUC: <?= htmlspecialchars($empresa['ruc']) ?></h5>
    <p><?= $formato ?></p>
    <p>PERIODO: <?= $nombreMes . " " . $anio ?></p>
</div>

<div class="resumen-totales text-center">
    <strong>Total Base Imponible:</strong> S/ <?= number_format($totales['base_general'], 2) ?>
    <strong>Total IGV:</strong> S/ <?= number_format($totales['igv_general'], 2) ?>
    <strong>Total General:</strong> S/ <?= number_format($totales['importe_general'], 2) ?>
</div>

<table class="table table-bordered" id="tablaCompras">
    <thead>
        <tr>
            <th rowspan="3">N° Registro</th>
            <th rowspan="2">Fecha del Dcto</th>
            <th rowspan="3">Fecha de Vcto o Pago</th>
            <th colspan="3">Comprobante de Pago o Documento</th>
            <th rowspan="3">N° del Comprobante o N° de DUA</th>
            <th colspan="3">Información del Proveedor</th>
            <th colspan="2">Adquisiciones Gravadas destinadas a operaciones gravadas y/o de exportación</th>
            <th colspan="2">Adquisiciones Gravadas destinadas a operaciones gravadas y/o no gravadas</th>
            <th colspan="2">Adquisiciones Gravadas destinadas a operaciones no gravadas</th>
            <th rowspan="3">Adquisiciones Gravadas no gravadas</th>
            <th rowspan="3">Otros Tributos y cargos</th>
            <th rowspan="3">Importe Total</th>
            <th rowspan="3">Tipo de Cambio</th>
            <th colspan="4">Referencia del comprob. de pago o doc original que se modifica</th>
        </tr>
        <tr>
            <th rowspan="2">Tipo</th>
            <th rowspan="2">N° Serie</th>
            <th rowspan="2">Año DUA</th>
            <th>Tipo Doc.</th>
            <th>Número</th>
            <th rowspan="2">Apellidos y Nombres / Razón Social</th>
            <th rowspan="2">Base Imponible</th>
            <th rowspan="2">IGV</th>
            <th rowspan="2">Base Imponible</th>
            <th rowspan="2">IGV</th>
            <th rowspan="2">Base Imponible</th>
            <th rowspan="2">IGV</th>
            <th rowspan="2">Fecha</th>
            <th rowspan="3">Tipo</th>
            <th rowspan="3">Serie</th>
            <th rowspan="2">N° del Comprobante de pago o documento</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach ($facturas as $f): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($f['fecha_emision'] ?? '') ?></td>
            <td><?= htmlspecialchars($f['fecha_vencimiento'] ?? '') ?></td>

            <!-- Comprobante -->
            <td><?= htmlspecialchars($tipo_doc_sunat[$f['tipo_doc']] ?? '99') ?></td>
            <td><?= htmlspecialchars($f['serie'] ?? '') ?></td>
            <td><?= htmlspecialchars(date('Y', strtotime($f['fecha_emision'] ?? 'now'))) ?></td>
            <td><?= htmlspecialchars($f['correlativo'] ?? '') ?></td>

            <!-- Proveedor -->
            <td><?= htmlspecialchars($f['tipo_doc_identidad'] ?? '6') ?></td>
            <td><?= htmlspecialchars($f['ruc_emisor'] ?? '') ?></td>
            <td><?= htmlspecialchars($f['nombre_emisor'] ?? '') ?></td>

            <!-- Gravadas -->
            <td><?= number_format((float)$f['base_gravadas'], 2) ?></td>
            <td><?= number_format((float)$f['igv'], 2) ?></td>

            <!-- Exoneradas -->
            <td><?= number_format((float)$f['base_exoneradas'], 2) ?></td>
            <td>0.00</td>

            <!-- Inafectas -->
            <td><?= number_format((float)$f['base_inafectas'], 2) ?></td>
            <td>0.00</td>

            <!-- Gravadas no gravadas -->
            <td>0.00</td>

            <!-- Otros tributos -->
            <td>0.00</td>

            <!-- Total -->
            <td><?= number_format((float)$f['importe_total'], 2) ?></td>

            <!-- Tipo de cambio -->
            <td><?= ($f['moneda'] == 'USD') ? '3.80' : '1.00' ?></td>

            <!-- Referencias -->
            <td></td><td></td><td></td><td></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10" class="text-end">TOTALES:</td>

            <td><?= number_format($totales['base_gravadas'], 2) ?></td>
            <td><?= number_format($totales['igv_gravadas'], 2) ?></td>
            <td><?= number_format($totales['base_exoneradas'], 2) ?></td>
            <td>0.00</td>
            <td><?= number_format($totales['base_inafectas'], 2) ?></td>
            <td>0.00</td>
            <td><?= number_format($totales['no_gravadas'], 2) ?></td>
            <td><?= number_format($totales['otros_tributos'], 2) ?></td>
            <td><?= number_format($totales['importe_general'], 2) ?></td>

            <td></td>
            <td colspan="4"></td>
        </tr>
    </tfoot>
</table>

<div class="text-end mt-3">
   <button class="btn btn-success" onclick="exportPantallaExcel()">Exportar a Excel</button>
   <button class="btn btn-danger" onclick="exportPDF()">Exportar a PDF</button>
</div>

</div>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- AutoTable (aunque no se usa, lo dejamos si lo necesitas luego) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- XLSX + FileSaver -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


<script>
/* ============================================================
   EXPORTAR A PDF (captura completa con salto de páginas)
============================================================ */
function exportPDF() {

    const { jsPDF } = window.jspdf;
    const element = document.getElementById("capturaPDF");

    // Ocultar elementos no imprimibles
    const botones = document.querySelectorAll("button, .btn, .no-print");
    botones.forEach(b => b.style.display = "none");

    document.body.style.cursor = "wait";

    html2canvas(element, {
        scale: 2,
        useCORS: true,
        scrollY: -window.scrollY
    }).then(canvas => {

        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF("landscape", "pt", "a4");
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        const imgWidth = pageWidth - 20;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let y = 0;

        if (imgHeight <= pageHeight) {
            pdf.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
        } else {
            while (y < canvas.height) {

                let canvasPart = document.createElement("canvas");
                let ctx = canvasPart.getContext("2d");

                canvasPart.width = canvas.width;
                canvasPart.height = pageHeight * canvas.width / pageWidth;

                ctx.drawImage(
                    canvas,
                    0, y,
                    canvas.width, canvasPart.height,
                    0, 0,
                    canvas.width, canvasPart.height
                );

                let partImg = canvasPart.toDataURL("image/png");
                pdf.addImage(partImg, "PNG", 10, 10, imgWidth, pageHeight - 20);

                y += canvasPart.height;

                if (y < canvas.height) pdf.addPage();
            }
        }

        pdf.save("Registro_Compras_81.pdf");
        document.body.style.cursor = "default";

        botones.forEach(b => b.style.display = "");

    });
}


async function exportPantallaExcel() {

    const table = document.querySelector("#tablaCompras");
    const wb = XLSX.utils.book_new();

    // Convertir tabla a hoja base
    let ws = XLSX.utils.table_to_sheet(table);

    // Aplicar fusionado del encabezado SUNAT
    ws['!merges'] = [
        { s: { r: 0, c: 0 }, e: { r: 2, c: 0 } }, // N° registro
        { s: { r: 0, c: 1 }, e: { r: 2, c: 1 } },
        { s: { r: 0, c: 2 }, e: { r: 2, c: 2 } },

        // Comprobante de pago
        { s: { r: 0, c: 3 }, e: { r: 1, c: 5 } },

        // Info proveedor
        { s: { r: 0, c: 7 }, e: { r: 1, c: 9 } },

        // Adquisiciones
        { s: { r: 0, c: 10 }, e: { r: 0, c: 11 } },
        { s: { r: 0, c: 12 }, e: { r: 0, c: 13 } },
        { s: { r: 0, c: 14 }, e: { r: 0, c: 15 } },

        // Referencia
        { s: { r: 0, c: 20 }, e: { r: 1, c: 23 } },
    ];

    // FORMATO VISUAL – Bordes, alineación, tamaño
    const cellRange = XLSX.utils.decode_range(ws['!ref']);
    for (let R = cellRange.s.r; R <= cellRange.e.r; R++) {
        for (let C = cellRange.s.c; C <= cellRange.e.c; C++) {
            let cell = ws[XLSX.utils.encode_cell({ r: R, c: C })];
            if (cell) {
                cell.s = {
                    border: {
                        top: { style: "thin", color: "000000" },
                        left: { style: "thin", color: "000000" },
                        bottom: { style: "thin", color: "000000" },
                        right: { style: "thin", color: "000000" }
                    },
                    alignment: { vertical: "center", horizontal: "center", wrapText: true }
                };
            }
        }
    }

    // Autoajustar columnas según contenido
    ws['!cols'] = Array(cellRange.e.c).fill({ wch: 15 });

    // Insertar encabezado y totales antes de la tabla
const info = [
    ["<?= addslashes($empresa['razon_social']) ?>"],
    ["RUC: <?= $empresa['ruc'] ?>"],
    ["<?= $formato ?>"],
    ["PERIODO: <?= $nombreMes . ' ' . $anio ?>"],
    [""],
    ["Total Base Imponible: <?= number_format($totales['base_general'], 2) ?>  |  Total IGV: <?= number_format($totales['igv_general'], 2) ?>   |   Total General: <?= number_format($totales['importe_general'], 2) ?>"],
    [""]
];


    const headerSheet = XLSX.utils.aoa_to_sheet(info);

    // Combinar hoja final
    XLSX.utils.sheet_add_json(headerSheet, XLSX.utils.sheet_to_json(ws, { header: 1 }), {
        skipHeader: true,
        origin: "A8"
    });

    XLSX.utils.book_append_sheet(wb, headerSheet, "Registro Compras");

    XLSX.writeFile(wb, "Registro_Compras_SUNAT_8.1.xlsx");
}

</script>


</body>
</html>
