<?php

namespace App\Controllers;
use App\Middlewares\Auth;
use App\Abstract\AbstractController;

class AcceuilController extends AbstractController
{
        public function index():void
    {
        // $auth = new Auth();
        // $auth();
        $this->renderHtml('acceuil');
    }
    public function create():void {}

    public function store():void {}

    public function show():void {}

    public function update():void {}

    public function edit():void {}
    public function destroy():void {}



}