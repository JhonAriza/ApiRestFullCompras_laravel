###   INTRODUCCION

Esta es un api se usa Responses para  manejar las respuestas , podemos  crear marcas ,categorias ,productos y realizar compras, maneja operaciones para actualizar las tablas.  
###  INSTALACION 

- git clone  
- cd ApiRestFullCompras_laravel
- Instalar las dependencias del proyecto con: 
###### composer install
- Configurar el archivo .env.example y dejarlo como .env y dentro colocar todas las variables de entorno de nuestro proyecto.
- Creamos la base de datos para nuestro proyecto.
- Generar una APP_KEY que es una llave para cada proyecto de Laravel se puede generar con este comando:
###### php artisan key:generate
- Generar las migraciones y ejecutar los seeders para nuestras tablas de base de datos con este comando:
###### php artisan migrate --seed

## USO
- se puede utilizar  Postman

se crean categorias atraves del metodon POST
URL:
http://localhost:8000/api/categorias
JSON:
{
    "nombre":"leches",
    "descripcion":"categoria para todo lo derivado de vacas lecheras"
}

 ----------- 

 
Tambien se crean las marcas metodo POST
URL:
http://localhost:8000/api/marcas
JSON:
{
    "nombre":"alpina",
    "descripcion":"categoria para marcas de leche alpina
}

 ---------

se crea el producto con el metodo post
URL:
http://localhost:8000/api/productos
JSON:
{
   "nombre": "leche doña leche",
    "descripcion": "esta es una leche de vaca",
    "precio": "54",
    "cantidad_disponible": "100",
    "categoria_id": "1",
    "marca_id": "1"

}
------------

se valida la creacion de marcas y categorias 
http://localhost:8000/api/categorias
http://localhost:8000/api/marcas

---------------------------------

se realiza la compra del producto atraves del metodo POST 
no se permiten productos duplicados para la compra
URL:
 http://localhost:8000/api/compras
 {
   "productos":
       {
       "producto_id":1,
       "cantidad":2
       }
}


Busquedas
------------

metodo get 
- http://localhost:8000/api/categorias 
- http://localhost:8000/api/productos
- en esta ruta se busca todos  productos por categoria con el metodo get
ejemplo se busca categoria 1 todos los productos que tenga esta
URL http://localhost:8000/api/categorias/1/productos
