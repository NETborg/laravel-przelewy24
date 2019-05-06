<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Observers;

use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationObserver
{
    /**
     * @param P24TransactionConfirmation $transactionConfirmation
     */
    public function creating(P24TransactionConfirmation $transactionConfirmation): void
    {
        if (!$transactionConfirmation->id) {
            $transactionConfirmation->id = P24TransactionConfirmation::generateUid();
        }
    }
}
