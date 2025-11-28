<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Simple dashboard for Kuya EDs meatshop
        $content = view('dashboard');

        return view('layout', [
            'title'   => 'Kuya EDs Meatshop',
            'content' => $content,
        ]);
    }
}
