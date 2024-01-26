<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Marca;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class MarcaController extends Controller
{
    public function index()
    {
        try{ 
          //  throw new Exception("error al obtener categorias ");
            $marcas = Marca::all();
           return ApiResponse::success('lista de marcas',200,$marcas);
        } catch (Exception $e) {
           
            return ApiResponse::error('ha ocurrido un error '.$e->getMessage(),500);
        }
    }

    public function store(Request $request)
    {
        try {
          $request->validate([
            'nombre'=>'required|unique:marcas'
          ]);
          $marca= Marca::create($request->all());
          return ApiResponse::success('marca creada exitosamente  ', 201, $marca);

        } catch (ValidationException $e) {
            // no se debe enviar el error de $e por que se da mucha informacion en caso de hackeo
           return ApiResponse::error('error validacion '.$e->getMessage(),422);
            return ApiResponse::error('error validacion ',422);
        }
    }

    public function show($id)
    {
       try {
        $marca = Marca::findOrFail($id);
        return ApiResponse::success('marcxa encontrada exitosamente', 200, $marca);
       } catch (\Throwable $th) {
         return ApiResponse::error('marca no encontrada',404);
       }
    }

    public function update(Request $request, $id)
    {
    try {
        $marca = Marca::findOrFail($id);
        $request->validate([
            'nombre' => ['required', Rule::unique('marcas')->ignore($marca)]
          ]);
          $marca->update($request->all());
          return ApiResponse::success('marca actualizada exitosamente', 200, $marca);
    } catch (ModelNotFoundException $e) {
         return  ApiResponse::error('marca no encontrada',404);
    } catch (Exception $e){
        return ApiResponse::error('error: '.$e->getMessage(),422);


    }
    }

    public function destroy($id)
    {
     try {
        $marca = Marca::findOrFail($id);
        $marca->delete();
        return ApiResponse::success('marca eliminada exitosamente',200);
     } catch (ModelNotFoundException $e) {
       return ApiResponse::error('marca no encontrada',404);
     }
    }

    public function productosPorMarca($id)
    
    {try {
      $marca = Marca::with('productos')->findOrFail($id);
      return ApiResponse::success('marcas y lista de productos',200,$marca);
    } 
   
        catch (ModelNotFoundException $e) {
          return ApiResponse::error('marca no encontrada',404);
        }
    }
}
