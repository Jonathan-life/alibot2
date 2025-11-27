<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('empresa', 'EmpresaController::index');        // Vista empresas
$routes->get('api/empresas', 'EmpresaController::listar'); // API listar empresas
$routes->post('api/empresa/eliminar', 'EmpresaController::eliminar'); // API eliminar empresa
$routes->post('api/empresa/registrar', 'RegistroController::registrar'); // API registrar
$routes->get('api/empresa/consultaRuc', 'RegistroController::consultaRuc'); // API consulta RUC

$routes->get('mantenimiento/empresa', 'EmpresaController::verEmpresas');
$routes->get('mantenimiento/dashboard/(:num)', 'DashboardController::ver/$1');



$routes->get('sunat', 'BotController::index');   // muestra formulario
$routes->post('api/bot/ejecutar', 'BotController::ejecutar'); // ejecuta Python
$routes->get('mantenimiento/sunat-og', 'BotController::index');
$routes->get('reportes/venta_sire', 'EmpresaController::listarActivas');


$routes->get('reportes/ventasxml', 'EmpresaController::listarActivasxml');
$routes->get('reportes/facturas_empresaxml/(:num)', 'FacturasController::listarActivasxml/$1');


$routes->get('reportes/facturas_empresa/(:num)', 'FacturasController::listarCompras/$1');

$routes->get('descarga/(:num)', 'Descarga::index/$1');
$routes->get('descarga', 'Descarga::index');


$routes->get('reportes/libro_contable', 'EmpresaController::listarTodas');

$routes->get('api/get_periodos', 'EmpresaController::get_periodos');

$routes->get('reportes/listado_empresas', 'FacturaController::libroContable');

// En app/Config/Routes.php

$routes->get('reportes/empresas_ventas', 'FacturaController::empresasVentas');

