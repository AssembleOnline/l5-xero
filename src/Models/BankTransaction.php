<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class BankTransaction extends Model {

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
		'IsReconciled',
        'Date',
        'Reference',
        'CurrencyCode',
        'CurrencyRate',
        'Url',
        'SubTotal',
        'TotalTax',
        'Total',
        'BankTransactionID',
        'PrepaymentID',
        'OverpaymentID',
        'UpdatedDateUTC',
        'HasAttachments',
        'Type',
        'Status',
        'LineAmountTypes',
        'Contact_id'
    ];







}