<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Account extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'Code',
        'Name',
        'BankAccountNumber',
        'Description',
        'CurrencyCode',
        'EnablePaymentsToAccount',
        'ShowInExpenseClaims',
        'AccountID',
        'ReportingCode',
        'ReportingCodeName',
        'HasAttachments',
        'UpdatedDateUTC',
        'Type',
        'BankAccountType',
        'Class'
    ];


}