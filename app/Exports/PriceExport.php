<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Product;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\Store;
use Auth;
use App\Helpers\Helpers;
class PriceExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	protected $vendor_id;
    public function __construct($id)
	{
		$this->vendor_id=$id;
	}
    public function collection()
    {
        $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
        $data=Product::where('vendor', $this->vendor_id)->get();
		if(count($data) > 0)
		{
        foreach($data as $val)
            {
				$store=Store::find($this->vendor_id);
				$info=ProductInfo::where('product_id', $val->id)->get();
                foreach($info as $var_row)
                {
					$images=ProductImages::where('product_id', $val->id)->whereNull('variant_ids')->first();
					if($images)
						$img=$images->image;
					else
						$img='';
					
					$variantImages=ProductImages::where('variant_ids', $var_row->id)->first();
					if($variantImages)
						$vImg=$variantImages->image;
					else
						$vImg='';
                $arr_data[]=[
                    $val->handle,
                    $val->title,
                    $val->body_html,
                    $store->name,
                    '',
                    $val->product_type,
                    $val->tags,
                    'TRUE',
                    $var_row->varient_name,
                    $var_row->varient_value,
                    '',
                    '',
                    '',
                    '',
                    $var_row->sku,
                    $var_row->grams,
                    '',
                    $var_row->stock,
                    'deny',
                    'manual',
                    $var_row->price_usd,
                    '',
                    'TRUE',
                    'FALSE',
                    '',
                    $img,
                    '1',
                    '',
                    'FALSE',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'FALSE',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $vImg,
                    'g',
                    '',
                    '',
                    $var_row->price_aud,
                    '',
                    $var_row->price_cad,
                    '',
                    $var_row->price_gbp,
                    '',
                    $var_row->price,
                    '',
                    $var_row->price_nld,
                    '',
                    $var_row->price_usd,
                    '',
                    'active'
                    ];
                }
            }
        return collect($arr_data);
		}
		else
		{
			$arr_data=array();
			return collect($arr_data);
		}
    }
    public function headings(): array
    {
//        return ["Title", "HSN", "price", "weight", "USD", "GBP", "NLD", "INR"];
        return ["Handle", "Title", "Body (HTML)", "Vendor", "Product Category", "Type", "Tags", "Published", "Option1 Name", "Option1 Value", "Option2 Name", "Option2 Value", "Option3 Name", "Option3 Value", "Variant SKU", "Variant Grams", "Variant Inventory Tracker", "Variant Inventory Qty", "Variant Inventory Policy", "Variant Fulfillment Service", "Variant Price", "Variant Compare At Price", "Variant Requires Shipping", "Variant Taxable", "Variant Barcode", "Image Src", "Image Position", "Image Alt Text", "Gift Card", "SEO Title", "SEO Description", "Google Shopping / Google Product Category", "Google Shopping / Gender", "Google Shopping / Age Group", "Google Shopping / MPN", "Google Shopping / AdWords Grouping", "Google Shopping / AdWords Labels", "Google Shopping / Condition", "Google Shopping / Custom Product", "Google Shopping / Custom Label 0", "Google Shopping / Custom Label 1", "Google Shopping / Custom Label 2", "Google Shopping / Custom Label 3", "Google Shopping / Custom Label 4", "Variant Image", "Variant Weight Unit", "Variant Tax Code", "Cost per item", "Price / AUD", "Compare At Price / AUD", "Price / CAD", "Compare At Price / CAD", "Price / GBP", "Compare At Price / GBP", "Price / INR", "Compare At Price / INR", "Price / NLD", "Compare At Price / NLD", "Price / USD", "Compare At Price / USD", "Status"];
    }
}
