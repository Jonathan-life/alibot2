<!-- app/Views/reportes/venta_sire -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Empresas</title>

  <!-- Bootstrap -->
<!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="<?= base_url('css/index.css') ?>">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 py-3">
  <div class="navbar-logo-container">
    <a class="navbar-brand fw-bold ms-4" href="#">
      <img src="<?= base_url('/img/logcounting.png') ?>" alt="Logo" style="height:60px;">
    </a>
  </div>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="menuNav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <a class="nav-link active" href="<?= base_url() ?>">Inicio</a>
      </li>

      <!-- Mantenimiento -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Mantenimiento</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="<?= base_url('mantenimiento/usuario') ?>">Usuarios</a></li>
          <li><a class="dropdown-item" href="<?= base_url('mantenimiento/empresa') ?>">Empresas</a></li>
          <li><a class="dropdown-item" href="<?= base_url('mantenimiento/sunat-og') ?>">Descargar</a></li>
          <li class="dropdown-submenu">
            <a class="dropdown-item dropdown-toggle" href="#">Permisos</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= base_url('permisos/usuario') ?>">Usuarios</a></li>
              <li><a class="dropdown-item" href="<?= base_url('permisos/empresa') ?>">Empresas</a></li>
            </ul>
          </li>
        </ul>
      </li>

      <!-- Reportes -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Reportes</a>
        <ul class="dropdown-menu">
          <li class="dropdown-submenu">
            <a class="dropdown-item dropdown-toggle" href="#">SUNAT</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Buzón Electrónico</a></li>
              <li><a class="dropdown-item" href="<?= base_url('reportes/libro_contable') ?>">Libros Electrónicos</a></li>
              <li class="dropdown-submenu">
                <a class="dropdown-item dropdown-toggle" href="#">Compras SIRE</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="<?= base_url('reportes/venta_sire') ?>">PDF</a></li>
                  <li><a class="dropdown-item" href="<?= base_url('reportes/ventasxml') ?>">XML</a></li>
                </ul>
              </li>
              <li class="dropdown-submenu">
                <a class="dropdown-item dropdown-toggle" href="#">Ventas SIRE</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">PDF</a></li>
                  <li><a class="dropdown-item" href="#">XML</a></li>
                </ul>
              </li>
              <li><a class="dropdown-item" href="<?= base_url('reportes/admin_contable') ?>">Cuadro de cálculo</a></li>
            </ul>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="#">Requerimientos</a>
      </li>
    </ul>

    <div class="navbar-icons-container iconos-navbar d-flex gap-3 ms-auto">
      <a href="#" class="icon-link" title="Cerrar sesión"><i class="fas fa-power-off"></i></a>
      <a href="#" class="icon-link" title="Mi perfil"><i class="fas fa-user-circle"></i></a>
    </div>
  </div>
</nav>

<!-- Contenido principal -->
<div class="container mt-5">
  <h2 class="text-center mb-4">Empresas Registradas</h2>
  <div class="row">
    <?php if (!empty($empresas)): ?>
      <?php foreach ($empresas as $empresa): ?>
        <div class="col-md-4 mb-4">
          <div class="card border-primary shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= esc($empresa['razon_social']) ?></h5>
              <p class="card-text"><strong>RUC:</strong> <?= esc($empresa['ruc']) ?></p>
             <a href="<?= base_url('reportes/facturas_empresa/' . $empresa['id_empresa']) ?>" class="btn btn-sm btn-outline-primary">Ver facturas</a>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No hay empresas activas.</p>
    <?php endif; ?>
  </div>

  <div class="text-center mt-4">
    <a href="<?= base_url() ?>" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Volver</a>
  </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
