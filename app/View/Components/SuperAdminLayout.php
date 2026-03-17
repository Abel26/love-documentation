<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class SuperAdminLayout extends Component
{
    /**
     * Get view / contents that represents component.
     */
    public function render(): View
    {
        return view('layouts.super-admin');
    }
}
