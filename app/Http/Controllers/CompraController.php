<?php

namespace App\Http\Controllers;
use App\http\Responses\ApiResponse;
use App\Models\Compra;
use App\Models\Producto;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
 
class CompraController extends Controller
{
    public function index(){
        # code
    }

    public function store(Request $request)
    {
        try {
            $productos = $request->input('productos');
            // VALIDAR los productos
                if(empty($productos)){
                    return ApiResponse::error('no se proporsionaron productos',400);
                }
                // validar la lista de productos
                // con este metodo se hace las validaciones validator
                  $validator = validator::make($request->all(),[
                    'productos'=>'required|array', // se valida que sea requerido y que sea nun arrray
                    'productos.*.producto_id'=>'required|integer|exists:productos,id',//se valida el producto.id que sea entero y que exista 
                    'productos.*.cantidad'=>'required|integer|min:1' //que es requerido y sea entero y tenga minimo 1 
                  ]);
                  //si ese validator trae algo que no este bien  va a retornar un error
                  if($validator->fails()){
                    //$validator->errors() este metodo nos indica todos los errores que encuentre en la validacion 
                    return ApiResponse::error('datos no validos',400,$validator->errors());
                  }

                  // validar productos duplicados 
                  //en esta variable $productoIds 
                  // se guardan todos los id de los productos 
$productoIds = array_column($productos,'producto_id');
// se valida que no exista el id del producto que esta validando ejemplo dos productos con id 1 falla la validacion
// esta condicional cuenta 2 veces los array pero en la segunda condicion solo los id unicos
if (count($productoIds) !== count(array_unique($productoIds))) {
    return ApiResponse::error('no se permiten productos duplicados para la compra',400);
    
}
// se inbicializan las variables para la compra 
$totalPagar=0;
$subtotal = 0;
$compraItems=[];


//iteracion de los productos para hacer la factura
foreach($productos as $producto){
// se guarda cada producto 
    $productoB = Producto::find($producto['producto_id']);
    // if(!$productoB){
    //     return ApiResponse::error('producto no encontrado ',404);
    // }
    // validar la cantidad disponible de los productos
    // si  cantidad disponible es menor que el producto cantidad muestra el error 
    if ($productoB->cantidad_disponible < $producto['cantidad']) {
        return ApiResponse::error('el producto  cantidad no  disponible  ',404);
    }
    //// actualizacion de la cantidad disponible de cada producto
    // se resta de la cantidad disponible de la compra
    $productoB->cantidad_disponible -= $producto['cantidad'];
    $productoB->save();

    // calculo de los importes 
    //
     $subtotal = $productoB->precio * $producto['cantidad'];
     // se guarda la suma de el precio de los productos
     $totalPagar += $subtotal;
     //guardar item s de la compra
// se guarda en este array todos los items   hace la iteracion coin todos los productos de la compra para guardarlos
 $compraItems[] = [
'producto_id' => $productoB->id,
'precio'=> $productoB->precio,
'cantidad'=>$producto['cantidad'],
'subtotal'=>$subtotal
     ];
}
// registro enla tabla compra
$compra = Compra::create([
'subtotal' => $totalPagar,
'total' => $totalPagar
]);
// asociar los productos ala compra con sus cantidades attach  metodo para guardar  , asignar registros de tablas intermedias
// accedemos a compra y mediante las relaciones llamo al metodo de producto() que esta en el modelo
 $compra->productos()->attach($compraItems);

return ApiResponse::success('compra realizada exitosamente ',201, $compra);

 
        } catch (QueryException $e) {
            // QueryException muestra los errores en la consulta en la base de datos 
            return ApiResponse::error('error en la consulta de base de datos',500);
        } catch (Exception $e) {
            return ApiResponse::error('error  inesperado',500);
        }
    }

    public function show($id)
    {
        # code
    }
}
