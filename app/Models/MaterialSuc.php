<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialSuc extends Model
{
    use HasFactory;
    protected $fillable = [
        'materialsucId', 'matid', 'sucid', 'fecact', 'existenc', 'existmin', 'existmax', 'costo', 'porcutild',
        'observ', 'impcons', 'costestprd', 'fecultcli', 'ultcli', 'precultcli', 'canultcli', 'fecultprov', 'ultpro',
        'precultprov', 'canultprov', 'precio1', 'precio2', 'precio3', 'precio4', 'precio5', 'descuento1', 'descuento2',
        'descuento3', 'descuento4', 'descuento5', 'preciom1', 'preciom2', 'preciom3', 'preciom4', 'preciom5', 'escalamin1',
        'escalamin2', 'escalamin3', 'escalamin4', 'escalamin5', 'fecdesini', 'fecdesfin', 'factor1', 'factor2', 'factor3',
        'factor4', 'factor5', 'inactivo', 'pos_redime', 'pos_facpto', 'docultprov', 'docultcli', 'ultcostprom', 'costopres',
        'ubicacion', 'cant30', 'cant60', 'cant90', 'cant120', 'cantmas', 'porcosind', 'porimpcons', 'preciomind', 'preciominm',
        'dctomax', 'costofical', 'preiconsumo', 'preiva', 'costounitario', 'fecrecal', 'divisor1', 'divisor2', 'divisor3',
        'divisor4', 'divisor5'
    ];
}
