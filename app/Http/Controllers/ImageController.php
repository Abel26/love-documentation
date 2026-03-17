<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display the image gallery page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('images.index');
    }
}
