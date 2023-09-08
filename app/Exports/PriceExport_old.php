<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Product;
use App\Models\ProductInfo;
use Auth;
use App\Helpers\Helpers;
class PriceExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function collection()
    {
        $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
        $str=file_get_contents("https://yellowverandah.in/collections/all/products.json?limit=100", false, $context);
        $arr=json_decode($str,true);
        foreach($arr['products'] as $val)
            {
                $Tags=$val['tags'];
                if(!in_array( "HSN" ,$Tags ))
                {
                    $tag = 'HSN:21069099';
                }
                else
                    $tag = '';

                if(in_array("Namkeen",$Tags))
                {
                    $savory = 1;
                }
                else if(in_array( "Khara" ,$Tags ))
                {
                    $savory = 1;
                }
                else
                {
                    $savory = 0;
                }
                if(in_array("Saree",$Tags))
                    $is_saree = 1;
                else
                    $is_saree = 0;
                if(in_array("furniture",$Tags))
                {
                    $is_furniture = 1;
                    $volumetric_Weight = 10000/5000;
                }
                else
                {
                    $is_furniture = 0;
                    $volumetric_Weight = 0;
                }
                $tags_str=implode(',',$Tags).",".$tag;
                foreach($val['variants'] as $var_row)
                {
                $price_arr=Helpers::calc_price($var_row['price'],$var_row['grams'],$is_saree,$is_furniture,$volumetric_Weight);
                //echo "<pre>"; print_r($price_arr); die();
                $price=$var_row['price'];
                $weight=$var_row['grams'];
                $arr_data[]=[
                    $val['handle'],
                    $val['title'],
                    $val['body_html'],
                    $val['vendor'],
                    '',
                    $val['product_type'],
                    $tags_str,
                    'TRUE',
                    $var_row['title'],
                    $var_row['option1'],
                    '',
                    '',
                    '',
                    '',
                    $var_row['sku'],
                    $var_row['grams'],
                    '',
                    $var_row['available'],
                    'deny',
                    'manual',
                    $var_row['price'],
                    '',
                    'TRUE',
                    'FALSE',
                    '',
                    '',
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
                    '',
                    'g',
                    '',
                    '',
                    $price_arr['aud'],
                    '',
                    $price_arr['cad'],
                    '',
                    $price_arr['gbp'],
                    '',
                    $price_arr['inr'],
                    '',
                    $price_arr['nld'],
                    '',
                    $price_arr['usd'],
                    '',
                    'active'
                    ];
                }
            }
        return collect($arr_data);
    }
    public function headings(): array
    {
//        return ["Title", "HSN", "price", "weight", "USD", "GBP", "NLD", "INR"];
        return ["Handle", "Title", "Body (HTML)", "Vendor", "Product Category", "Type", "Tags", "Published", "Option1 Name", "Option1 Value", "Option2 Name", "Option2 Value", "Option3 Name", "Option3 Value", "Variant SKU", "Variant Grams", "Variant Inventory Tracker", "Variant Inventory Qty", "Variant Inventory Policy", "Variant Fulfillment Service", "Variant Price", "Variant Compare At Price", "Variant Requires Shipping", "Variant Taxable", "Variant Barcode", "Image Src", "Image Position", "Image Alt Text", "Gift Card", "SEO Title", "SEO Description", "Google Shopping / Google Product Category", "Google Shopping / Gender", "Google Shopping / Age Group", "Google Shopping / MPN", "Google Shopping / AdWords Grouping", "Google Shopping / AdWords Labels", "Google Shopping / Condition", "Google Shopping / Custom Product", "Google Shopping / Custom Label 0", "Google Shopping / Custom Label 1", "Google Shopping / Custom Label 2", "Google Shopping / Custom Label 3", "Google Shopping / Custom Label 4", "Variant Image", "Variant Weight Unit", "Variant Tax Code", "Cost per item", "Price / AUD", "Compare At Price / AUD", "Price / CAD", "Compare At Price / CAD", "Price / GBP", "Compare At Price / GBP", "Price / INR", "Compare At Price / INR", "Price / NLD", "Compare At Price / NLD", "Price / USD", "Compare At Price / USD", "Status"];
    }
}
