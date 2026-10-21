# Distribuidora de lácteos — CodeIgniter 4 + MySQL

Sistema con login para cargar clientes, ventas y pagos, y ver cuánto debe cada cliente.

## Qué incluye
- **Login** con usuario y contraseña (no hay registro público; el usuario se crea por comando).
- **Clientes**: alta, edición, búsqueda, ver qué se le vendió, cuánto, cuánto pagó y cuánto debe.
- **Ventas**: varios productos por venta, precio editable en el renglón, pago al momento (total, parcial o nada).
- **Pagos**: se registran sobre cada venta (parciales o totales). El saldo se calcula solo.
- **Productos y precios**: agregar, editar precio, pausar y quitar. Las ventas viejas conservan el precio con que se vendieron.

## Probar en tu computadora (PHP 8.2+ con intl, mbstring y mysqli)
1. Crear una base vacía en MySQL llamada `distribuidora`.
2. Copiar `env` como `.env` y completar usuario/clave de MySQL.
3. Crear tablas y productos iniciales:
   ```
   php spark migrate
   php spark db:seed ProductosSeeder
   php spark usuario:crear
   ```
4. Levantar el sitio: `php spark serve` y abrir http://localhost:8080

## Publicar: base en Clever Cloud
1. Crear un add-on **MySQL** en Clever Cloud y copiar host, puerto, base, usuario y contraseña.
2. Poner esos datos en tu `.env` local y correr **los mismos 3 comandos** de arriba (`migrate`, `db:seed`, `usuario:crear`). Eso crea las tablas en Clever Cloud.

## Publicar: sitio en Vercel
1. Subir el proyecto a GitHub (el `.env` no se sube, ya está en `.gitignore`).
2. En Vercel: *Add New > Project* e importar el repositorio.
3. En *Settings > Environment Variables* cargar:
   `CI_ENVIRONMENT=production`, `APP_BASE_URL=https://TU-PROYECTO.vercel.app/`,
   `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
4. Deploy.

Notas: Vercel no soporta PHP de forma oficial; se usa el runtime de la comunidad `vercel-php` (ver `vercel.json`). Las sesiones se guardan en MySQL (tabla `ci_sessions`) porque Vercel no tiene disco persistente.
