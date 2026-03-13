<?php

namespace App\Http\Livewire\Farmitem;

use Livewire\Component;

use Auth;
use App\Models\WithdrawalSeries             as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController    as AC;
use App\Http\Controllers\GeneralController  as GC;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\ItemList                     as IL;
use App\Models\Item                         as IT;
use App\Models\FarmInventory                as FIT;
use App\Models\InventoryHistory             as IH;
use App\Models\UnitOfMeasurement            as UOM;
use App\Models\Approvals;
use App\Models\TransactionType              as TT;
use App\Models\UsedSeries                   as US;
use App\Models\RequestItem                  as RI;
use App\Models\Category                     as CT;
use App\Models\Location                     as LC;
use App\Models\Supplier                     as SPL;
use App\Models\SubCategory                  as SCT;
use App\Models\Product                      as PRD;
use App\Models\ItemName                     as ITNAME;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;

class Farmitem extends Component
{
    public function render()
    {
        return view('livewire.farmitem.farmitem');
    }
}
