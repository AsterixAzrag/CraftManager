# proyecto-pedidos

Plataforma web en Laravel y PostgreSQL para administrar pedidos personalizados, clientes, productos, materiales, inventario y produccion.

## Modulos principales

- Acceso y autenticacion
- Empleados y roles
- Clientes
- Productos
- Materiales e inventario
- Pedidos personalizados
- Agenda y produccion
- Configuracion del negocio
- Reportes administrativos

## Configuracion inicial

1. Crear una base de datos PostgreSQL llamada `proyecto_pedidos`.
2. Configurar usuario y contrasena en `.env`.
3. Ejecutar migraciones:

```bash
php artisan migrate
```

## Desarrollo

Servidor Laravel:

```bash
php artisan serve
```

Frontend con Vite:

```bash
npm run dev
```
