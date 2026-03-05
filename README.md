# Sistema de Gestión de Montajes y Calibraciones

Sistema desarrollado con CodeIgniter 4 basado en la estructura del proyecto Itdelfoscampus.

## Estructura del Proyecto

El proyecto incluye los siguientes módulos:

- **Dashboard**: Página principal con resumen del sistema
- **Transportistas**: Gestión de transportistas
- **Equipos**: Gestión de equipos (flota: tractor + semi, patentes, cisternas)
- **Banderas**: Gestión de banderas
- **Calibradores**: Gestión de calibradores
- **Cubiertas**: Gestión de cubiertas
- **Marcas**: Gestión de marcas
- **Nación**: Gestión de nación
- **Calibración**: Gestión de calibraciones

## Requisitos

- PHP 8.1 o superior
- Composer
- Servidor web (Apache/Nginx) o WAMP/XAMPP

## Instalación

1. Instalar dependencias:
```bash
composer install
```

2. Copiar los archivos de assets del proyecto base (Itdelfoscampus) a `public/assets/`

3. Configurar la base de datos en `app/Config/Database.php`

4. Configurar la URL base en `app/Config/App.php`

## Estructura de Directorios

```
montajes-campana/
├── app/
│   ├── Config/
│   ├── Controllers/
│   └── Views/
│       ├── layout/
│       ├── dashboard/
│       ├── transportistas/
│       ├── equipos/
│       ├── banderas/
│       ├── calibradores/
│       ├── cubiertas/
│       ├── marcas/
│       ├── nacion/
│       └── calibracion/
├── public/
│   └── assets/
└── writable/
```

## Tecnologías Utilizadas

- CodeIgniter 4
- Bootstrap 5
- jQuery
- DataTables
- Luno Style Theme




