<?php

namespace App\Http\Controllers;

class MainController extends Controller
{
    public function index()
    {
        return $this->responseSuccess('success');
    }
}
