<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteProduct extends Model
{
    use HasFactory;


    protected $fillable = [
        'product_id',
        'invoice_id',
        'quantity',
        'description',
        'tax',
        'discount',
        'total',
    ];

    public function product(){
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id')->first();
    }
}
