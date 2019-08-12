<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class BankTransactionLineItem extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bank_transactions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'BankTransaction_id',
        'Tracking_id',
        'Description',
        'Quantity',
        'AccountCode',
        'ItemCode',
        'LineItemID',
        'UnitAmount',
        'LineAmount',
        'TaxType'
    ];

    





}