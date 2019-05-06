<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Observers;

use NetborgTeam\P24\P24Transaction;

class P24TransactionObserver
{
    /**
     * @param P24Transaction $transaction
     */
    public function creating(P24Transaction $transaction): void
    {
        if (!$transaction->id) {
            $transaction->id = P24Transaction::generateUid();
        }

        if (!$transaction->p24_url_return) {
            $transaction->p24_url_return = url(route('getTransactionReturn', [ 'transactionId' => $transaction->id ]), [], true);
        }
    }
}
