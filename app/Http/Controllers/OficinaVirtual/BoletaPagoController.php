<?php

namespace app\Http\Controllers\OficinaVirtual;

use PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Models\OficinaVirtual\BoletaPago;
use App\Http\Requests;
use Illuminate\Http\Request;

use DNS1D;

use Tecnickcom\Tcpdf\Tcpdf_barcodes_1d as TCPDFBarcode;

class BoletaPagoController extends Controller
{
    /**
     * [generar description]
     * @param  Resquest $request [description]
     * @return [type]            [description]
     */
    public function generar(Request $request)
    {
        if (!$request->has('tipo-busqueda') || !$request->has('busqueda')) {
            return abort(503);
        }

        if ($request->has('tipo-busqueda') === 'expediente') {
            $boletasPago = BoletaPago::where('expediente', '=', $request->input('busqueda'))->get();
        }
        else {
            $boletasPago = BoletaPago::where('expediente', '=', $request->input('busqueda'))->get();
        }

        // si no obtuve resultados por expediente o unidad respondo con un error
        if ($boletasPago->isEmpty()) {
            return response()->json(['error' => 'No se encontró boleta de pago con los datos ingresados'], 404);
        }

        // Aquí sigue configuración básica del PDF
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        PDF::SetFont('helvetica', '', 10);

        // define barcode style
        $bar_code_style = array(
            'position'     => '',
            'align'        => 'L',
            'stretch'      => false,
            'fitwidth'     => false,
            'cellfitalign' => 'L',
            'border'       => false,
            'hpadding'     => 'auto',
            'vpadding'     => 'auto',
            'fgcolor'      => [0,0,0],
            'bgcolor'      => false, //array(255,255,255),
            'text'         => true,
            'font'         => 'helvetica',
            'fontsize'     => 8,
            'stretchtext'  => 4
        );

        foreach ($boletasPago as $boletaPago) {

            // Agregamos una página en blanco
            PDF::AddPage();

            // "Parseamos" el template (esto se podría formalizar más)
            $codigoDposs = PDF::serializeTCPDFtagParameters(
                [$boletaPago->getCodigoDposs(), 'C39', '', '', '', '16', 0.4, $bar_code_style, 'N']
            );

            $html = view('oficina-virtual.boleta-pago')
                ->with('boletaPago', $boletaPago)
                ->with('codigoDposs', $codigoDposs)
                ->render();

            // output the HTML content
            PDF::writeHTML($html, true, false, true, false, '');

            // reset pointer to the last page
            PDF::lastPage();
        }

        // Close and output PDF document
        return Response::make(
            PDF::Output('boleta-pago.pdf', 'S'),
            200, [
                'content-type' => 'application/pdf',
                'Content-Disposition' => 'inline; boleta-pago.pdf'
            ]
        );
    }
}
