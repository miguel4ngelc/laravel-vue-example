<?php

namespace app\Http\Controllers\OficinaVirtual;

use App\Http\Controllers\Controller;
use App\Http\Requests\OficinaVirtual\PedidoAdjuntoStoreRequest;
use App\Models\OficinaVirtual\Pedido;
use App\Models\OficinaVirtual\PedidoAdjunto;
use File;
use Storage;

class PedidoAdjuntoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(PedidoAdjuntoStoreRequest $request)
    {
        $file     = null;
        $filename = null;

        // selecciono el "disco" a usar
        $disk = Storage::disk('public');

        try {

            // busco el pedido
            $pedido = Pedido::findOrFail($request->input('pedido'));

            $file     = $request->file('file');
            $filename = microtime(true).'_'.$file->getClientOriginalName();

            if ($disk->put($filename, File::get($file))) {
                $adjunto         = new PedidoAdjunto();
                $adjunto->titulo = $file->getClientOriginalName();
                $adjunto->path   = $filename;

                // asocio el pedido
                $adjunto->pedido()->associate($pedido);

                $adjunto->save();

                return response()->json($adjunto, 201);
            } else {
                abort(500);
            }
        } catch (\Exception $e) {

            if ( ($filename !== null) && ($disk->exists($filename))) {
                $disk->delete($filename);
            }

            return response()->json("Error al subir el archivo", 500);
        }
    }


    /**
     * [destroy description]
     * @param  integer $id ID del adjunto
     * @return Response
     */
    public function destroy($id)
    {
        try {

            $adjunto = PedidoAdjunto::findOrFail($id);
            $disk    = Storage::disk('public');

            // existe el archivo
            if ($disk->exists($adjunto->path)) {

                // se pudo borrar
                if ($disk->delete($adjunto->path)) {
                    $adjunto->delete();
                    return response()->json(null, 200);
                }
                else {
                    abort(500);
                }
            }
            // si no existe solo borro la relacion
            else {
                $adjunto->delete();
                return response()->json(null, 200);
            }

        } catch (\ModelNotFoundException $e) {
            abort(401);
        } catch (\Exception $e) {
            abort(500);
        }
    }
}
