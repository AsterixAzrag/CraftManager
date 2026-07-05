# Plan del proyecto

## Objetivo

Desarrollar una plataforma web en Laravel con PostgreSQL para administrar pedidos personalizados, clientes, productos, materiales, inventario, produccion y reportes administrativos.

## Stack recomendado

- Backend: Laravel 12
- Base de datos: PostgreSQL
- Frontend inicial: Blade, Vite y Tailwind CSS
- Autenticacion: Laravel Breeze o autenticacion propia con sesiones
- Reportes: consultas agregadas en Laravel y vistas administrativas

## Fases de desarrollo

### Fase 1: Base del sistema

- Configurar Laravel y PostgreSQL.
- Definir migraciones principales.
- Implementar autenticacion.
- Crear layout administrativo.
- Crear roles basicos: administrador, produccion y ventas.

### Fase 2: Catalogos principales

- Empleados y usuarios.
- Clientes.
- Productos y servicios.
- Materiales.

### Fase 3: Pedidos personalizados

- Registro de pedidos.
- Detalle de productos por pedido.
- Caracteristicas personalizadas.
- Estados del pedido: registrado, en produccion, listo, entregado, cancelado.
- Historial por cliente.

### Fase 4: Inventario

- Entradas de materiales.
- Salidas por uso en produccion.
- Ajustes manuales.
- Stock minimo.
- Alertas de stock bajo.

### Fase 5: Produccion y agenda

- Actividades de produccion por pedido.
- Asignacion a empleados.
- Fechas de entrega.
- Calendario de trabajo.
- Seguimiento de carga de trabajo.

### Fase 6: Reportes

- Ventas por periodo.
- Pedidos pendientes.
- Materiales mas utilizados.
- Inventario bajo.
- Carga de trabajo por empleado.

## Primer MVP

El MVP debe permitir iniciar sesion, registrar clientes, productos, materiales y pedidos, consultar pedidos pendientes y actualizar estados de produccion.

