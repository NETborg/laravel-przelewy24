<?php
namespace NetborgTeam\P24\Observers;

use NetborgTeam\P24\P24Transaction;

class P24TransactionObserver
{

    public function created(P24Transaction $transaction)
    {
        if (!$transaction->p24_url_return) {
            $transaction->p24_url_return = url(route('getTransactionReturn', [ 'transactionId' => $transaction->id ]), [], true);
            $transaction->save();
        }
    }
}
