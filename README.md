El backend está dockerizado: 
Desde consola ejecutar:
- cd backend
- composer install
- docker compose up -d
- cd ..

En docker, desde nsign symfony:
- php bin/console doctrine:database:create
- php bin/console doctrine:migrations:migrate (confirm)
- php bin/console doctrine:fixtures:load (confirm)

En frontend:
- cd frontend
- yarn install
- yarn quasar dev

Acceder a la web en http://localhost:9000/

Crear un usuario en Registro. Ese primer usuario tiene asignado rol Admin. Los que se creen a continuación serán usuarios normales.

Observaciones: 
- La búsqueda funciona por todos los campos excepto nombre de imagen, aunque incluyo en un comentario cómo hacer para que solo se haga por ID y nombre de producto.
- Falta validación en la edición del producto (está en la creación de usuario, aunque muy básica).
- Falta validación en el backend
