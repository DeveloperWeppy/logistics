<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';

    protected $fillable = [
        'materialesId', 'matid', 'codigo', 'codbarra', 'descrip', 'grupmatid', 'referencia', 'peso', 'unidad', 'unimay', 'tipoivaid', 'factor', 'factorglb', 'comision', 'lineamatid', 'tipmat', 'tipserial', 'rutafoto', 'horasserv', 'porutil', 'porcomision', 'excluido', 'deptoartid', 'bono1', 'bono2', 'mesgaran', 'codcums', 'concentracion', 'regimvima', 'principioact', 'presentacion', 'canal', 'conben', 'marcaartid', 'volumen', 'cantpre', 'modeloart', 'sku', 'rutaficha', 'codcpid', 'res162', 'empaque', 'porviva', 'estado'
    ];
    public function materialsuc()
    {
        return $this->hasMany(MaterialSuc::class, 'matid', 'matid');
    }
}
