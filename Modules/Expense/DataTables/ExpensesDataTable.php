<?php

namespace Modules\Expense\DataTables;

use Modules\Expense\Entities\Expense;
use Modules\Expense\Entities\ExpenseCategory;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ExpensesDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount', function ($data) {
                return format_currency($data->amount);
            })
            ->addColumn('action', function ($data) {
                return view('expense::expenses.partials.actions', compact('data'));
            })
            ->filterColumn('category.category_name', function($query, $keyword) {
                $query->whereHas('category', function($q) use ($keyword) {
                    $q->where('category_name', 'like', "%{$keyword}%");
                });
            });
    }

    public function query(Expense $model)
    {
        $query = $model->newQuery()->with('category');
        
        if ($this->request()->has('category_id') && !empty($this->request()->category_id)) {
            $query->where('category_id', $this->request()->category_id);
        }
        
        
        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('expenses-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(6)
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
            )
            ->initComplete('function() {
                // Category filter
                var categorySelect = $("<select class=\"form-control\"><option value=\"\">All Categories</option></select>")
                    .on("change", function() {
                        var val = $(this).val();
                        window.LaravelDataTables[\'expenses-table\'].column("category.category_name:name").search(val).draw();
                    });
                
                // Append the select to the DataTables filter container
                $(this.api().table().container()).find(\'.dataTables_filter\').prepend(categorySelect);
                
                $.ajax({
                    url: "' . route('expense-categories.get') . '",
                    dataType: "json",
                    success: function(response) {
                        if (response && response.data) {
                            $.each(response.data, function(index, category) {
                                categorySelect.append("<option value=\"" + category.category_name + "\">" + category.category_name + "</option>");
                            });
                        }
                    }
                });
            }');
    }

    protected function getColumns()
    {
        return [
            Column::make('date')
                ->className('text-center align-middle'),

            Column::make('reference')
                ->className('text-center align-middle'),

            Column::make('category.category_name')
                ->title('Category')
                ->className('text-center align-middle'),

            Column::computed('amount')
                ->className('text-center align-middle'),

            Column::make('details')
                ->className('text-center align-middle'),

            Column::make('payment_method')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string
    {
        return 'Expenses_' . date('YmdHis');
    }
}
