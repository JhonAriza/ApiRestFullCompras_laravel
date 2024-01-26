<?php 
namespace App\Http\Responses;
class ApiResponse
{
    //este metodo  success para mostrar que todo se ha ejecutado correcto
    // este metodo recibe esos parametros  , casi siempre se maneja el status 200
    // en caso de que sea vacio se puede poner esos valores por defecto
    public static function success($message = 'Success',$statusCode = 200,$data = [])
    {
       // este metodo devuelve devuelve la respuesta de tipo json
       // esta es la estructura que quiero que devuelva la api 
        return response()->json([
            'message'=>$message,
            'statusCode'=>$statusCode,
            'error'=>false,
            'data'=>$data // contiene la informacion que se va a mostrar 
        ],$statusCode  ); // se pasa para que muestre el status code 
    }
//esta funcion 
    public static function error($message = 'Error',$statusCode,$data = [])
    {
        return response()->json([
            'message'=>$message,
            'statusCode'=>$statusCode,
            'error'=>true,
            'data'=>$data
        ],$statusCode ); 
    }
   }