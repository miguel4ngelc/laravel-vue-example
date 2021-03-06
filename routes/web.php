<?php

// rutas de login, logout, register, verify, reset, etc
require(__DIR__.'/auth-routes.php');


// Route::get('/test', function(App\Contracts\DpossApiContract $api){

//     // ejemplos
//     // R 19401 26792
//     // C 247
//     // MM 2187
// });

/**
 * User authenticated routes
 */
Route::group(['middleware' => ['auth']], function() {

	/**
     * HOME
     */
    Route::get('/', [
    	'as' => 'home',
    	'uses' => 'HomeController@index'
    ]);

	/**
     * USERS
     */
    Route::group([
        'as'        => 'users.',
        'prefix'    => 'users'
    ], function() {
        Route::get('dashboard', [
            'as'   => 'dashboard',
            'uses' => 'Admin\UserController@dashboard'
        ]);

        Route::get('profile', [
            'as'   => 'profile.form',
            'uses' => 'Admin\UserController@profile'
        ]);

        Route::put('profile', [
            'as'   => 'profile',
            'uses' => 'Admin\UserController@saveProfile'
        ]);
    });

    // /admin routes
    require(__DIR__.'/admin-routes.php');

    // /pedidos routes
    require(__DIR__.'/pedidos-routes.php');

    // /alertas routes
    require(__DIR__.'/alertas-routes.php');

    // /solicitudes routes
    require(__DIR__.'/solicitudes-routes.php');

    /**
     * Turnos
     */
    Route::group([
        'namespace' => 'Turnos',
        'as'        => 'turnos::',
        'prefix'    => 'turnos'
    ], function() {

        Route::get('/', [
            'as'   => 'index',
            'uses' => 'TurnoController@index',
            'middleware' => ['ability:admin,turnos-browse'],
        ]);

        Route::get('asignados/{actividadId}', [
            'as'   => 'asignados-por-actividad',
            'uses' => 'TurnoController@asignadosPorActividad',
            'middleware' => ['ability:admin,turnos-browse'],
        ])->where('actividadId', '[0-9]+');

        Route::get('vencidos/{actividadId}', [
            'as'   => 'vencidos-por-actividad',
            'uses' => 'TurnoController@vencidosPorActividad',
            'middleware' => ['ability:admin,turnos-browse'],
        ])->where('actividadId', '[0-9]+');

        Route::put('validar-atencion/{id}', [
            'as'   => 'validar-atencion',
            'uses' => 'TurnoController@validarAtencion',
            'middleware' => ['ability:admin,turnos-validar-atencion'],
        ])->where('id', '[0-9]+');

        Route::delete('/{id}', [
            'as'   => 'destroy',
            'uses' => 'TurnoController@destroy',
            'middleware' => ['ability:admin,turnos-delete'],
        ])->where('id', '[0-9]+');

    }); // turnos group

    /**
     * OficinaVirtual
     */
    Route::group([
        'namespace' => 'OficinaVirtual',
        'as'        => 'oficina-virtual::',
        'prefix'    => 'oficina-virtual'
    ], function() {

        Route::get('boletas-de-pago', [
            'as'   => 'boletas-de-pago',
            'uses' => 'BoletaPagoController@main'
        ]);

        Route::get('conexiones/{conexion}/facturas', [
            'as'   => 'conexiones.facturas',
            'uses' => 'ConexionController@facturas'
        ])->where('conexion', '[0-9]+');

        Route::get('resumen-historico', [
            'as'   => 'resumen-historico-facturas',
            'uses' => 'ConexionController@resumenFacturas'
        ]);

        Route::get('conexiones/{conexion}/deudas', [
            'as'   => 'conexiones.deudas',
            'uses' => 'ConexionController@deudas'
        ]);

        Route::get('deudas-pendientes', [
            'as'   => 'deudas-pendientes',
            'uses' => 'ConexionController@estadoDeuda'
        ]);

        Route::post('boletas-de-pago-query', [
            'as'   => 'boletas-de-pago-query',
            'uses' => 'BoletaPagoController@query'
        ]);

        Route::get('solicitar-libre-deuda', [
            'as'   => 'solicitar-libre-deuda',
            'uses' => 'LibreDeudaController@solicitarLibreDeuda'
        ]);

        Route::get('cuentas-vinculadas', [
            'as' => 'conexiones.vinculadas',
            'uses' => 'ConexionController@vinculadas',
        ]);

        Route::post('cuentas/vincular', [
            'as' => 'conexiones.vincular-usuario',
            'uses' => 'ConexionController@vincularCuentaConUsuario',
        ]);

        Route::delete('cuentas/{conexion}/desvincular', [
            'as' => 'conexiones.desvincular',
            'uses' => 'ConexionController@desvincular',
        ]);

        Route::get('notificaciones', [
            'as' => 'notificaciones.de-usuario',
            'uses' => 'NotificacionController@deUsuario',
        ]);

    }); // OficinaVirtual group

}); // middleware auth
