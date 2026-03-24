<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;
use Modules\Master\Repositories\ItemRepository;

class TestUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:test:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test update';

    public function __construct(
        protected ItemRepository $items
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Getting all items.');
        $items = $this->items();
        foreach($items as $item) {
            $dbItem = $this->items->select(['*'])
                ->where('title', $item['Item_Name'])
                ->first();
            $dbItem ? $dbItem->update(['item_code' => $item['Item_Code'], 'record_exists'=>1]) : null;
        }
        $this->info('Items are updated.');
    }

    public function items()
    {
        return [
            ["SN" => 1, "Item_Name" => "Bag", "Item_Code" => "BCB"],
            ["SN" => 2, "Item_Name" => "Battery", "Item_Code" => "BTRY"],
            ["SN" => 3, "Item_Name" => "Bicycle", "Item_Code" => "BIC"],
            ["SN" => 4, "Item_Name" => "Calculator", "Item_Code" => "CLCTOR"],
            ["SN" => 5, "Item_Name" => "Carton Packing Tape", "Item_Code" => "CRTT"],
            ["SN" => 6, "Item_Name" => "Chair", "Item_Code" => "CHR"],
            ["SN" => 7, "Item_Name" => "Chart Paper", "Item_Code" => "CHP"],
            ["SN" => 8, "Item_Name" => "Coffee Maker", "Item_Code" => "CFM"],
            ["SN" => 9, "Item_Name" => "Colorful Paper A4", "Item_Code" => "CFPR"],
            ["SN" => 10, "Item_Name" => "Diary", "Item_Code" => "DAI"],
            ["SN" => 11, "Item_Name" => "Envelope", "Item_Code" => "ENV"],
            ["SN" => 12, "Item_Name" => "Eraser", "Item_Code" => "ERSR"],
            ["SN" => 13, "Item_Name" => "Glue Stick", "Item_Code" => "GS"],
            ["SN" => 14, "Item_Name" => "Hand Sanitizer", "Item_Code" => "HSAN"],
            ["SN" => 15, "Item_Name" => "ID Card Lanyard", "Item_Code" => "IDL"],
            ["SN" => 16, "Item_Name" => "ID Holder", "Item_Code" => "IDH"],
            ["SN" => 17, "Item_Name" => "Meeting table", "Item_Code" => "MTN"],
            ["SN" => 18, "Item_Name" => "Multiplug", "Item_Code" => "MP"],
            ["SN" => 19, "Item_Name" => "My Clear Bag", "Item_Code" => "MCB"],
            ["SN" => 20, "Item_Name" => "Newsprint", "Item_Code" => "NEP"],
            ["SN" => 21, "Item_Name" => "Office Desk", "Item_Code" => "OFD"],
            ["SN" => 22, "Item_Name" => "Pen", "Item_Code" => "Pen"],
            ["SN" => 23, "Item_Name" => "Pen Holder", "Item_Code" => "PNHDR"],
            ["SN" => 24, "Item_Name" => "Pencil", "Item_Code" => "PCL"],
            ["SN" => 25, "Item_Name" => "Permanent Marker", "Item_Code" => "PML"],
            ["SN" => 26, "Item_Name" => "Board Marker", "Item_Code" => "BML"],
            ["SN" => 27, "Item_Name" => "Photocopy Paper A4", "Item_Code" => "PCYP"],
            ["SN" => 28, "Item_Name" => "Stapler Machine", "Item_Code" => "SPMS"],
            ["SN" => 29, "Item_Name" => "Stapler pins", "Item_Code" => "SPIN"],
            ["SN" => 30, "Item_Name" => "Table", "Item_Code" => "TBL"],
            ["SN" => 31, "Item_Name" => "Tissue Box", "Item_Code" => "TSUB"],
            ["SN" => 32, "Item_Name" => "Water Bottle", "Item_Code" => "WB"],
            ["SN" => 33, "Item_Name" => "Tissue", "Item_Code" => "TSU"],
            ["SN" => 34, "Item_Name" => "Air Conditioner", "Item_Code" => "AC"],
            ["SN" => 35, "Item_Name" => "Attendance Machine", "Item_Code" => "ATM"],
            ["SN" => 36, "Item_Name" => "Camera", "Item_Code" => "CMR"],
            ["SN" => 37, "Item_Name" => "Cartridge Drum", "Item_Code" => "CDM"],
            ["SN" => 38, "Item_Name" => "CCTV Camera", "Item_Code" => "CCTV"],
            ["SN" => 39, "Item_Name" => "Adapter", "Item_Code" => "ADPTR"],
            ["SN" => 40, "Item_Name" => "Desktop Computer", "Item_Code" => "DTOP"],
            ["SN" => 41, "Item_Name" => "EPABX System", "Item_Code" => "EPB"],
            ["SN" => 42, "Item_Name" => "Ethernet Cable", "Item_Code" => "CAT6"],
            ["SN" => 43, "Item_Name" => "Hard drive", "Item_Code" => "HDD"],
            ["SN" => 44, "Item_Name" => "HDMI Cable", "Item_Code" => "HDMI"],
            ["SN" => 45, "Item_Name" => "Headset", "Item_Code" => "HSET"],
            ["SN" => 46, "Item_Name" => "Headphone", "Item_Code" => "HPHONE"],
            ["SN" => 47, "Item_Name" => "Inverter", "Item_Code" => "IVTR"],
            ["SN" => 48, "Item_Name" => "Inverter Battery", "Item_Code" => "IBTRY"],
            ["SN" => 49, "Item_Name" => "Keyboard", "Item_Code" => "KYB"],
            ["SN" => 50, "Item_Name" => "Laptop", "Item_Code" => "LAP"],
            ["SN" => 51, "Item_Name" => "Laptop Adapter", "Item_Code" => "LADPTR"],
            ["SN" => 52, "Item_Name" => "Laptop Keyboard", "Item_Code" => "LKYB"],
            ["SN" => 53, "Item_Name" => "Laptop Trackpad", "Item_Code" => "LPAD"],
            ["SN" => 54, "Item_Name" => "Microwave", "Item_Code" => "MWV"],
            ["SN" => 55, "Item_Name" => "Mobile", "Item_Code" => "MBL"],
            ["SN" => 56, "Item_Name" => "Monitor", "Item_Code" => "MTR"],
            ["SN" => 57, "Item_Name" => "Motorbike", "Item_Code" => "BIKE"],
            ["SN" => 58, "Item_Name" => "Mouse", "Item_Code" => "MOUSE"],
            ["SN" => 59, "Item_Name" => "Mouse Pad", "Item_Code" => "MSP"],
            ["SN" => 60, "Item_Name" => "Pointer", "Item_Code" => "PNT"],
            ["SN" => 61, "Item_Name" => "Power Bank", "Item_Code" => "PBANK"],
            ["SN" => 62, "Item_Name" => "Printer", "Item_Code" => "PTR"],
            ["SN" => 63, "Item_Name" => "Projector", "Item_Code" => "PJT"],
            ["SN" => 64, "Item_Name" => "Projector Screen", "Item_Code" => "PJS"],
            ["SN" => 65, "Item_Name" => "Recorder", "Item_Code" => "RCD"],
            ["SN" => 66, "Item_Name" => "Speaker", "Item_Code" => "SPK"],
            ["SN" => 67, "Item_Name" => "Tablet Cover", "Item_Code" => "TBCVR"],
            ["SN" => 68, "Item_Name" => "Tablet", "Item_Code" => "TAB"],
            ["SN" => 69, "Item_Name" => "Telephone", "Item_Code" => "TLP"],
            ["SN" => 70, "Item_Name" => "Television", "Item_Code" => "TEV"],
            ["SN" => 71, "Item_Name" => "Toner Cartridge", "Item_Code" => "TCA"],
            ["SN" => 72, "Item_Name" => "Camera Accessories", "Item_Code" => "CMA"],
            ["SN" => 73, "Item_Name" => "Pendrive", "Item_Code" => "PED"],
            ["SN" => 74, "Item_Name" => "Laptop Stand", "Item_Code" => "LSTD"],
            ["SN" => 75, "Item_Name" => "Smart Board", "Item_Code" => "SBOARD"],
            ["SN" => 76, "Item_Name" => "Conference Video and Audio System", "Item_Code" => "CVAS"]
        ];
    }
}
