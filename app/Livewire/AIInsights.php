<?php

namespace App\Livewire;

use App\Models\AIInsight;
use App\Services\AIInsightsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AIInsights extends Component
{
    public $period = '30_days';
    public $insight = null;
    public $loading = false;
    public $error = null;

    public function mount()
    {
        $this->loadInsight();
    }

    public function loadInsight()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $user = Auth::user();
            $service = new AIInsightsService();

            $this->insight = $service->getInsights($user, $this->period);

            if (!$this->insight) {
                $this->error = 'No hay suficientes datos de ánimo para generar insights. Por favor, registra tus estados de ánimo durante algunos días.';
            }
        } catch (\Exception $e) {
            $this->error = 'Error al generar insights: ' . $e->getMessage();
            \Log::error('AI Insights error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function refreshInsight()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $user = Auth::user();
            $service = new AIInsightsService();

            // Generate new insights (bypass cache)
            $this->insight = $service->generateInsights($user, $this->period);

            if (!$this->insight) {
                $this->error = 'No hay suficientes datos de ánimo para generar insights.';
            } else {
                session()->flash('success', '¡Insights actualizados correctamente!');
            }
        } catch (\Exception $e) {
            $this->error = 'Error al generar insights: ' . $e->getMessage();
            \Log::error('AI Insights refresh error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function updatedPeriod()
    {
        $this->loadInsight();
    }

    public function render()
    {
        return view('livewire.a-i-insights')->layout('layouts.app');
    }
}
