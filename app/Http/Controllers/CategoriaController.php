<?php

namespace App\Http\Controllers;
use App\http\Responses\ApiResponse; // hace referencia al archivo de  CLASE responses para manejar las respuestas de esta api
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
 

    public function index()
    {
        try{ 
          //  throw new Exception("error al obtener categorias ");
          // se obtienen  las categorias
            $categorias = Categoria::all();
            // se pasa el mensaje , se pasa el status y la data que seria categorias
           return ApiResponse::success('lista de categorias',200,$categorias);
        } catch (Exception $e) {
           
            return ApiResponse::error('ha ocurrido un error '.$e->getMessage(),500);
        }
    }
    public function store(Request $request)
    {
        try {
          $request->validate([
            'nombre'=>'required|unique:categorias'
          ]);
          $categoria= Categoria::create($request->all());
          return ApiResponse::success('categoria creada exitosamente  ', 201, $categoria);

        } catch (ValidationException $e) {
            // no se debe enviar el error de $e por que se da mucha informacion en caso de hackeo
          //  return ApiResponse::error('error validacion '.$e->getMessage(),422);
            return ApiResponse::error('error validacion ',422);
        }
    }

    public function show($id)
    {
       try {
        $categoria = Categoria::findOrFail($id);
        return ApiResponse::success('categoria encontrada exitosamente', 200, $categoria);
       } catch (\Throwable $th) {
         return ApiResponse::error('categoria no encontrada',404);
       }
    }

    public function update(Request $request, $id)
    {
    try {
        $categoria = Categoria::findOrFail($id);
        $request->validate([
            'nombre' => ['required', Rule::unique('categorias')->ignore($categoria)] // regla para validar 
          ]);
          $categoria->update($request->all());
          return ApiResponse::success('categoria actualizada exitosamente', 200, $categoria);
    } catch (ModelNotFoundException $e) {
         return  ApiResponse::error('cattegoria no encontrada',404);
    } catch (Exception $e){
        return ApiResponse::error('error: '.$e->getMessage(),422);


    }
    }

    public function destroy($id)
    {
     try {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return ApiResponse::success('categoria eliminada exitosamente',200);
     } catch (ModelNotFoundException $e) {
       return ApiResponse::error('categoria no encontrada',404);
     }
    }
// metodo para traer todos los productos asociados a las categorias 
    public function productosPorCategoria($id)
    {try {
      $categoria = Categoria::with('productos')->findOrFail($id);
      return ApiResponse::success('Categoria y lista de productos',200,$categoria);
    } 
   
        catch (ModelNotFoundException $e) {
          return ApiResponse::error('categoria no encontrada',404);
        }
    }
}
