<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Test;
use App\Models\Product;
use App\Models\ProductInfo;
use App\Models\Category;
use App\Models\Locations;
use App\Models\ProductInventoryLocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;
class InventoryImport implements ToModel, WithStartRow
{
    /**
    * @param Collection $collection
    */
    public $key=array();
    private $rows = 0;
 public function startRow(): int
    {
        return 1;
    }
 
    public function model(array $row)
    {
        //print_r($this->key);
        if($this->rows==0)
        {
            foreach($row as $k=>$v)
            {
                if($k!=0)
                {
                    $location_data=Locations::where('name',$v)->get()->toArray();
                    if($location_data)
                        $this->key[$k]=$location_data[0]['location_id'];
                    else
                        $this->key[$k]='';
                }
                else
                 $this->key[$k]=$v;
            }
        }
        else
        {
                $inventory_item_id_data=ProductInfo::where('sku',$row[0])->first();
                foreach($this->key as $kk=>$kv)
                {
                    if($kk > 0)
                    {
                        if($kv!='')
                        {
                        ProductInventoryLocation::updateOrCreate(
                                ['items_id' => $inventory_item_id_data->inventory_item_id, 'location_id' => $kv],
                                ['items_id' => $inventory_item_id_data->inventory_item_id, 'stock' => $row[$kk], 'location_id' => $kv]
                            );
                        }
                    }
                }
        }
        ++$this->rows;        
    }
}
