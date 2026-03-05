<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Auth (Ion Auth: login, Google Sign-In POST, logout)
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->post('auth/google-sign-in', 'Auth::googleSignIn');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/cambiar-contrasena', 'Auth::cambiarContrasena', ['filter' => 'checkLogin']);
$routes->post('auth/cambiar-contrasena', 'Auth::cambiarContrasena', ['filter' => 'checkLogin']);
$routes->get('auth/pendiente', 'Auth::pendiente');
$routes->get('auth/rechazado', 'Auth::rechazado');

// Notificaciones (solo Admin)
$routes->get('notificaciones', 'Notificaciones::index', ['filter' => 'checkLogin:admin']);
$routes->get('notificaciones/config', 'Notificaciones::config', ['filter' => 'checkLogin:admin']);
$routes->post('notificaciones/guardar-config', 'Notificaciones::guardarConfig', ['filter' => 'checkLogin:admin']);
$routes->get('notificaciones/contar-no-leidas', 'Notificaciones::contarNoLeidas', ['filter' => 'checkLogin:admin']);
$routes->get('notificaciones/estado-push', 'Notificaciones::estadoPush', ['filter' => 'checkLogin:admin']);
$routes->post('notificaciones/marcar-leida', 'Notificaciones::marcarLeida', ['filter' => 'checkLogin:admin']);
$routes->post('notificaciones/marcar-todas-leidas', 'Notificaciones::marcarTodasLeidas', ['filter' => 'checkLogin:admin']);
$routes->post('notificaciones/aprobar-usuario/(:num)', 'Notificaciones::aprobarUsuario/$1', ['filter' => 'checkLogin:admin']);
$routes->post('notificaciones/rechazar-usuario/(:num)', 'Notificaciones::rechazarUsuario/$1', ['filter' => 'checkLogin:admin']);
$routes->get('notificaciones/procesar-recordatorios', 'Notificaciones::procesarRecordatorios', ['filter' => 'checkLogin:admin']);

// Usuarios (solo Admin): gestión de usuarios que pueden entrar con Google
$routes->get('usuarios', 'Usuarios::index', ['filter' => 'checkLogin:admin']);
$routes->post('usuarios/listar', 'Usuarios::listar', ['filter' => 'checkLogin:admin']);
$routes->get('usuarios/obtener/(:num)', 'Usuarios::obtener/$1', ['filter' => 'checkLogin:admin']);
$routes->post('usuarios/guardar', 'Usuarios::guardar', ['filter' => 'checkLogin:admin']);
$routes->post('usuarios/restablecer-password/(:num)', 'Usuarios::restablecerPassword/$1', ['filter' => 'checkLogin:admin']);
$routes->post('usuarios/eliminar/(:num)', 'Usuarios::eliminar/$1', ['filter' => 'checkLogin:admin']);

// Dashboard
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');

// Módulos principales
$routes->get('transportistas', 'Transportistas::index');
$routes->get('transportistas/ver/(:num)', 'Transportistas::ver/$1');
$routes->post('transportistas/listar', 'Transportistas::listar');
$routes->get('transportistas/obtener/(:num)', 'Transportistas::obtener/$1');
$routes->post('transportistas/guardar', 'Transportistas::guardar');
$routes->post('transportistas/eliminar/(:num)', 'Transportistas::eliminar/$1');
$routes->get('transportistas/total', 'Transportistas::total');
$routes->get('transportistas/provinciasPorPais/(:num)', 'Transportistas::provinciasPorPais/$1');

// Documentos (transportistas y equipos)
$routes->get('documentos/listar/(:segment)/(:num)', 'Documentos::listar/$1/$2');
$routes->post('documentos/subir/(:segment)/(:num)', 'Documentos::subir/$1/$2');
$routes->get('documentos/ver/(:num)', 'Documentos::ver/$1');
$routes->post('documentos/eliminar/(:num)', 'Documentos::eliminar/$1');

