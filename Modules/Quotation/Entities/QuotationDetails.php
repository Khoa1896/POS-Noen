<?php

namespace Modules\Quotation\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class QuotationDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function quotation() {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function getPriceAttribute($value) {
        return $value;
    }

    public function getUnitPriceAttribute($value) {
        return $value;
    }

    public function getSubTotalAttribute($value) {
        return $value;
    }

    public function getProductDiscountAmountAttribute($value) {
        return $value;
    }

    public function getProductTaxAmountAttribute($value) {
        return $value;
    }}
