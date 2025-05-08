<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Sale\Entities\Sale;

class SalesReport extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $customers;
    public $start_date;
    public $end_date;
    public $customer_id;
    public $sale_status;
    public $payment_status;

    protected $rules = [
        'start_date' => 'required|date|before_or_equal:end_date',
        'end_date'   => 'required|date|after_or_equal:start_date',
    ];


    public function mount($customers) {
        $this->customers = $customers;
        $this->start_date = today()->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->customer_id = '';
        $this->sale_status = '';
        $this->payment_status = '';
    }

    public function render() {
        $sales = Sale::whereDate('date', '>=', $this->start_date)
            ->whereDate('date', '<=', $this->end_date)
            ->when($this->customer_id, function ($query) {
                return $query->where('customer_id', $this->customer_id);
            })
            ->when($this->sale_status, function ($query) {
                return $query->where('status', $this->sale_status);
            })
            ->when($this->payment_status, function ($query) {
                return $query->where('payment_status', $this->payment_status);
            })
            ->orderBy('date', 'desc')->paginate(10);

        $totalAmount = Sale::whereDate('date', '>=', $this->start_date)
                ->whereDate('date', '<=', $this->end_date)
                ->when($this->customer_id, function ($query) {
                    return $query->where('customer_id', $this->customer_id);
                })
                ->when($this->sale_status, function ($query) {
                    return $query->where('status', $this->sale_status);
                })
                ->when($this->payment_status, function ($query) {
                    return $query->where('payment_status', $this->payment_status);
                })
                ->sum('total_amount') / 100;  // Tính tổng của total_amount

        return view('livewire.reports.sales-report', [
            'sales' => $sales,
            'totalAmount' => $totalAmount,  // Truyền tổng vào view
        ]);
    }

    public function generateReport() {
        $this->validate();
        $this->render();
    }
}
