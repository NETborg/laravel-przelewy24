<?php
namespace NetborgTeam\P24\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NetborgTeam\P24\Events\P24TransactionCancelledEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationConnectionErrorEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationInvalidParameterEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationInvalidSenderEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationInvalidSignatureEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationSuccessEvent;
use NetborgTeam\P24\GeneralError;
use NetborgTeam\P24\Services\P24Manager;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;
use Maknz\Slack\Facades\Slack;

/**
 * Description of P24ListenerController
 *
 * @author netborg
 */
class P24ListenerController extends Controller
{
    public function getTransactionStatus(Request $request, P24Manager $manager)
    {
        $transactionConfirmation = $manager->parseTransactionConfirmation($request);

        if (!$manager->isValidSender($request)) {
            if (class_exists(Slack::class)) {
                Slack::send('Received P24 Transaction Confirmation from INVALID SENDER!');
            }

            event(new P24TransactionConfirmationInvalidSenderEvent(
                $transactionConfirmation,
                $request->getClientIp()
            ));

            return new Response();
        }

        if (class_exists(Slack::class)) {
            Slack::send($transactionConfirmation->toJson());
        }

        // find transaction and assign confirmation
        $transaction = P24Transaction::where('p24_session_id', $transactionConfirmation->p24_session_id)->first();
        if ($transaction instanceof P24Transaction) {
            $transactionConfirmation->p24Transaction()->associate($transaction);
            $transactionConfirmation->save();

            try {
                $manager->validateTransactionConfirmation(
                    $transaction,
                    $transactionConfirmation,
                    $transactionConfirmation->p24_sign
                );

                $verificationResult = $manager->verifyTransactionConfirmation($transactionConfirmation);
            } catch (\NetborgTeam\P24\Exceptions\InvalidSignatureException $ex) {
                if (class_exists(Slack::class)) {
                    Slack::send($ex->getMessage());
                }
                event(new P24TransactionConfirmationInvalidSignatureEvent($transactionConfirmation));

                return new Response();
            } catch (\NetborgTeam\P24\Exceptions\InvalidTransactionParameterException $ex) {
                if (class_exists(Slack::class)) {
                    Slack::send($ex->getMessage());
                }

                event(new P24TransactionConfirmationInvalidParameterEvent(
                    $transactionConfirmation,
                    $ex->getParameterName(),
                    $ex->getExpectedValue(),
                    $ex->getReceivedValue()
                ));

                return new Response();
            } catch (\NetborgTeam\P24\Exceptions\P24ConnectionException $ex) {
                if (class_exists(Slack::class)) {
                    Slack::send($ex->getMessage());
                }

                event(new P24TransactionConfirmationConnectionErrorEvent(
                    $transactionConfirmation,
                    $ex->getCode(),
                    $ex->getMessage()
                ));

                return new Response();
            }

            if (isset($verificationResult['error']) && $verificationResult['error'] === 0) {
                $transactionConfirmation->p24Transaction()->associate($transaction);
                $transactionConfirmation->verification_status = P24TransactionConfirmation::STATUS_CONFIRMED_VERIFIED;
                $transactionConfirmation->save();

                if (class_exists(Slack::class)) {
                    Slack::send("Verified payment received for "
                        .number_format($transactionConfirmation->p24_amount / 100, 2)
                        ." $transactionConfirmation->p24_currency.");
                }
                event(new P24TransactionConfirmationSuccessEvent(
                    $transaction,
                    $transactionConfirmation
                ));
            }
        }

        return new Response();
    }

    public function getReturn($transactionId=null)
    {
        if ($transactionId) {
            $transaction = P24Transaction::find($transactionId);

            if ($transaction instanceof P24Transaction) {
                event(new P24TransactionCancelledEvent($transaction));
            }
        }

        return redirect();
    }
}
