<?php

namespace Codificar\Panic\Models;


use Illuminate\Database\Eloquent\Model;
use SebastianBerc\Repositories\Traits\Sortable;

class Panic extends Model
{
    use Sortable;

    protected $table = 'panic';

    public $incrementing = true;

    public $timestamps = true;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'ledger_id',
        'request_id',
        'admin_id',
        'history'
    ];

    public static $rules = [
        'ledger_id' => 'required',
        'request_id' => '',
        'admin_id' => '',
        'history' => '',
    ];

    public $sortable = [
        'ledger_id',
        'request_id',
        'admin_id',
        'history',
    ];

    protected $casts = [
        'id' => 'int',
        'ledger_id' => 'int',
        'request_id' => 'int',
        'admin_id' => 'int',
        'history' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    public function request()
    {
        return $this->belongsTo('Requests', 'request_id', 'id');
    }

    public function ledger()
    {
        return $this->belongsTo('Ledger', 'ledger_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id');
    }
}
