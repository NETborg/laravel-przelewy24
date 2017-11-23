<?php
namespace NetborgTeam\P24\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NetborgTeam\P24\Services\P24Manager;

/**
 * Description of P24ListenerController
 *
 * @author netborg
 */
class P24ListenerController extends Controller {
    
    
    public function getTransactionStatus(Request $request, P24Manager $manager)
    {
        return new Response("listening ...");
    }
    
    public function getReturn(Request $request)
    {
        event();
        return redirect(route(config('p24.route_return')));
    }
    
    
}