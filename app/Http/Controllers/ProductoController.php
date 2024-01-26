<?php

namespace App\Http\Controllers;
use App\http\Responses\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Producto;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    public function index()
    {
        try {
            // CON EL METODO WITH se trae el producto con la informacion de la marca y la categoria 
          // solo para pruebas para que no sea tan pesada la busqueda en grandes cantidades de registros 
            // $productos = Producto::with('marca','categoria')->get();
           // obtenemos todos nuestros productos
            $productos = Producto::all();
            return ApiResponse::success('lista de productos ',200,$productos);
        } catch (Exception $th) {
            return ApiResponse::error('error al obtener la lista de productos',$th->getMessage(), $productos,500);

        }
    }
// en este metodo validamos los datos
    public function store(Request $request)
    {
        try {
            $request->validate([
          'nombre'=>'required|unique:productos',
          'precio'=>'required|numeric|between:0,999999,99',
          'cantidad_disponible'=>'required|integer',
          'categoria_id'=>'required|exists:categorias,id',//validamos que el id de categorias exista 
          'marca_id'=>'required|exists:marcas,id',
            ]);
            //registramos el producto en nuestra tabla atravez del metodo create
            $producto = Producto::create($request->all());
            // se retorna la respuesta afirmativa
            return ApiResponse::success('producto creado exitosamente',201,$producto);

        } catch (ValidationException $e) {
//se guardan todos los errores en $errors
// llamamos al metodo validator y invocamos todos los errores y se guardan en un array errors()->toArray()
            $errors = $e->validator->errors()->toArray();
            // en esta condicional se valida si los $errors  en el atributo de la categoria el id
            // error se le asigna una nueva llave  para que muestre todos los errores que tenga la categoria
            if (isset($errors['categoria_id'])) {
                $errors['categoria']=$errors['categoria_id'];
                // se elimina la llave que no nesecitamos  por que lla esta asignada en otra variable
                unset($errors['categoria_id']);
            }
            
             if (isset($errors['marca_id'])) {
                $errors['marca']=$errors['marca_id'];
                unset($errors['marca_id']);
            }
            // se retorna error de validacion y se le pasa el array de errores  $errors
            return ApiResponse::error('error de validacion',422,$errors);

        }
    }

    public function show($id)
    {
       try {
        // se llama al modelo producto  y le pasamos el parametro id que se recibe public function show($id)
       $producto = Producto::with('marca','categoria')->findOrFail($id);
        return ApiResponse::success('producto obtenido exitosamente',201,$producto);
// se valida   ModelNotFoundException este metodo cuando no se encuentra el registro 
       } catch (ModelNotFoundException $e) {
        return ApiResponse::error('producto no encontrado', 404);
       }
    }

    public function update(Request $request, $id)
    {
     try {
        // se busca el producto y se valida 
        $producto = Producto::findOrFail($id);
        $request->validate([
          'nombre'=>'required|unique:productos,nombre,'.$producto->id,// se valida asi productos,nombre,y se concatena el id del producto 
          'precio'=>'required|numeric|between:0,999999,99', //
          'cantidad_disponible'=>'required|integer',
          'categoria_id'=>'required|exists:categorias,id',
          'marca_id'=>'required|exists:marcas,id',
            ]);
            // al producto le pasamos el metodo update y el request para pasar todos los parametros 
            $producto->update($request->all());
            return ApiResponse::success('producto actualizado exitosamente',200,$producto);

     } catch (ValidationException $e) {
        $errors= $e->validator->errors()->toArray();
        if (isset($errors['categoria_id'])) {
            $errors['categoria']=$errors['categoria_id'];
            unset($errors['categoria_id']);
        }
         if (isset($errors['marca_id'])) {
            $errors['marca']=$errors['marca_id'];
            unset($errors['marca_id']);
        }
        return ApiResponse::error('errores  de validacion',422,$errors);
        //se agrega otra validacion extra 
// se captura el ModelNotFoundException para cuando se busca el producto y no se encuentra  
     } catch(ModelNotFoundException  $e){
return ApiResponse::error('producto no encontrado',404);
     }
    }

    public function destroy($id)
    {
        try {
              // se busca el producto que se quiere borrar y se valida 
            
            $producto = Producto::findOrFail($id);
            //se llama al producto encontrado y le pasamos el metodo delete 
            $producto->delete();
            return ApiResponse::success('producto eliminado exitosamente',200);
         } catch (ModelNotFoundException $e) {
           return ApiResponse::error('producto no encontrado',404);
         }
    }
}
