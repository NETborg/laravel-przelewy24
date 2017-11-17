<?php
namespace NetborgTeam\P24\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Description of P24ListenerController
 *
 * @author netborg
 */
class P24ListenerController extends Controller {
    
    
    public function status(Request $request)
    {
        return new Response("listening ...");
    }
    
    public function getReturn(Request $request)
    {
        event();
        return redirect(route(config('p24.route_return')));
    }
    
    
}