// Equipos (listado de vehículos/equipos por patente, cisternas; controlador Equipos, modelo EquiposModel)
$routes->get('equipos', 'Equipos::index');
$routes->get('equipos/ver/(:num)', 'Equipos::ver/$1');
$routes->post('equipos/listar', 'Equipos::listar');
$routes->get('equipos/obtener/(:num)', 'Equipos::obtener/$1');
$routes->post('equipos/guardar', 'Equipos::guardar');
$routes->post('equipos/eliminar/(:num)', 'Equipos::eliminar/$1');
$routes->get('equipos/total', 'Equipos::total');
$routes->get('equipos/patentes', 'Equipos::patentes');
$routes->get('equipos/info-patente/(:segment)', 'Equipos::infoPatente/$1');
$routes->get('choferes', 'Choferes::index');
$routes->post('choferes/listar', 'Choferes::listar');
$routes->get('choferes/obtener/(:num)', 'Choferes::obtener/$1');
$routes->post('choferes/guardar', 'Choferes::guardar');
$routes->post('choferes/eliminar/(:num)', 'Choferes::eliminar/$1');
$routes->get('banderas', 'Banderas::index');
$routes->post('banderas/listar', 'Banderas::listar');
$routes->get('banderas/obtener/(:num)', 'Banderas::obtener/$1');
$routes->post('banderas/guardar', 'Banderas::guardar');
$routes->post('banderas/eliminar/(:num)', 'Banderas::eliminar/$1');
$routes->get('banderas/total', 'Banderas::total');
$routes->get('calibradores', 'Calibradores::index');
$routes->post('calibradores/listar', 'Calibradores::listar');
$routes->get('calibradores/obtener/(:num)', 'Calibradores::obtener/$1');
$routes->post('calibradores/guardar', 'Calibradores::guardar');
$routes->post('calibradores/eliminar/(:num)', 'Calibradores::eliminar/$1');
$routes->get('calibradores/total', 'Calibradores::total');
$routes->get('cubiertas', 'Cubiertas::index');
$routes->post('cubiertas/listar', 'Cubiertas::listar');
$routes->get('cubiertas/obtener/(:num)', 'Cubiertas::obtener/$1');
$routes->post('cubiertas/guardar', 'Cubiertas::guardar');
$routes->post('cubiertas/eliminar/(:num)', 'Cubiertas::eliminar/$1');
$routes->get('cubiertas/total', 'Cubiertas::total');
$routes->get('marcas', 'Marcas::index');
$routes->post('marcas/listar', 'Marcas::listar');
$routes->get('marcas/obtener/(:num)', 'Marcas::obtener/$1');
$routes->post('marcas/guardar', 'Marcas::guardar');
$routes->post('marcas/eliminar/(:num)', 'Marcas::eliminar/$1');
$routes->get('marcas/total', 'Marcas::total');
$routes->get('marcas-sensor', 'MarcasSensor::index');
$routes->post('marcas-sensor/listar', 'MarcasSensor::listar');
$routes->get('marcas-sensor/obtener/(:num)', 'MarcasSensor::obtener/$1');
$routes->post('marcas-sensor/guardar', 'MarcasSensor::guardar');
$routes->post('marcas-sensor/eliminar/(:num)', 'MarcasSensor::eliminar/$1');
$routes->get('marcas-sensor/total', 'MarcasSensor::total');
$routes->get('nacion', 'Nacion::index');
$routes->post('nacion/listar', 'Nacion::listar');
$routes->get('nacion/obtener/(:num)', 'Nacion::obtener/$1');
$routes->post('nacion/guardar', 'Nacion::guardar');
$routes->post('nacion/eliminar/(:num)', 'Nacion::eliminar/$1');
$routes->get('nacion/total', 'Nacion::total');
$routes->get('inspectores', 'Inspectores::index');
$routes->post('inspectores/listar', 'Inspectores::listar');
$routes->get('inspectores/obtener/(:num)', 'Inspectores::obtener/$1');
$routes->post('inspectores/guardar', 'Inspectores::guardar');
$routes->post('inspectores/eliminar/(:num)', 'Inspectores::eliminar/$1');
$routes->get('inspectores/total', 'Inspectores::total');
$routes->get('inspectores/opciones', 'Inspectores::opciones');
$routes->get('items-censo', 'ItemsCenso::index');
$routes->post('items-censo/listar', 'ItemsCenso::listar');
$routes->get('items-censo/obtener/(:num)', 'ItemsCenso::obtener/$1');
$routes->post('items-censo/guardar', 'ItemsCenso::guardar');
$routes->post('items-censo/eliminar/(:num)', 'ItemsCenso::eliminar/$1');
$routes->get('items-censo/total', 'ItemsCenso::total');
$routes->get('tipos-cargamentos', 'TiposCargamentos::index');
$routes->post('tipos-cargamentos/listar', 'TiposCargamentos::listar');
$routes->get('tipos-cargamentos/imprimir', 'TiposCargamentos::imprimir');
$routes->get('reglas', 'Reglas::index');
$routes->post('reglas/listar', 'Reglas::listar');
$routes->get('reglas/obtener/(:num)', 'Reglas::obtener/$1');
$routes->post('reglas/guardar', 'Reglas::guardar');
$routes->post('reglas/eliminar/(:num)', 'Reglas::eliminar/$1');
$routes->get('reglas/total', 'Reglas::total');
$routes->get('calibracion', 'Calibracion::index');
$routes->post('calibracion/listar', 'Calibracion::listar');
$routes->get('calibracion/obtener/(:num)', 'Calibracion::obtener/$1');
$routes->get('calibracion/ultima-por-patente', 'Calibracion::ultimaPorPatente');
$routes->post('calibracion/guardar', 'Calibracion::guardar');
$routes->post('calibracion/eliminar/(:num)', 'Calibracion::eliminar/$1');
$routes->get('calibracion/imprimir/(:num)', 'Calibracion::imprimir/$1', ['filter' => 'checkLogin:admin']);
$routes->get('calibracion/informe-carga-segura/(:num)', 'Calibracion::informeCargaSegura/$1');
$routes->post('calibracion/guardar-informe-carga-segura/(:num)', 'Calibracion::guardarInformeCargaSegura/$1');
$routes->get('calibracion/imprimir-informe-carga-segura/(:num)', 'Calibracion::imprimirInformeCargaSegura/$1', ['filter' => 'checkLogin:admin']);
$routes->post('calibracion/registrar-reimpresion', 'Calibracion::registrarReimpresion', ['filter' => 'checkLogin:admin']);
// Vista pública por token (sin login) - solo lectura + registro de accesos
$routes->get('calibracion/ver/(:segment)', 'Calibracion::ver/$1');
$routes->get('calibracion/multiflecha/(:num)/(:num)/(:num)', 'Calibracion::getMultiflecha/$1/$2/$3');
$routes->get('calibracion/multiflecha/(:num)/(:num)', 'Calibracion::getMultiflecha/$1/$2');
$routes->post('calibracion/multiflecha-guardar', 'Calibracion::guardarMultiflecha');
$routes->get('calibracion/notas/(:num)', 'Calibracion::getNotas/$1');
$routes->post('calibracion/notas-guardar', 'Calibracion::guardarNotas');
$routes->get('calibradores/opciones', 'Calibradores::opciones');

// Reportes
$routes->get('reportes', 'Reportes::index');
$routes->get('reportes/calibraciones', 'Reportes::calibraciones');
$routes->post('reportes/listar-calibraciones', 'Reportes::listarCalibraciones');
$routes->get('reportes/exportar-calibraciones-csv', 'Reportes::exportarCalibracionesCsv');
$routes->get('reportes/vencimientos', 'Reportes::vencimientos');
$routes->post('reportes/listar-vencimientos', 'Reportes::listarVencimientos');
$routes->get('reportes/exportar-vencimientos-csv', 'Reportes::exportarVencimientosCsv');
$routes->get('reportes/flota', 'Reportes::flota');
$routes->post('reportes/listar-flota', 'Reportes::listarFlota');
$routes->get('reportes/exportar-flota-csv', 'Reportes::exportarFlotaCsv');
$routes->get('reportes/transportistas', 'Reportes::transportistas');
$routes->post('reportes/listar-transportistas', 'Reportes::listarTransportistas');
$routes->get('reportes/exportar-transportistas-csv', 'Reportes::exportarTransportistasCsv');




