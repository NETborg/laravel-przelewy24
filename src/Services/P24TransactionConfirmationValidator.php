<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Services;

use NetborgTeam\P24\Contracts\P24SignerContract;
use NetborgTeam\P24\Exceptions\InvalidSignatureException;
use NetborgTeam\P24\Exceptions\InvalidTransactionParameterException;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;

class P24TransactionConfirmationValidator
{

    /**
     * @var P24SignerContract
     */
    private $signer;


    /**
     * P24TransactionConfirmationValidator constructor.
     * @param P24SignerContract $signer
     */
    public function __construct(P24SignerContract $signer)
    {
        $this->signer = $signer;
    }


    /**
     * @param  P24Transaction                       $transaction
     * @param  P24TransactionConfirmation           $transactionConfirmation
     * @throws InvalidSignatureException
     * @throws InvalidTransactionParameterException
     */
    public function validate(P24Transaction $transaction, P24TransactionConfirmation $transactionConfirmation): void
    {
        $this->validateSignature($transactionConfirmation);
        $this->validateTransactionParameters($transaction, $transactionConfirmation);
    }

    /**
     * @param  P24TransactionConfirmation $transactionConfirmation
     * @throws InvalidSignatureException
     */
    public function validateSignature(P24TransactionConfirmation $transactionConfirmation): void
    {
        if ($transactionConfirmation->p24_sign !== $this->signer->sign($transactionConfirmation->getSignablePayload())) {
            throw new InvalidSignatureException(
                $this->signer->sign($transactionConfirmation->getSignablePayload()),
                $transactionConfirmation->p24_sign
            );
        }
    }

    /**
     * @param  P24Transaction                       $transaction
     * @param  P24TransactionConfirmation           $transactionConfirmation
     * @throws InvalidTransactionParameterException
     */
    public function validateTransactionParameters(P24Transaction $transaction, P24TransactionConfirmation $transactionConfirmation): void
    {
        if ($transaction->p24_merchant_id !== $transactionConfirmation->p24_merchant_id) {
            throw new InvalidTransactionParameterException('p24_merchant_id', $transaction->p24_merchant_id, $transactionConfirmation->p24_merchant_id);
        }
        if ($transaction->p24_pos_id !== $transactionConfirmation->p24_pos_id) {
            throw new InvalidTransactionParameterException('p24_pos_id', $transaction->p24_pos_id, $transactionConfirmation->p24_pos_id);
        }
        if ($transaction->p24_amount !== $transactionConfirmation->p24_amount) {
            throw new InvalidTransactionParameterException('p24_amount', $transaction->p24_amount, $transactionConfirmation->p24_amount);
        }
        if ($transaction->p24_currency !== $transactionConfirmation->p24_currency) {
            throw new InvalidTransactionParameterException('p24_currency', $transaction->p24_currency, $transactionConfirmation->p24_currency);
        }
    }
}
