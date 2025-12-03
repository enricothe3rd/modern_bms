<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardHeader extends Component
{
    public $title;
    public $subtitle;
    public $buttonText;
    public $buttonId;

    public function __construct(
        $title = 'Dashboard',
        $subtitle = "Welcome back! Here's your overview.",
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
    }

    public function render()
    {
        return view('components.dashboard-header');
    }
}
