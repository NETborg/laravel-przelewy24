<?php
namespace NetborgTeam\P24\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use NetborgTeam\P24\Events\P24TransactionUserReturnedEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationConnectionErrorEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationInvalidParameterEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationInvalidSenderEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationInvalidSignatureEvent;
use NetborgTeam\P24\Events\P24TransactionConfirmationSuccessEvent;
use NetborgTeam\P24\Exceptions\InvalidSignatureException;
use NetborgTeam\P24\Exceptions\InvalidTransactionParameterException;
use NetborgTeam\P24\Exceptions\P24ConnectionException;
use NetborgTeam\P24\P24TransactionConfirmation;
use NetborgTeam\P24\Services\P24Manager;
use NetborgTeam\P24\P24Transaction;

/**
 * Description of P24ListenerController
 *
 * @author netborg
 */
class P24ListenerController extends Controller
{
    public function getTransactionStatus(Request $request, P24Manager $manager)
    {
        Log::debug("Received P24 Payment Confirmation request.");
        Log::debug("Raw Request params: ".json_encode($request->all(), JSON_UNESCAPED_UNICODE));

        $transactionConfirmation = $manager->parseTransactionConfirmation($request);

        Log::debug("Processed Request params [TransactionConfirmation]: ".$transactionConfirmation->toJson(JSON_UNESCAPED_UNICODE));

        if (!$manager->isValidSender($request)) {
            $transactionConfirmation->verification_status = P24TransactionConfirmation::STATUS_INVALID_SENDER_IP;
            $transactionConfirmation->save();

            Log::error('Received P24 Transaction Confirmation from INVALID SENDER!');

            event(new P24TransactionConfirmationInvalidSenderEvent(
                $transactionConfirmation,
                $request->getClientIp()
            ));

            return new Response();
        }

        // find transaction and assign confirmation
        $transaction = P24Transaction::where('p24_session_id', $transactionConfirmation->p24_session_id)->first();
        if ($transaction instanceof P24Transaction) {
            $transactionConfirmation->p24Transaction()->associate($transaction);
            $transactionConfirmation->save();

            Log::debug('Transaction found and assigned successfully.');

            // First validate received transaction confirmation before sending for verification
            try {
                $manager->validateTransactionConfirmation(
                    $transaction,
                    $transactionConfirmation
                );

                Log::debug('Transaction Confirmation VALIDATION procedure finished. Validation result: `'.$transactionConfirmation->verification_status.'`.');
            } catch (InvalidSignatureException $ex) {
                Log::error($ex->getMessage());

                event(new P24TransactionConfirmationInvalidSignatureEvent($transactionConfirmation));

                return new Response();
            } catch (InvalidTransactionParameterException $ex) {
                Log::error($ex->getMessage());

                event(new P24TransactionConfirmationInvalidParameterEvent(
                    $transactionConfirmation,
                    $ex->getParameterName(),
                    $ex->getExpectedValue(),
                    $ex->getReceivedValue()
                ));

                return new Response();
            }

            // If validation passes - send for verification to P24
            try {
                $manager->verifyTransactionConfirmation($transactionConfirmation);

                Log::debug('Transaction Confirmation VERIFICATION procedure finished. Verification result `'.$transactionConfirmation->verification_status.'`.');

                if (P24TransactionConfirmation::STATUS_VERIFIED === $transactionConfirmation->verification_status) {

                    // Valid and verified transaction confirmation received

                    Log::info("Verified payment received for "
                        .number_format($transactionConfirmation->p24_amount / 100, 2)
                        ." $transactionConfirmation->p24_currency.");

                    event(new P24TransactionConfirmationSuccessEvent(
                        $transaction,
                        $transactionConfirmation
                    ));

                    return new Response();
                }
            } catch (P24ConnectionException $e) {
                Log::error($e->getMessage());

                event(new P24TransactionConfirmationConnectionErrorEvent(
                    $transactionConfirmation,
                    $e->getCode(),
                    $e->getMessage()
                ));

                return new Response();
            }
        }

        return new Response();
    }

    public function getReturn($transactionId=null)
    {
        if ($transactionId) {
            $transaction = P24Transaction::find($transactionId);

            if ($transaction instanceof P24Transaction) {
                event(new P24TransactionUserReturnedEvent($transaction));
            }
        }

        $redirectTo = '/';
        if ($returnRoute = config('p24.route_return')) {
            $redirectTo = preg_match('/^(http|https):\/\//i', $returnRoute) > 0
                ? $returnRoute
                : url(route($returnRoute), [], true);
        }

        return redirect($redirectTo);
    }
}
