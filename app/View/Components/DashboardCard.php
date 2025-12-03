<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    public $label;
    public $value;
    public $change;
    public $changeColor;
    public $iconBgClass;

    public function __construct($label = 'Metric Label', $value = '0', $change = null, $changeColor = 'cyan', $iconBgClass = 'bg-gradient-to-br from-purple-500 to-cyan-500')
    {
        $this->label = $label;
        $this->value = $value;
        $this->change = $change;
        $this->changeColor = $changeColor;
        $this->iconBgClass = $iconBgClass;
    }

    public function render()
    {
        return view('components.dashboard-card');
    }
}
