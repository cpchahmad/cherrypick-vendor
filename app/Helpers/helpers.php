<?php 
namespace App\Helpers;  
use Auth;
use App\Models\Locations;
use App\Models\ConversionRate;
use App\Models\ShipingCharges;
use App\Models\Store;
/**
 * Write code on Method
 *
 * @return response()
 */
class Helpers{
    public static function VendorID()
    {
        if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
       return $vendor_id;
    }
    public static function DiffalultLocation()
    {
        $location_data=Locations::where('is_diffault',1)->get()->toArray();
        return $location_data[0]['location_id'];
    }
    public static function StoreName()
    {
        if(Auth::user()->role=='Vendor')
        {
           $store_name=Auth::user()->name;
        }
        else
        {
            $store=Store::where('id',Auth::user()->vendor_id)->first();
            $store_name=$store->name;
        }
        return $store_name;
    }
	public static function originalPrice($products_price,$products_grams)
	{
		$usa_shipping_2000gms = 15;
        $usa_shipping_5000gms = 13;

        $usa_shipping_saree = 20;

        $usa_shipping_50gms = 2;
        $usa_shipping_100gms = 2.5;
        $usa_shipping_150gms = 3;
        $usa_shipping_200gms = 4.5;
        $usa_shipping_250gms = 5.5;
        $usa_shipping_300gms = 6;
        $usa_shipping_400gms = 8;
        $usa_shipping_500gms = 9;
        $usa_shipping_750gms = 13;
        $usa_shipping_1000gms = 16;
		$price = str_replace(',', '',$products_price);
        $price_usd = $price;
		
		$weight_in_gms=$products_grams;
        if($weight_in_gms <= 50)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_50gms), 2);
        }
        elseif($weight_in_gms <= 100)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_100gms), 2);
        }
        elseif($weight_in_gms <= 150)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_150gms), 2);
        }
        elseif($weight_in_gms <= 200)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_200gms), 2);
        }
        elseif($weight_in_gms <= 250)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_250gms), 2);
        }
        elseif($weight_in_gms <= 300)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_300gms), 2);
        } 
        elseif($weight_in_gms <= 400)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_400gms), 2);
        }
        elseif($weight_in_gms <= 500)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_500gms), 2);
        }
        elseif($weight_in_gms <= 750)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_750gms), 2);
        }
        elseif($weight_in_gms <= 1000)
        {
            $Variant_Price = round(($price_usd - $usa_shipping_1000gms), 2);
        }
        else
        {
            $Variant_Price = round(($price_usd - ($weight_in_gms * 0.016)), 2);
        }
		
		$decimal_part_usd = ltrim(($Variant_Price - floor($Variant_Price)),"0.");
        if($decimal_part_usd <= 25)
        {
            $Variant_Price=floor($Variant_Price);
        }
        elseif($decimal_part_usd >= 75)
        {
            $Variant_Price=ceil($Variant_Price);
        }
        else
        {
            $Variant_Price=floor($Variant_Price);
        }
		return $Variant_Price;
	}
	public static function finalPrice($products_price,$products_grams)
	{
		$usa_shipping_2000gms = 15;
        $usa_shipping_5000gms = 13;

        $usa_shipping_saree = 20;

        $usa_shipping_50gms = 2;
        $usa_shipping_100gms = 2.5;
        $usa_shipping_150gms = 3;
        $usa_shipping_200gms = 4.5;
        $usa_shipping_250gms = 5.5;
        $usa_shipping_300gms = 6;
        $usa_shipping_400gms = 8;
        $usa_shipping_500gms = 9;
        $usa_shipping_750gms = 13;
        $usa_shipping_1000gms = 16;
		$price = str_replace(',', '',$products_price);
        $price_usd = $price;
		
		$weight_in_gms=$products_grams;
        if($weight_in_gms <= 50)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_50gms), 2);
        }
        elseif($weight_in_gms <= 100)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_100gms), 2);
        }
        elseif($weight_in_gms <= 150)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_150gms), 2);
        }
        elseif($weight_in_gms <= 200)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_200gms), 2);
        }
        elseif($weight_in_gms <= 250)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_250gms), 2);
        }
        elseif($weight_in_gms <= 300)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_300gms), 2);
        } 
        elseif($weight_in_gms <= 400)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_400gms), 2);
        }
        elseif($weight_in_gms <= 500)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_500gms), 2);
        }
        elseif($weight_in_gms <= 750)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_750gms), 2);
        }
        elseif($weight_in_gms <= 1000)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_1000gms), 2);
        }
        else
        {
            $Variant_Price = round(($price_usd + ($weight_in_gms * 0.016)), 2);
        }
		
		$decimal_part_usd = ltrim(($Variant_Price - floor($Variant_Price)),"0.");
        if($decimal_part_usd <= 25)
        {
            $Variant_Price=floor($Variant_Price);
        }
        elseif($decimal_part_usd >= 75)
        {
            $Variant_Price=ceil($Variant_Price);
        }
        else
        {
            $Variant_Price=floor($Variant_Price);
        }
		return $Variant_Price;
	}
	public static function calc_price_fetched_products_old($products_price,$products_grams)
	{
		$usd=$products_price;
		$gbp=$products_price*1;
		$nld=round(($products_price*1.5),2);
		$cad=round(($products_price*1.33),2);
		$aud=round(($products_price*1.41),2);
		$inr=round(($products_price*82.23),2);
		
		$ind_shipping_2000gms = 15;
        $ind_shipping_5000gms = 13;
        $ind_shipping_saree = 20;
        $ind_shipping_50gms = 2;
        $ind_shipping_100gms = 2.5;
        $ind_shipping_150gms = 3;
        $ind_shipping_200gms = 4.5;
        $ind_shipping_250gms = 5.5;
        $ind_shipping_300gms = 6;
        $ind_shipping_400gms = 8;
        $ind_shipping_500gms = 9;
        $ind_shipping_750gms = 13;
        $ind_shipping_1000gms = 16;
		$weight_in_gms=$products_grams;
        if($weight_in_gms <= 50)
        {
            $base_price = round(($inr - $ind_shipping_50gms), 2);
        }
        elseif($weight_in_gms <= 100)
        {
            $base_price = round(($inr - $ind_shipping_100gms), 2);
        }
        elseif($weight_in_gms <= 150)
        {
            $base_price = round(($inr - $ind_shipping_150gms), 2);
        }
        elseif($weight_in_gms <= 200)
        {
            $base_price = round(($inr - $ind_shipping_200gms), 2);
        }
        elseif($weight_in_gms <= 250)
        {
            $base_price = round(($inr - $ind_shipping_250gms), 2);
        }
        elseif($weight_in_gms <= 300)
        {
            $base_price = round(($inr - $ind_shipping_300gms), 2);
        } 
        elseif($weight_in_gms <= 400)
        {
            $base_price = round(($inr - $ind_shipping_400gms), 2);
        }
        elseif($weight_in_gms <= 500)
        {
            $base_price = round(($inr - $ind_shipping_500gms), 2);
        }
        elseif($weight_in_gms <= 750)
        {
            $base_price = round(($inr - $ind_shipping_750gms), 2);
        }
        elseif($weight_in_gms <= 1000)
        {
            $base_price = round(($inr - $ind_shipping_1000gms), 2);
        }
        else
        {
			$base_price = round(($inr - ($weight_in_gms * 0.016)), 2);
        }
		$market_price=['usd' => $usd, 'gbp' => $gbp, 'nld' => $nld, 'inr' => $inr, 'cad' => $cad, 'aud' => $aud, 'base_price' => $base_price];
        return $market_price;
	}
	public static function calc_price_fetched_products($products_price,$products_grams)
	{
		$conversionPrice=ConversionRate::first();
		//$usd=$products_price;
		$inr=round($products_price,2);
		$usd=round(($products_price/$conversionPrice->usd_inr),2);
		$gbp=round(($inr/$conversionPrice->gbp_inr),2);
		$nld=round(($inr/$conversionPrice->euro_inr),2);
		$cad=round(($inr/$conversionPrice->cad_inr),2);
		$aud=round(($inr/$conversionPrice->aud_inr),2);
		
		///IND
		$ind_ship=ShipingCharges::where('market', 4)->first();
		$ind_shipping_50gms = $ind_ship->gms_50;
        $ind_shipping_100gms = $ind_ship->gms_100;
        $ind_shipping_150gms = $ind_ship->gms_150;
        $ind_shipping_200gms = $ind_ship->gms_200;
        $ind_shipping_250gms = $ind_ship->gms_250;
        $ind_shipping_300gms = $ind_ship->gms_300;
        $ind_shipping_400gms = $ind_ship->gms_400;
        $ind_shipping_500gms = $ind_ship->gms_500;
        $ind_shipping_750gms = $ind_ship->gms_750;
        $ind_shipping_1000gms = $ind_ship->gms_1000;
        $ind_shipping_5000gms = $ind_ship->gms_5000;
		
		$weight_in_gms=$products_grams;
        if($weight_in_gms <= 50)
        {
            $base_price = round(($inr - $ind_shipping_50gms), 2);
        }
        elseif($weight_in_gms <= 100)
        {
            $base_price = round(($inr - $ind_shipping_100gms), 2);
        }
        elseif($weight_in_gms <= 150)
        {
            $base_price = round(($inr - $ind_shipping_150gms), 2);
        }
        elseif($weight_in_gms <= 200)
        {
            $base_price = round(($inr - $ind_shipping_200gms), 2);
        }
        elseif($weight_in_gms <= 250)
        {
            $base_price = round(($inr - $ind_shipping_250gms), 2);
        }
        elseif($weight_in_gms <= 300)
        {
            $base_price = round(($inr - $ind_shipping_300gms), 2);
        } 
        elseif($weight_in_gms <= 400)
        {
            $base_price = round(($inr - $ind_shipping_400gms), 2);
        }
        elseif($weight_in_gms <= 500)
        {
            $base_price = round(($inr - $ind_shipping_500gms), 2);
        }
        elseif($weight_in_gms <= 750)
        {
            $base_price = round(($inr - $ind_shipping_750gms), 2);
        }
        elseif($weight_in_gms <= 1000)
        {
            $base_price = round(($inr - $ind_shipping_1000gms), 2);
        }
        else
        {
         $base_price = round(($inr - $ind_shipping_5000gms), 2);
			//$base_price = round(($inr - ($weight_in_gms * 0.016)), 2);
        }
		$market_price=['usd' => $usd, 'gbp' => $gbp, 'nld' => $nld, 'inr' => $inr, 'cad' => $cad, 'aud' => $aud, 'base_price' => $base_price];
        return $market_price;
	}
    public static function calc_price($products_price,$products_grams,$is_saree,$is_furniture,$volumetric_Weight)
    {
		$conversionPrice=ConversionRate::first();
		$usd_inr=$conversionPrice->usd_inr;
		$euro_inr=$conversionPrice->euro_inr;
		$gbp_inr=$conversionPrice->gbp_inr;
		$dirham_inr=$conversionPrice->dirham_inr;
		$cad_inr=$conversionPrice->cad_inr;
		$aud_inr=$conversionPrice->aud_inr;
		
		///US
		$usa_ship=ShipingCharges::where('market', 1)->first();
		$usa_shipping_50gms = $usa_ship->gms_50;
        $usa_shipping_100gms = $usa_ship->gms_100;
        $usa_shipping_150gms = $usa_ship->gms_150;
        $usa_shipping_200gms = $usa_ship->gms_200;
        $usa_shipping_250gms = $usa_ship->gms_250;
        $usa_shipping_300gms = $usa_ship->gms_300;
        $usa_shipping_400gms = $usa_ship->gms_400;
        $usa_shipping_500gms = $usa_ship->gms_500;
        $usa_shipping_750gms = $usa_ship->gms_750;
        $usa_shipping_1000gms = $usa_ship->gms_1000;
		
		///UK
		$uk_ship=ShipingCharges::where('market', 2)->first();
		$uk_shipping_50gms = $uk_ship->gms_50;
        $uk_shipping_100gms = $uk_ship->gms_100;
        $uk_shipping_150gms = $uk_ship->gms_150;
        $uk_shipping_200gms = $uk_ship->gms_200;
        $uk_shipping_250gms = $uk_ship->gms_250;
        $uk_shipping_300gms = $uk_ship->gms_300;
        $uk_shipping_400gms = $uk_ship->gms_400;
        $uk_shipping_500gms = $uk_ship->gms_500;
        $uk_shipping_750gms = $uk_ship->gms_750;
        $uk_shipping_1000gms = $uk_ship->gms_1000;
		
		///NLD
		$nld_ship=ShipingCharges::where('market', 3)->first();
		$nld_shipping_50gms = $nld_ship->gms_50;
        $nld_shipping_100gms = $nld_ship->gms_100;
        $nld_shipping_150gms = $nld_ship->gms_150;
        $nld_shipping_200gms = $nld_ship->gms_200;
        $nld_shipping_250gms = $nld_ship->gms_250;
        $nld_shipping_300gms = $nld_ship->gms_300;
        $nld_shipping_400gms = $nld_ship->gms_400;
        $nld_shipping_500gms = $nld_ship->gms_500;
        $nld_shipping_750gms = $nld_ship->gms_750;
        $nld_shipping_1000gms = $nld_ship->gms_1000;
		
		///IND
		$ind_ship=ShipingCharges::where('market', 4)->first();
		$ind_shipping_50gms = $ind_ship->gms_50;
        $ind_shipping_100gms = $ind_ship->gms_100;
        $ind_shipping_150gms = $ind_ship->gms_150;
        $ind_shipping_200gms = $ind_ship->gms_200;
        $ind_shipping_250gms = $ind_ship->gms_250;
        $ind_shipping_300gms = $ind_ship->gms_300;
        $ind_shipping_400gms = $ind_ship->gms_400;
        $ind_shipping_500gms = $ind_ship->gms_500;
        $ind_shipping_750gms = $ind_ship->gms_750;
        $ind_shipping_1000gms = $ind_ship->gms_1000;
		
		///CAD
		$cad_ship=ShipingCharges::where('market', 5)->first();
		$cad_shipping_50gms = $cad_ship->gms_50;
        $cad_shipping_100gms = $cad_ship->gms_100;
        $cad_shipping_150gms = $cad_ship->gms_150;
        $cad_shipping_200gms = $cad_ship->gms_200;
        $cad_shipping_250gms = $cad_ship->gms_250;
        $cad_shipping_300gms = $cad_ship->gms_300;
        $cad_shipping_400gms = $cad_ship->gms_400;
        $cad_shipping_500gms = $cad_ship->gms_500;
        $cad_shipping_750gms = $cad_ship->gms_750;
        $cad_shipping_1000gms = $cad_ship->gms_1000;
		
		///CAD
		$au_ship=ShipingCharges::where('market', 6)->first();
		$au_shipping_50gms = $au_ship->gms_50;
        $au_shipping_100gms = $au_ship->gms_100;
        $au_shipping_150gms = $au_ship->gms_150;
        $au_shipping_200gms = $au_ship->gms_200;
        $au_shipping_250gms = $au_ship->gms_250;
        $au_shipping_300gms = $au_ship->gms_300;
        $au_shipping_400gms = $au_ship->gms_400;
        $au_shipping_500gms = $au_ship->gms_500;
        $au_shipping_750gms = $au_ship->gms_750;
        $au_shipping_1000gms = $au_ship->gms_1000;
		
		$usa_shipping_saree = 20;
        
        #  Convert base INR price to respective currencies using currency conversion rates 
            $price = str_replace(',', '',$products_price);
            $price_inr = $price;
            $price_usd = $price_inr / $usd_inr;
            $price_euro = $price_inr / $euro_inr;
            $price_gbp = $price_inr / $gbp_inr;
            $price_aed = $price_inr / $dirham_inr;
            $price_cad = $price_inr / $cad_inr;
            $price_aud = $price_inr / $aud_inr;
            
            if($is_saree==1)
                $price_usd=round(($price_usd + $usa_shipping_saree), 2);
            elseif($is_furniture==1)
               $price_usd=round(($price_usd + ($volumetric_Weight * 0.016)), 2);
 //echo $price_usd; die;
        // echo "<br>";
        $weight_in_gms=$products_grams;
        if($weight_in_gms <= 50)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_50gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_50gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_50gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_50gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_50gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_50gms), 2);
        }
        elseif($weight_in_gms <= 100)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_100gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_100gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_100gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_100gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_100gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_100gms), 2);
        }
        elseif($weight_in_gms <= 150)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_150gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_150gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_150gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_150gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_150gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_150gms), 2);
        }
        elseif($weight_in_gms <= 200)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_200gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_200gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_200gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_200gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_200gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_200gms), 2);
        }
        elseif($weight_in_gms <= 250)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_250gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_250gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_250gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_250gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_250gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_250gms), 2);
        }
        elseif($weight_in_gms <= 300)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_300gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_300gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_300gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_300gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_300gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_300gms), 2);
			// echo $Variant_Price;
        // echo "<br>";
        } 
        elseif($weight_in_gms <= 400)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_400gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_400gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_400gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_400gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_400gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_400gms), 2);
        }
        elseif($weight_in_gms <= 500)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_500gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_500gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_500gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_500gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_500gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_500gms), 2);
        }
        elseif($weight_in_gms <= 750)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_750gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_750gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_750gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_750gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_750gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_750gms), 2);
        }
        elseif($weight_in_gms <= 1000)
        {
            $Variant_Price = round(($price_usd + $usa_shipping_1000gms), 2);
            $price_gbp_final = round(($price_gbp + $uk_shipping_1000gms), 2);
            $price_nld_final = round(($price_euro + $nld_shipping_1000gms), 2);
            $price_inr_final = round(($price_inr + $ind_shipping_1000gms), 2);
            $price_cad_final = round(($price_cad + $cad_shipping_1000gms), 2);
            $price_aud_final = round(($price_aud + $au_shipping_1000gms), 2);
        }
        else
        {
            $Variant_Price = round(($price_usd + ($weight_in_gms * 0.016)), 2);
            $price_gbp_final = round(($price_gbp + ($weight_in_gms * 0.011)), 2);
            $price_nld_final = round(($price_euro + ($weight_in_gms * 0.012)), 2);
            $price_inr_final = round(($price_inr + ($weight_in_gms * 0.016)), 2);
            $price_cad_final = round(($price_cad + ($weight_in_gms * 0.016)), 2);
            $price_aud_final = round(($price_aud + ($weight_in_gms * 0.016)), 2);
        }
		//echo $Variant_Price; die;
        $premium=0;
        if($premium==1)
        {
            $Variant_Price=round(($Variant_Price  * 1.05), 2);
            $price_gbp_final=round(($price_gbp_final * 1.05 * 1.04), 2);
            $price_nld_final=round(($price_nld_final * 1.05 * 1.04), 2);
            $price_inr_final=round(($price_inr_final * 1.05 * 1.04), 2);
            $price_cad_final=round(($price_cad_final * 1.05 * 1.04), 2);
            $price_aud_final=round(($price_aud_final * 1.05 * 1.04), 2);
        }
        else
        {
            $Variant_Price=round(($Variant_Price  * 1.04), 2);
            $price_gbp_final=round(($price_gbp_final  * 1.04), 2);
            $price_nld_final=round(($price_nld_final  * 1.04), 2);
            $price_inr_final=round(($price_inr_final  * 1.04), 2);
            $price_cad_final=round(($price_cad_final  * 1.04), 2);
            $price_aud_final=round(($price_aud_final  * 1.04), 2);
        }
		
		$decimal_part_usd = ltrim(number_format(($Variant_Price - floor($Variant_Price)),2),"0.");
        $decimal_part_gbp = ltrim(number_format(($price_gbp_final - floor($price_gbp_final)),2),"0.");
        $decimal_part_nld = ltrim(number_format(($price_nld_final - floor($price_nld_final)),2),"0.");
        $decimal_part_inr = ltrim(number_format(($price_inr_final - floor($price_inr_final)),2),"0.");
        $decimal_part_cad = ltrim(number_format(($price_cad_final - floor($price_cad_final)),2),"0.");
        $decimal_part_aud = ltrim(number_format(($price_aud_final - floor($price_aud_final)),2),"0.");
        if($decimal_part_usd <= 25)
        {
            $Variant_Price=floor($Variant_Price);
        }
        elseif($decimal_part_usd >= 75)
        {
            $Variant_Price=ceil($Variant_Price);
        }
        else
        {
            $Variant_Price=floor($Variant_Price);
        }
        
        if($decimal_part_gbp <= 25)
        {
            $price_gbp_final=floor($price_gbp_final);
        }
        elseif($decimal_part_gbp >= 75)
        {
            $price_gbp_final=ceil($price_gbp_final);
        }
        else
        {
            $price_gbp_final=floor($price_gbp_final);
        }
        
        if($decimal_part_nld <= 25)
        {
            $price_nld_final=floor($price_nld_final);
        }
        elseif($decimal_part_nld >= 75)
        {
            $price_nld_final=ceil($price_nld_final);
        }
        else
        {
            $price_nld_final=floor($price_nld_final);
        }
        
        if($decimal_part_inr <= 25)
        {
            $price_inr_final=floor($price_inr_final);
        }
        elseif($decimal_part_inr >= 75)
        {
            $price_inr_final=ceil($price_inr_final);
        }
        else
        {
            $price_inr_final=floor($price_inr_final);
        }
        
        if($decimal_part_cad <= 25)
        {
            $price_cad_final=floor($price_cad_final);
        }
        elseif($decimal_part_cad >= 75)
        {
            $price_cad_final=ceil($price_cad_final);
        }
        else
        {
            $price_cad_final=floor($price_cad_final);
        }
        
        if($decimal_part_aud <= 25)
        {
            $price_aud_final=floor($price_aud_final);
        }
        elseif($decimal_part_aud >= 75)
        {
            $price_aud_final=ceil($price_aud_final);
        }
        else
        {
            $price_aud_final=floor($price_aud_final);
        }
		//echo $Variant_Price; die;
		// echo "<br>"; die;
        //echo $Variant_Price."+++".$price_gbp_final."+++".$price_nld_final."+++".$price_inr_final;
        $market_price=['usd' => $Variant_Price, 'gbp' => $price_gbp_final, 'nld' => $price_nld_final, 'inr' => $price_inr_final, 'cad' => $price_cad_final, 'aud' => $price_aud_final];
        return $market_price;
    }
	
	public static function calc_price_new($products_price,$products_grams,$tags,$volumetric_Weight=null,$vendor)
    {
		$store=Store::where('id',$vendor)->first();
		$conversionPrice=ConversionRate::first();
		$usd_inr=$conversionPrice->usd_inr;
		$euro_inr=$conversionPrice->euro_inr;
		$gbp_inr=$conversionPrice->gbp_inr;
		$dirham_inr=$conversionPrice->dirham_inr;
		$cad_inr=$conversionPrice->cad_inr;
		$aud_inr=$conversionPrice->aud_inr;
		
		///US
		$usa_ship=ShipingCharges::where('market', 1)->first();
		$usa_shipping_50gms = $usa_ship->gms_50;
        $usa_shipping_100gms = $usa_ship->gms_100;
        $usa_shipping_150gms = $usa_ship->gms_150;
        $usa_shipping_200gms = $usa_ship->gms_200;
        $usa_shipping_250gms = $usa_ship->gms_250;
        $usa_shipping_300gms = $usa_ship->gms_300;
        $usa_shipping_400gms = $usa_ship->gms_400;
        $usa_shipping_500gms = $usa_ship->gms_500;
        $usa_shipping_750gms = $usa_ship->gms_750;
        $usa_shipping_1000gms = $usa_ship->gms_1000;
        $usa_shipping_5000gms = $usa_ship->gms_5000;
		
        $usa_shipping_50gms_savory = $usa_ship->savory_gms_50;
        $usa_shipping_100gms_savory = $usa_ship->savory_gms_100;
        $usa_shipping_150gms_savory = $usa_ship->savory_gms_150;
        $usa_shipping_200gms_savory = $usa_ship->savory_gms_200;
        $usa_shipping_250gms_savory = $usa_ship->savory_gms_250;
        $usa_shipping_300gms_savory = $usa_ship->savory_gms_300;
        $usa_shipping_400gms_savory = $usa_ship->savory_gms_400;
        $usa_shipping_500gms_savory = $usa_ship->savory_gms_500;
        $usa_shipping_750gms_savory = $usa_ship->savory_gms_750;
        $usa_shipping_1000gms_savory = $usa_ship->savory_gms_1000;
        $usa_shipping_5000gms_savory = $usa_ship->savory_gms_5000;
		
		$usa_saree = $usa_ship->saree;
		$usa_furniture = $usa_ship->furniture;
		
		///UK
		$uk_ship=ShipingCharges::where('market', 2)->first();
		$uk_shipping_50gms = $uk_ship->gms_50;
        $uk_shipping_100gms = $uk_ship->gms_100;
        $uk_shipping_150gms = $uk_ship->gms_150;
        $uk_shipping_200gms = $uk_ship->gms_200;
        $uk_shipping_250gms = $uk_ship->gms_250;
        $uk_shipping_300gms = $uk_ship->gms_300;
        $uk_shipping_400gms = $uk_ship->gms_400;
        $uk_shipping_500gms = $uk_ship->gms_500;
        $uk_shipping_750gms = $uk_ship->gms_750;
        $uk_shipping_1000gms = $uk_ship->gms_1000;
        $uk_shipping_5000gms = $uk_ship->gms_5000;
		
	$uk_shipping_50gms_savory = $uk_ship->savory_gms_50;
        $uk_shipping_100gms_savory = $uk_ship->savory_gms_100;
        $uk_shipping_150gms_savory = $uk_ship->savory_gms_150;
        $uk_shipping_200gms_savory = $uk_ship->savory_gms_200;
        $uk_shipping_250gms_savory = $uk_ship->savory_gms_250;
        $uk_shipping_300gms_savory = $uk_ship->savory_gms_300;
        $uk_shipping_400gms_savory = $uk_ship->savory_gms_400;
        $uk_shipping_500gms_savory = $uk_ship->savory_gms_500;
        $uk_shipping_750gms_savory = $uk_ship->savory_gms_750;
        $uk_shipping_1000gms_savory = $uk_ship->savory_gms_1000;
        $uk_shipping_5000gms_savory = $uk_ship->savory_gms_5000;
		
		$uk_saree = $uk_ship->saree;
		$uk_furniture = $uk_ship->furniture;
		
		///NLD
		$nld_ship=ShipingCharges::where('market', 3)->first();
		$nld_shipping_50gms = $nld_ship->gms_50;
        $nld_shipping_100gms = $nld_ship->gms_100;
        $nld_shipping_150gms = $nld_ship->gms_150;
        $nld_shipping_200gms = $nld_ship->gms_200;
        $nld_shipping_250gms = $nld_ship->gms_250;
        $nld_shipping_300gms = $nld_ship->gms_300;
        $nld_shipping_400gms = $nld_ship->gms_400;
        $nld_shipping_500gms = $nld_ship->gms_500;
        $nld_shipping_750gms = $nld_ship->gms_750;
        $nld_shipping_1000gms = $nld_ship->gms_1000;
        $nld_shipping_5000gms = $nld_ship->gms_5000;
		
	$nld_shipping_50gms_savory = $nld_ship->savory_gms_50;
        $nld_shipping_100gms_savory = $nld_ship->savory_gms_100;
        $nld_shipping_150gms_savory = $nld_ship->savory_gms_150;
        $nld_shipping_200gms_savory = $nld_ship->savory_gms_200;
        $nld_shipping_250gms_savory = $nld_ship->savory_gms_250;
        $nld_shipping_300gms_savory = $nld_ship->savory_gms_300;
        $nld_shipping_400gms_savory = $nld_ship->savory_gms_400;
        $nld_shipping_500gms_savory = $nld_ship->savory_gms_500;
        $nld_shipping_750gms_savory = $nld_ship->savory_gms_750;
        $nld_shipping_1000gms_savory = $nld_ship->savory_gms_1000;
        $nld_shipping_5000gms_savory = $nld_ship->savory_gms_5000;
		
		$nld_saree = $nld_ship->saree;
		$nld_furniture = $nld_ship->furniture;
		
		///IND
		$ind_ship=ShipingCharges::where('market', 4)->first();
		$ind_shipping_50gms = $ind_ship->gms_50;
        $ind_shipping_100gms = $ind_ship->gms_100;
        $ind_shipping_150gms = $ind_ship->gms_150;
        $ind_shipping_200gms = $ind_ship->gms_200;
        $ind_shipping_250gms = $ind_ship->gms_250;
        $ind_shipping_300gms = $ind_ship->gms_300;
        $ind_shipping_400gms = $ind_ship->gms_400;
        $ind_shipping_500gms = $ind_ship->gms_500;
        $ind_shipping_750gms = $ind_ship->gms_750;
        $ind_shipping_1000gms = $ind_ship->gms_1000;
        $ind_shipping_5000gms = $ind_ship->gms_5000;
		
	$ind_shipping_50gms_savory = $ind_ship->savory_gms_50;
        $ind_shipping_100gms_savory = $ind_ship->savory_gms_100;
        $ind_shipping_150gms_savory = $ind_ship->savory_gms_150;
        $ind_shipping_200gms_savory = $ind_ship->savory_gms_200;
        $ind_shipping_250gms_savory = $ind_ship->savory_gms_250;
        $ind_shipping_300gms_savory = $ind_ship->savory_gms_300;
        $ind_shipping_400gms_savory = $ind_ship->savory_gms_400;
        $ind_shipping_500gms_savory = $ind_ship->savory_gms_500;
        $ind_shipping_750gms_savory = $ind_ship->savory_gms_750;
        $ind_shipping_1000gms_savory = $ind_ship->savory_gms_1000;
        $ind_shipping_5000gms_savory = $ind_ship->savory_gms_5000;
		
		$ind_saree = $ind_ship->saree;
		$ind_furniture = $ind_ship->furniture;
		
		///CAD
		$cad_ship=ShipingCharges::where('market', 5)->first();
		$cad_shipping_50gms = $cad_ship->gms_50;
        $cad_shipping_100gms = $cad_ship->gms_100;
        $cad_shipping_150gms = $cad_ship->gms_150;
        $cad_shipping_200gms = $cad_ship->gms_200;
        $cad_shipping_250gms = $cad_ship->gms_250;
        $cad_shipping_300gms = $cad_ship->gms_300;
        $cad_shipping_400gms = $cad_ship->gms_400;
        $cad_shipping_500gms = $cad_ship->gms_500;
        $cad_shipping_750gms = $cad_ship->gms_750;
        $cad_shipping_1000gms = $cad_ship->gms_1000;
        $cad_shipping_5000gms = $cad_ship->gms_5000;
		
	$cad_shipping_50gms_savory = $cad_ship->savory_gms_50;
        $cad_shipping_100gms_savory = $cad_ship->savory_gms_100;
        $cad_shipping_150gms_savory = $cad_ship->savory_gms_150;
        $cad_shipping_200gms_savory = $cad_ship->savory_gms_200;
        $cad_shipping_250gms_savory = $cad_ship->savory_gms_250;
        $cad_shipping_300gms_savory = $cad_ship->savory_gms_300;
        $cad_shipping_400gms_savory = $cad_ship->savory_gms_400;
        $cad_shipping_500gms_savory = $cad_ship->savory_gms_500;
        $cad_shipping_750gms_savory = $cad_ship->savory_gms_750;
        $cad_shipping_1000gms_savory = $cad_ship->savory_gms_1000;
        $cad_shipping_5000gms_savory = $cad_ship->savory_gms_5000;
		
		$cad_saree = $cad_ship->saree;
		$cad_furniture = $cad_ship->furniture;
		
		///CAD
		$au_ship=ShipingCharges::where('market', 6)->first();
		$au_shipping_50gms = $au_ship->gms_50;
        $au_shipping_100gms = $au_ship->gms_100;
        $au_shipping_150gms = $au_ship->gms_150;
        $au_shipping_200gms = $au_ship->gms_200;
        $au_shipping_250gms = $au_ship->gms_250;
        $au_shipping_300gms = $au_ship->gms_300;
        $au_shipping_400gms = $au_ship->gms_400;
        $au_shipping_500gms = $au_ship->gms_500;
        $au_shipping_750gms = $au_ship->gms_750;
        $au_shipping_1000gms = $au_ship->gms_1000;
        $au_shipping_5000gms = $au_ship->gms_5000;
		
	$au_shipping_50gms_savory = $au_ship->savory_gms_50;
        $au_shipping_100gms_savory = $au_ship->savory_gms_100;
        $au_shipping_150gms_savory = $au_ship->savory_gms_150;
        $au_shipping_200gms_savory = $au_ship->savory_gms_200;
        $au_shipping_250gms_savory = $au_ship->savory_gms_250;
        $au_shipping_300gms_savory = $au_ship->savory_gms_300;
        $au_shipping_400gms_savory = $au_ship->savory_gms_400;
        $au_shipping_500gms_savory = $au_ship->savory_gms_500;
        $au_shipping_750gms_savory = $au_ship->savory_gms_750;
        $au_shipping_1000gms_savory = $au_ship->savory_gms_1000;
        $au_shipping_5000gms_savory = $au_ship->savory_gms_5000;
		
		$au_saree = $au_ship->saree;
		$au_furniture = $au_ship->furniture;
		
		///Irlend
		$irl_ship=ShipingCharges::where('market', 7)->first();
		$irl_shipping_50gms = $irl_ship->gms_50;
        $irl_shipping_100gms = $irl_ship->gms_100;
        $irl_shipping_150gms = $irl_ship->gms_150;
        $irl_shipping_200gms = $irl_ship->gms_200;
        $irl_shipping_250gms = $irl_ship->gms_250;
        $irl_shipping_300gms = $irl_ship->gms_300;
        $irl_shipping_400gms = $irl_ship->gms_400;
        $irl_shipping_500gms = $irl_ship->gms_500;
        $irl_shipping_750gms = $irl_ship->gms_750;
        $irl_shipping_1000gms = $irl_ship->gms_1000;
        $irl_shipping_5000gms = $irl_ship->gms_5000;
		
	$irl_shipping_50gms_savory = $irl_ship->savory_gms_50;
        $irl_shipping_100gms_savory = $irl_ship->savory_gms_100;
        $irl_shipping_150gms_savory = $irl_ship->savory_gms_150;
        $irl_shipping_200gms_savory = $irl_ship->savory_gms_200;
        $irl_shipping_250gms_savory = $irl_ship->savory_gms_250;
        $irl_shipping_300gms_savory = $irl_ship->savory_gms_300;
        $irl_shipping_400gms_savory = $irl_ship->savory_gms_400;
        $irl_shipping_500gms_savory = $irl_ship->savory_gms_500;
        $irl_shipping_750gms_savory = $irl_ship->savory_gms_750;
        $irl_shipping_1000gms_savory = $irl_ship->savory_gms_1000;
        $irl_shipping_5000gms_savory = $irl_ship->savory_gms_5000;
		
		$irl_saree = $irl_ship->saree;
		$irl_furniture = $irl_ship->furniture;
		
		///Germany
		$ger_ship=ShipingCharges::where('market', 8)->first();
		$ger_shipping_50gms = $ger_ship->gms_50;
        $ger_shipping_100gms = $ger_ship->gms_100;
        $ger_shipping_150gms = $ger_ship->gms_150;
        $ger_shipping_200gms = $ger_ship->gms_200;
        $ger_shipping_250gms = $ger_ship->gms_250;
        $ger_shipping_300gms = $ger_ship->gms_300;
        $ger_shipping_400gms = $ger_ship->gms_400;
        $ger_shipping_500gms = $ger_ship->gms_500;
        $ger_shipping_750gms = $ger_ship->gms_750;
        $ger_shipping_1000gms = $ger_ship->gms_1000;
        $ger_shipping_5000gms = $ger_ship->gms_5000;
		
	$ger_shipping_50gms_savory = $ger_ship->savory_gms_50;
        $ger_shipping_100gms_savory = $ger_ship->savory_gms_100;
        $ger_shipping_150gms_savory = $ger_ship->savory_gms_150;
        $ger_shipping_200gms_savory = $ger_ship->savory_gms_200;
        $ger_shipping_250gms_savory = $ger_ship->savory_gms_250;
        $ger_shipping_300gms_savory = $ger_ship->savory_gms_300;
        $ger_shipping_400gms_savory = $ger_ship->savory_gms_400;
        $ger_shipping_500gms_savory = $ger_ship->savory_gms_500;
        $ger_shipping_750gms_savory = $ger_ship->savory_gms_750;
        $ger_shipping_1000gms_savory = $ger_ship->savory_gms_1000;
        $ger_shipping_5000gms_savory = $ger_ship->savory_gms_5000;
		
		$ger_saree = $ger_ship->saree;
		$ger_furniture = $ger_ship->furniture;
		
		//$usa_shipping_saree = 20;
        
        #  Convert base INR price to respective currencies using currency conversion rates 
		$weight_in_gms=$products_grams;
            $price = str_replace(',', '',$products_price);
            $price_inr = $price;
            $price_usd = $price_inr / $usd_inr;
            $price_euro = $price_inr / $euro_inr;
            $price_gbp = $price_inr / $gbp_inr;
            $price_aed = $price_inr / $dirham_inr;
            $price_cad = $price_inr / $cad_inr;
            $price_aud = $price_inr / $aud_inr;
            
			$is_saree = 0;	
			$is_furniture = 0;
			$savory=0;
			if($tags!='')	
			{
				$Tags=explode(",",$tags);
				foreach($Tags as $tags_val)
				{
					
					if(strpos(strtoupper($tags_val), "SAREE") !== false)
						$is_saree = 1;
					elseif(strpos(strtoupper($tags_val),"FURNITURE") !== false)
					{
						$is_furniture = 1;
					}
					elseif(strpos(strtoupper($tags_val),"NAMKEEN") !== false)
						$savory = 1;
					elseif(strpos(strtoupper($tags_val), "KHARA") !== false)
						$savory = 1;
					// elseif(strpos("SWEETS", strtoupper($tags_val)) !== false)
						// $savory = 1;
				}
			}
			//echo $tags;
			//echo $savory; die;
            if($is_saree==1)
			{
                //$price_inr=round(($price_inr + $usa_shipping_saree), 2);
				$Variant_Price = round(($price_usd + $usa_saree), 2);
				$price_gbp_final = round(($price_gbp + $uk_saree), 2);
				$price_nld_final = round(($price_euro + $nld_saree), 2);
				$price_inr_final = round(($price_inr + $ind_saree), 2);
				$price_cad_final = round(($price_cad + $cad_saree), 2);
				$price_aud_final = round(($price_aud + $au_saree), 2);
				$price_irl_final = round(($price_euro + $irl_saree), 2);
				$price_ger_final = round(($price_euro + $ger_saree), 2);
			}
            elseif($is_furniture==1)
			{
               //$price_inr=round(($price_inr + ($volumetric_Weight * 0.016)), 2);
			    $Variant_Price = round(($price_usd + ($volumetric_Weight * $usa_furniture)), 2);
				$price_gbp_final = round(($price_gbp + ($volumetric_Weight * $uk_furniture)), 2);
				$price_nld_final = round(($price_euro + ($volumetric_Weight * $nld_furniture)), 2);
				$price_inr_final = round(($price_inr + ($volumetric_Weight * $ind_furniture)), 2);
				$price_cad_final = round(($price_cad + ($volumetric_Weight * $cad_furniture)), 2);
				$price_aud_final = round(($price_aud + ($volumetric_Weight * $au_furniture)), 2);
				$price_irl_final = round(($price_euro + ($volumetric_Weight * $irl_furniture)), 2);
				$price_ger_final = round(($price_euro + ($volumetric_Weight * $ger_furniture)), 2);
			}
			elseif($savory==1) ///////////////For savory
			{
				if($weight_in_gms <= 50)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_50gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_50gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_50gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_50gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_50gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_50gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_50gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_50gms_savory), 2);
				}
				elseif($weight_in_gms <= 100)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_100gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_100gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_100gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_100gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_100gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_100gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_100gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_100gms_savory), 2);
				}
				elseif($weight_in_gms <= 150)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_150gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_150gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_150gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_150gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_150gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_150gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_150gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_150gms_savory), 2);
				}
				elseif($weight_in_gms <= 200)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_200gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_200gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_200gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_200gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_200gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_200gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_200gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_200gms_savory), 2);
				}
				elseif($weight_in_gms <= 250)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_250gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_250gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_250gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_250gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_250gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_250gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_250gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_250gms_savory), 2);
				}
				elseif($weight_in_gms <= 300)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_300gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_300gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_300gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_300gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_300gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_300gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_300gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_300gms_savory), 2);
				} 
				elseif($weight_in_gms <= 400)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_400gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_400gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_400gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_400gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_400gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_400gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_400gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_400gms_savory), 2);
				}
				elseif($weight_in_gms <= 500)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_500gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_500gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_500gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_500gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_500gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_500gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_500gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_500gms_savory), 2);
				}
				elseif($weight_in_gms <= 750)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_750gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_750gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_750gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_750gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_750gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_750gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_750gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_750gms_savory), 2);
				}
				elseif($weight_in_gms <= 1000)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_1000gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_1000gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_1000gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_1000gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_1000gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_1000gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_1000gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_1000gms_savory), 2);
				}
				else
				{
				
				        $Variant_Price = round(($price_usd + $usa_shipping_5000gms_savory), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_5000gms_savory), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_5000gms_savory), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_5000gms_savory), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_5000gms_savory), 2);
					$price_aud_final = round(($price_aud + $au_shipping_5000gms_savory), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_5000gms_savory), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_5000gms_savory), 2);
					
					
					/*$Variant_Price = round(($price_usd + ($weight_in_gms * 0.016)), 2);
					$price_gbp_final = round(($price_gbp + ($weight_in_gms * 0.011)), 2);
					$price_nld_final = round(($price_euro + ($weight_in_gms * 0.012)), 2);
					$price_inr_final = round(($price_inr + ($weight_in_gms * 0.016)), 2);
					$price_cad_final = round(($price_cad + ($weight_in_gms * 0.016)), 2);
					$price_aud_final = round(($price_aud + ($weight_in_gms * 0.016)), 2);*/
				}
			}       
			else  //For normal product
			{
				if($weight_in_gms <= 50)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_50gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_50gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_50gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_50gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_50gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_50gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_50gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_50gms), 2);
				}
				elseif($weight_in_gms <= 100)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_100gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_100gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_100gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_100gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_100gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_100gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_100gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_100gms), 2);
				}
				elseif($weight_in_gms <= 150)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_150gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_150gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_150gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_150gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_150gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_150gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_150gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_150gms), 2);
				}
				elseif($weight_in_gms <= 200)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_200gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_200gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_200gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_200gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_200gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_200gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_200gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_200gms), 2);
				}
				elseif($weight_in_gms <= 250)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_250gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_250gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_250gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_250gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_250gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_250gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_250gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_250gms), 2);
				}
				elseif($weight_in_gms <= 300)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_300gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_300gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_300gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_300gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_300gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_300gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_300gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_300gms), 2);
				} 
				elseif($weight_in_gms <= 400)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_400gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_400gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_400gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_400gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_400gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_400gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_400gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_400gms), 2);
				}
				elseif($weight_in_gms <= 500)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_500gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_500gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_500gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_500gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_500gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_500gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_500gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_500gms), 2);
				}
				elseif($weight_in_gms <= 750)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_750gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_750gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_750gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_750gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_750gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_750gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_750gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_750gms), 2);
				}
				elseif($weight_in_gms <= 1000)
				{
					$Variant_Price = round(($price_usd + $usa_shipping_1000gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_1000gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_1000gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_1000gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_1000gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_1000gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_1000gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_1000gms), 2);
				}
				else
				{
				        $Variant_Price = round(($price_usd + $usa_shipping_5000gms), 2);
					$price_gbp_final = round(($price_gbp + $uk_shipping_5000gms), 2);
					$price_nld_final = round(($price_euro + $nld_shipping_5000gms), 2);
					$price_inr_final = round(($price_inr + $ind_shipping_5000gms), 2);
					$price_cad_final = round(($price_cad + $cad_shipping_5000gms), 2);
					$price_aud_final = round(($price_aud + $au_shipping_5000gms), 2);
					$price_irl_final = round(($price_euro + $irl_shipping_5000gms), 2);
					$price_ger_final = round(($price_euro + $ger_shipping_5000gms), 2);
					
					/*$Variant_Price = round(($price_usd + ($weight_in_gms * 0.016)), 2);
					$price_gbp_final = round(($price_gbp + ($weight_in_gms * 0.011)), 2);
					$price_nld_final = round(($price_euro + ($weight_in_gms * 0.012)), 2);
					$price_inr_final = round(($price_inr + ($weight_in_gms * 0.016)), 2);
					$price_cad_final = round(($price_cad + ($weight_in_gms * 0.016)), 2);
					$price_aud_final = round(($price_aud + ($weight_in_gms * 0.016)), 2);*/
				}
			}

		//echo $Variant_Price; die;
        //$premium=0;
        if($store->premium==1)
        {
            $Variant_Price=round(($Variant_Price  * 1.05), 2);
            $price_gbp_final=round(($price_gbp_final * 1.05 * 1.04), 2);
            $price_nld_final=round(($price_nld_final * 1.05 * 1.04), 2);
            $price_inr_final=round(($price_inr_final * 1.05 * 1.04), 2);
            $price_cad_final=round(($price_cad_final * 1.05 * 1.04), 2);
            $price_aud_final=round(($price_aud_final * 1.05 * 1.04), 2);
			$price_irl_final=round(($price_irl_final * 1.05 * 1.04), 2);
			$price_ger_final=round(($price_ger_final * 1.05 * 1.04), 2);
        }
        else
        {
            $Variant_Price=round($Variant_Price, 2);
            $price_gbp_final=round(($price_gbp_final  * 1.04), 2);
            $price_nld_final=round(($price_nld_final  * 1.04), 2);
            $price_inr_final=round(($price_inr_final  * 1.04), 2);
            $price_cad_final=round(($price_cad_final  * 1.04), 2);
            $price_aud_final=round(($price_aud_final  * 1.04), 2);
			$price_irl_final=round(($price_irl_final  * 1.04), 2);
			$price_ger_final=round(($price_ger_final  * 1.04), 2);
        }
		
		$decimal_part_usd = ltrim(number_format(($Variant_Price - floor($Variant_Price)),2),"0.");
        $decimal_part_gbp = ltrim(number_format(($price_gbp_final - floor($price_gbp_final)),2),"0.");
        $decimal_part_nld = ltrim(number_format(($price_nld_final - floor($price_nld_final)),2),"0.");
        $decimal_part_inr = ltrim(number_format(($price_inr_final - floor($price_inr_final)),2),"0.");
        $decimal_part_cad = ltrim(number_format(($price_cad_final - floor($price_cad_final)),2),"0.");
        $decimal_part_aud = ltrim(number_format(($price_aud_final - floor($price_aud_final)),2),"0.");
		$decimal_part_irl = ltrim(number_format(($price_irl_final - floor($price_irl_final)),2),"0.");
		$decimal_part_ger= ltrim(number_format(($price_ger_final - floor($price_ger_final)),2),"0.");
        if($decimal_part_usd < 25)
        {
            $Variant_Price=floor($Variant_Price);
        }
        elseif($decimal_part_usd > 50)
        {
            $Variant_Price=ceil($Variant_Price);
        }
        else
        {
            $Variant_Price=floor($Variant_Price).".5";
        }
        
        if($decimal_part_gbp < 25)
        {
            $price_gbp_final=floor($price_gbp_final);
        }
        elseif($decimal_part_gbp > 50)
        {
            $price_gbp_final=ceil($price_gbp_final);
        }
        else
        {
            $price_gbp_final=floor($price_gbp_final).".5";
        }
        
        if($decimal_part_nld < 25)
        {
            $price_nld_final=floor($price_nld_final);
        }
        elseif($decimal_part_nld > 50)
        {
            $price_nld_final=ceil($price_nld_final);
        }
        else
        {
            $price_nld_final=floor($price_nld_final).".5";
        }
		
		if($decimal_part_irl < 25)
        {
            $price_irl_final=floor($price_irl_final);
        }
        elseif($decimal_part_irl > 50)
        {
            $price_irl_final=ceil($price_irl_final);
        }
        else
        {
            $price_irl_final=floor($price_irl_final).".5";
        }
		
		if($decimal_part_ger < 25)
        {
            $price_ger_final=floor($price_ger_final);
        }
        elseif($decimal_part_ger > 50)
        {
            $price_ger_final=ceil($price_ger_final);
        }
        else
        {
            $price_ger_final=floor($price_ger_final).".5";
        }
        
        if($decimal_part_inr < 50)
        {
            $price_inr_final=floor($price_inr_final);
        }
        elseif($decimal_part_inr >= 50)
        {
            $price_inr_final=ceil($price_inr_final);
        }
        
        if($decimal_part_cad < 25)
        {
            $price_cad_final=floor($price_cad_final);
        }
        elseif($decimal_part_cad > 50)
        {
            $price_cad_final=ceil($price_cad_final);
        }
        else
        {
            $price_cad_final=floor($price_cad_final).".5";
        }
        
        if($decimal_part_aud < 25)
        {
            $price_aud_final=floor($price_aud_final);
        }
        elseif($decimal_part_aud > 50)
        {
            $price_aud_final=ceil($price_aud_final);
        }
        else
        {
            $price_aud_final=floor($price_aud_final).".5";
        }
		//echo $Variant_Price; die;
		// echo "<br>"; die;  
        //echo $Variant_Price."+++".$price_gbp_final."+++".$price_nld_final."+++".$price_inr_final;
        $market_price=['usd' => $Variant_Price, 'gbp' => $price_gbp_final, 'nld' => $price_nld_final, 'inr' => $price_inr_final, 'cad' => $price_cad_final, 'aud' => $price_aud_final, 'irl' => $price_irl_final, 'ger' => $price_ger_final];
        return $market_price;
    }
}
