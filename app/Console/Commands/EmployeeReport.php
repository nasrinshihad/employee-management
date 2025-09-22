<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Maatwebsite\Excel\Excel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeeReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports today\'s newly added employees into an Excel file.';

     protected $excel;

    public function __construct(Excel $excel)
    {
        parent::__construct();
        $this->excel = $excel;
    }

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $date = now()->format('Y-m-d');
        $fileName = "employees_{$date}.xlsx";
        $filePath = "reports/{$fileName}";

        if (!Storage::exists('reports')) {
            Storage::makeDirectory('reports');
        }

        $todaysEmployees = Employee::whereDate('created_at', now()->toDateString())
            ->get([
                'name','email','department','salary','joining_date','profile_picture','created_at'
            ]);

        $this->excel->store(new class($todaysEmployees) implements FromCollection {
            protected $employees;
            public function __construct($employees) { $this->employees = $employees; }
            public function collection() { return $this->employees; }
        }, $filePath);

        $this->info("Today's employees report has been saved to storage/app/{$filePath}");
    }
}