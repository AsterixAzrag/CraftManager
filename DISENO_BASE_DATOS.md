# Diseno inicial de base de datos

## Tablas principales

### users

Usuarios que acceden al sistema.

- id
- name
- email
- password
- role
- phone
- active

### clients

Clientes del negocio.

- id
- name
- phone
- email
- address
- notes

### products

Catalogo de productos o servicios personalizados.

- id
- name
- description
- base_price
- active

### materials

Materiales usados en produccion.

- id
- name
- unit
- current_stock
- minimum_stock
- unit_cost
- active

### inventory_movements

Historial de entradas, salidas y ajustes de inventario.

- id
- material_id
- user_id
- type
- quantity
- unit_cost
- reason
- reference_type
- reference_id

### orders

Pedidos personalizados.

- id
- client_id
- user_id
- folio
- status
- order_date
- due_date
- delivered_at
- subtotal
- discount
- total
- notes

### order_items

Productos incluidos en un pedido.

- id
- order_id
- product_id
- description
- quantity
- unit_price
- total
- customization_details

### order_item_materials

Materiales estimados o consumidos por cada producto del pedido.

- id
- order_item_id
- material_id
- quantity
- cost

### production_tasks

Actividades de produccion asociadas a pedidos.

- id
- order_id
- assigned_to
- title
- description
- status
- start_date
- due_date
- completed_at

### business_settings

Configuracion general del negocio.

- id
- business_name
- phone
- email
- address
- currency

