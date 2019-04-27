<?php
namespace NetborgTeam\P24\Observers;

use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationObserver
{
    public function creating(P24TransactionConfirmation $transactionConfirmation)
    {
        if (!$transactionConfirmation->id) {
            $transactionConfirmation->id = P24TransactionConfirmation::generateUid();
        }
    }
}
