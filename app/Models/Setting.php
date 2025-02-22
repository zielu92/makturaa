<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seller_name',
        'seller_company_name',
        'seller_email',
        'seller_phone',
        'seller_address',
        'seller_city',
        'seller_postal_code',
        'seller_country',
        'seller_nip',
        'seller_regon',
        'seller_krs',
        'invoice_default_issuer',
        'invoice_default_place',
        'invoice_default_pattern',
        'invoice_default_tax_rate',
        'invoice_default_template',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];
}
