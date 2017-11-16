<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Description of P24ListenerController
 *
 * @author netborg
 */
class P24ListenerController extends Controller {
    
    
    public function listen(Request $request)
    {
        return new Response("listening ...");
    }
    
    
}


