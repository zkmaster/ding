<?php


namespace App\Http\Controllers\V1;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

class VersionBaseController extends Controller
{
    use Helpers;

    public function showError($msg)
    {
        $this->response->error($msg, 200);
    }
}
