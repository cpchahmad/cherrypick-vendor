<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Auth;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\Store;
use App\Models\ProductImages;
use App\Models\Banner;
use DB;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function cronInventoryUpdate()
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/inventory_levels/set.json";
        $product_inv = ProductInfo::where('stock', '>', 0)->whereNotNull('inventory_item_id')->get();
        foreach($product_inv as $row)
        {
            $data=array(
                'location_id' => '62600577199',
                'inventory_item_id' => $row['inventory_item_id'],
                'available_adjustment' => $row['stock']
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
            $headers = array(
                "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
                "Content-Type: application/json",
                "charset: utf-8"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
            //curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec ($curl);
            curl_close ($curl);
        }
        //$result=json_decode($response, true);
        //echo "<pre>"; print_r($result); die();
    }
    public function testinv() 
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/price_rules.json";
        $data['price_rule']=array(
            'title' => 'OMTest',
            'target_type' => 'line_item',
            'target_selection' => 'entitled',
            'allocation_method' => 'across',
            'customer_selection' => 'all',
            'value_type' => 'percentage',
            'value' => '-5',
            'starts_at' => '2022-12-18T10:00:00Z',
            'entitled_product_ids' => ['7373527842991'],
            'entitled_variant_ids' => [],
            'entitled_collection_ids' => [],
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result); die();
    }
    public function testcode() 
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/price_rules/1080977260719/discount_codes.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"discount_code":{"code":"10OFF"}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result); die();
    }
    public function exportProduct() 
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }
    public function importProduct() 
    {
        Excel::import(new ProductImport,request()->file('file'));
               
        return back();
    }
    public function importProductView()
    {
       return view('subadmin.import-product');
    }
    public function productview(){
      $id = Auth::id();
    	$category = Category::all();
    	return view('subadmin.add-product',compact('category'));
    }
    public function savebanner(Request $request)
    {
        $home_desktop_banner='';
        $home_mobile_banner='';
        $store_desktop_banner='';
        $store_mobile_banner='';
        if($request->hasfile('home_desktop_banner')){
            $file = $request->file('home_desktop_banner');
            $extension = $file->getClientOriginalExtension();
            $home_desktop_banner = 'home_desktop_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$home_desktop_banner);
          }
          if($request->hasfile('home_mobile_banner')){
            $file = $request->file('home_mobile_banner');
            $extension = $file->getClientOriginalExtension();
            $home_mobile_banner = 'home_mobile_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$home_mobile_banner);
          }
          if($request->hasfile('store_desktop_banner')){
            $file = $request->file('store_desktop_banner');
            $extension = $file->getClientOriginalExtension();
            $store_desktop_banner = 'store_desktop_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$store_desktop_banner);
          }
          if($request->hasfile('store_mobile_banner')){
            $file = $request->file('store_mobile_banner');
            $extension = $file->getClientOriginalExtension();
            $store_mobile_banner = 'store_mobile_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$store_mobile_banner);
          }
          $res=new Banner;
          $res->home_desktop_banner=$home_desktop_banner;
          $res->home_mobile_banner=$home_desktop_banner;
          $res->store_desktop_banner=$store_desktop_banner;
          $res->store_mobile_banner=$store_mobile_banner;
          $res->vendor_id=Auth::id();
          $res->save();
          return redirect()->route('admin.banner');
    }
    public function saveproduct(Request $request){
      //echo "<pre>"; print_r($request->file()); die();
        if($request->hasfile('profile')){
            $file = $request->file('profile');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/profile/',$filename);
            $product = new ProductImages;
            $product->image = url('uploads/profile/'.$filename);
            $product->save();
            $product_id=$product->id;
            $response['success'] = true;
            $response['message'] = $product_id;
          }          
        return json_encode($response);
  }
   public function vendorId(){
       if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
       return $vendor_id;
   }
   public function saveproducts(Request $request){
     // echo "<pre>"; print_r($request->all()); die();
     $input = $request->all();
    if($request->payradious =='1'){
       $this->validate($request,([
        'name'=>'required',
        'description'=>'required',
        'tags'=>'required',
       ]));
     }
     else{
       $request->validate([
        'name'=>'required',
        'description'=>'required',
        'tags'=>'required',
        'price'=>'required',
        'sku'=>'required',
        'grams'=>'required',
        'quantity'=>'required',
        'category'=>'required',
        ]);
       } 
        $vendor=$this->vendorId();
        $product = new Product;
        $product->title = $request->name;
        $product->body_html = $request->description;
        $product->vendor = $vendor;
        $product->tags = $request->tags;
        $product->is_variants = $request->payradious;
        $product->category = $request->category;
        $product->save(); 
        $product_id=$product->id;
        if($request->payradious!=1)
        {
            $product_info = new ProductInfo;
            $product_info->product_id = $product_id;
            $product_info->sku = $request->sku;
            $product_info->price = $request->price;
            $product_info->grams = $request->grams;
            $product_info->stock = $request->quantity;
            $product_info->shelf_life = $request->shelf_life;
            $product_info->temp_require = $request->temp;
            $product_info->dimensions = $request->height.'-'.$request->width.'-'.$request->length;
            $product_info->vendor_id = $vendor;
            $product_info->save();
        }
        else
        {
            foreach($request->varient_name as $key => $value) {
            $product_info = new ProductInfo;
            $product_info->product_id = $product_id;
            $product_info->vendor_id = Auth::user()->id;
            $product_info->sku = $request->varient_sku[$key];;
            $product_info->varient_name = $request->varient_name[$key];
            $product_info->varient_value = $request->varient_value[$key];
            $product_info->price = $request->varient_price[$key];
            $product_info->grams = $request->varient_grams[$key];
            $product_info->stock = $request->varient_quantity[$key];
            $product_info->shelf_life = $request->varient_shelf_life[$key];
            $product_info->temp_require = $request->varient_temp[$key];
            $product_info->dimensions = $request->varient_height[$key].'-'.$request->varient_width[$key].'-'.$request->varient_length[$key];
            $product_info->save();
       }
       }
       if($request->images!='')
       {
           $img_arr=explode(",",$request->images);
           foreach($img_arr as $v)
           {
               ProductImages::where('id', $v)->update(['product_id' => $product_id]);
           }
       }
        return redirect()->route('product-list');
     }

     public function updateProducts(Request $request)
     {
        if($request->payradious =='1'){
           $this->validate($request,([
            'name'=>'required',
            'description'=>'required',
            'tags'=>'required',
           ]));
         }
         else{
           $request->validate([
            'name'=>'required',
            'description'=>'required',
            'tags'=>'required',
            'price'=>'required',
            'sku'=>'required',
            'grams'=>'required',
            'quantity'=>'required',
            'category'=>'required',
            ]);
           }
        $product =Product::find($request->pid);
        $product->title = $request->name;
        $product->body_html = $request->description;
        $product->vendor = Auth::user()->id;
        $product->tags = $request->tags;
        $product->is_variants = $request->payradious;
        $product->category = $request->category;
        $product->save(); 
        $product_id=$request->pid;
        if($product)
        {
            DB::table('products_variants')->where('product_id', $product_id)->delete();
            if($request->payradious!=1)
            {
                $product_info = new ProductInfo;
                $product_info->product_id = $product_id;
                $product_info->sku = $request->sku;
                $product_info->price = $request->price;
                $product_info->grams = $request->grams;
                $product_info->stock = $request->quantity;
                $product_info->shelf_life = $request->shelf_life;
                $product_info->temp_require = $request->temp;
                $product_info->dimensions = $request->height.'-'.$request->width.'-'.$request->length;
                $product_info->save();
            }
            else
            {
                foreach($request->varient_name as $key => $value) {
                $product_info = new ProductInfo;
                $product_info->product_id = $product_id;
                $product_info->sku = $request->varient_sku[$key];
                $product_info->varient_name = $request->varient_name[$key];
                $product_info->varient_value = $request->varient_value[$key];
                $product_info->price = $request->varient_price[$key];
                $product_info->grams = $request->varient_grams[$key];
                $product_info->stock = $request->varient_quantity[$key];
                $product_info->shelf_life = $request->varient_shelf_life[$key];
                $product_info->temp_require = $request->varient_temp[$key];
                $product_info->dimensions = $request->varient_height[$key].'-'.$request->varient_width[$key].'-'.$request->varient_length[$key];
                $product_info->save();
            }
           }
       }
       if($request->images!='')
       {
           $img_arr=explode(",",$request->images);
           foreach($img_arr as $v)
           {
               ProductImages::where('id', $v)->update(['product_id' => $product_id]);
           }
       }
       return redirect()->route('product-list');
     }
    public function productlist( Request $request){
      $res = Product::where('vendor', Auth::id());
      if($request->search != ""){
          $res->where('title' , 'LIKE', '%' . $request->search . '%');
      }
      $product = $res->orderBy('id', 'DESC')->paginate(20);
      return view('subadmin.view-products',compact('product'));
    }
    public function outOfStockProductsList(Request $request)
    {
        $res = ProductInfo::select('product_master.id as pid','product_master.title','product_master.is_variants','products_variants.varient_name','products_variants.varient_value','products_variants.stock','products_variants.id')->join('product_master','product_master.id','products_variants.product_id')
                ->where('product_master.vendor', Auth::id())
                ->where('products_variants.stock', 0);
        if($request->search != ""){
          $res->where('product_master.title' , 'LIKE', '%' . $request->search . '%');
        }
        $product = $res->orderBy('product_master.id', 'DESC')->paginate(20);
        return view('subadmin.outofstock-products',compact('product'));
    }
    public function updateStock(Request $request)
    {
        $product =ProductInfo::find($request->id);
        $product->stock = $request->qty;
        $product->save();
        return json_encode(array('status'=>'success','qty'=>$request->qty));
    }
    public function deleteproduct(Request $request,$id){
      $res = Product::findOrFail($id);
      $res->delete();
      DB::table('products_variants')->where('product_id', $id)->delete();
      DB::table('products_images')->where('product_id', $id)->delete();
      return redirect()->route('product-list')->with('success','Product Deleted.');
    }
    public function deleteImage($id){
      $res = ProductImages::findOrFail($id);
      $res->delete();
    }
    public function editproduct($id){
      $product = Product::find($id);
      $prodcut_info=ProductInfo::where('product_id',$id)->get();
      $prodcut_images=ProductImages::where('product_id',$id)->get();
      $category = Category::all();
      return view('subadmin.edit-product',compact('product','category','prodcut_info','prodcut_images'));

    }
    public function uploadeImage()
    {
        
        $API_KEY = '03549b537b31aeff2bdc45aa7c98d06d';
        $PASSWORD = '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';
        $SHOP_URL = 'mystore-3220.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/8047518646558/images.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"image":{"src":"https://sslimages.shoppersstop.com/sys-master/images/h98/hcf/28719001468958/GHM9150K_BLUE.jpg_230Wx334H"}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, 'https://mystore-3220.myshopify.com/admin/api/2022-10/products/8047518646558/images.json');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            'X-Shopify-Access-Token' => '03549b537b31aeff2bdc45aa7c98d06d',
//            'Content-Type' => 'application/json',
//        ]);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"image":{"src":"https://cdn57.androidauthority.net/wp-content/uploads/2019/02/Acer-Swift-7-840x472.jpg"}}');
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        $response = curl_exec($ch);
//
//        curl_close($ch);
        if($errno = curl_errno($curl)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
}
echo $response;
    }
    public function createProductShopifyMultiple()
    {
        $product_data = Product::where('status', 0)->where('vendor', Auth::id())->get();
        foreach($product_data as $product)
        {
        $category=Category::find($product->category);
            $variants=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
        $products_array = array(
            "product" => array( 
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       =>  Auth::user()->name,
                "product_type" => $category->category,
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
            )
        );
        //echo "<pre>"; print_r($products_array); die();
//        $API_KEY = '03549b537b31aeff2bdc45aa7c98d06d';
//        $PASSWORD = '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';
//        $SHOP_URL = 'mystore-3220.myshopify.com';
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        //echo "<pre>"; print_r($result); die();
        $shopify_product_id=$result['product']['id'];
        Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id, 'status' => '1']);
        $this->shopifyUploadeImage($product->id,$shopify_product_id);
        }
//        echo "<pre>";
//        echo $response;
//        print_r($result);
//        echo "</pre>";
//        echo $result['product']['id'];
        //return redirect()->route('product-list')->with('success','Product Created Successfully.');
    }
    public function createProductShopify($id)
    {
        $product = Product::find($id);
        $category=Category::find($product->category);
//        if($product->is_variants==1)
//        {
            $variants=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
        //}
        $products_array = array(
            "product" => array( 
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       =>  Auth::user()->name,
                "product_type" => $category->category,
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
            )
        );
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        //echo "<pre>"; print_r($result); die();
        $shopify_product_id=$result['product']['id'];
        Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id]);
        $this->shopifyUploadeImage($product->id,$shopify_product_id);
//        echo "<pre>";
//        echo $response;
//        print_r($result);
        foreach($result['product']['variants'] as $prd)
        {
            ProductInfo::where('sku', $prd['sku'])->update(['inventory_item_id' => $prd['inventory_item_id']]);
        }
        return redirect()->route('product-list')->with('success','Product Created Successfully.');
    }
    public function shopifyUploadeImage($id,$shopify_id)
    {        
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shopify_id/images.json";
        $product_images = ProductImages::where('product_id',$id)->get();
        foreach($product_images as $img_val)
        {
            $image='{"image":{"src":"https://sslimages.shoppersstop.com/sys-master/images/h98/hcf/28719001468958/GHM9150K_BLUE.jpg_230Wx334H"}}';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
            $headers = array(
                "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
                "Content-Type: application/json",
                "charset: utf-8"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
            //curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $image);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
        }
    }
    public function updateProductShopify($id)
    {
        $product = Product::find($id);
        $category=Category::find($product->category);
//        if($product->is_variants==1)
//        {
            $variants=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
        //}
        $products_array = array(
            "product" => array( 
                "id"        => $product->shopify_id,
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       =>  Auth::user()->name,
                "product_type" => $category->category,
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
            )
        );
        $shop=$product->shopify_id;
        $API_KEY = '03549b537b31aeff2bdc45aa7c98d06d';
        $PASSWORD = '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';
        $SHOP_URL = 'mystore-3220.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shop.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        if($errno = curl_errno($curl)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
}
echo "ok";
    }
    public function fetchShopifyOrders()
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
//        echo "<pre>";
//        print_r($result);
        $data=array();
        $arr_key=0;
        foreach($result['orders'] as $k=>$v)
        {
            echo "<pre>";
            //echo $k;
           //print_r($v['line_items']);
            $i=0;
            foreach($v['line_items'] as $item_val)
            {
                if($item_val['vendor']==Auth::user()->name)
                {
                    $i=1;
//                    echo $v['id']."==".$item_val['id'];
//                    echo "<br>";
                    $data[$arr_key]['line_items'][]=array(
                            'id' => $item_val['id'],
                            'name' => $item_val['name']
                        );
                }
            }
            if($i==1)
            {
                $data[$arr_key]['id']=$v['id'];
                $data[$arr_key]['created_at']=$v['created_at'];
                $data[$arr_key]['current_total_price']=$v['current_total_price'];
                $data[$arr_key]['fulfillment_status']=$v['fulfillment_status'];
            }
            $arr_key++;
        }
        echo "<pre>"; print_r($data);
//        foreach($result['orders'] as $v)
//        {
//            echo $v['order_number'].",Date=".$v['created_at'].",Total price=".$v['current_total_price'].",Paid=".$v['financial_status'];
//            echo "<br>";
//        }
    }
    public function allOrders()
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?limit=20";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        $data=$result['orders'];
        return view('subadmin.orders',compact('data'));
    }
    public function detailsShopifyOrders($id)
    {
      
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders/$id.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        //echo "<pre>"; print_r($result); die();
        $data=$result['order'];
       // echo "<pre>"; print_r($data); die();
        return view('subadmin.orders-details',compact('data'));
    }
    public function openOrders()
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?status=open";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        $data=$result['orders'];
        return view('subadmin.open-orders',compact('data'));
    }
    public function closeOrders()
    {
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?status=closed";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        $data=$result['orders'];
        return view('subadmin.close-orders',compact('data'));
    }
    public function fetchProductFromUrl($url)
    {
        set_time_limit(0);
            $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
            $str_cnt=file_get_contents("https://".$url."/collections/all/count.json", false, $context);
            $arr_1=json_decode($str_cnt,true);
            $page_total=$arr_1['collection']['products_count'];
            $page_count=ceil($page_total/250);
            for($i=1;$i<=$page_count;$i++)
            {
                $str=file_get_contents("https://".$url."/collections/all/products.json?page=".$i."&limit=250", false, $context);
                $arr=json_decode($str,true);
                foreach($arr['products'] as $val)
                {
                    $cat=Category::where('category',$val['product_type'])->first();
                    if($cat)
                        $category_id=$cat->id;
                    else
                    {
                        $cate_que = new Category;
                        $cate_que->category = $val['product_type'];
                        $cate_que->save(); 
                        $category_id=$cate_que->id;
                    }
                    ///
                    $product_exits =ProductInfo::where('sku',$val['variants'][0]['sku'])->count();
                    if(!$product_exits)
                    {
                        $product = new Product;
                        $product->title = $val['title'];
                        $product->handle = $val['handle'];
                        $product->body_html = $val['body_html'];
                        $product->vendor = Auth::user()->id;
                        $product->tags = implode(",",$val['tags']);
                        $product->category = $category_id;
                        $product->save(); 
                        $product_id=$product->id;

                        ///Product variants
                        $product_info = new ProductInfo;
                        $product_info->product_id = $product_id;
                        $product_info->sku = $val['variants'][0]['sku'];
                        $product_info->price = $val['variants'][0]['price'];
                        $product_info->grams = $val['variants'][0]['grams'];
                        $product_info->stock = $val['variants'][0]['available'];
                        $product_info->vendor_id = Auth::user()->id;
                        $product_info->dimensions = '0-0-0';
                        $product_info->save();

                        foreach($val['images'] as $img_val)
                        {
                            $url = $img_val['src'];
                            $img = "uploads/shopifyimages/".$img_val['id'].".jpg";
                            file_put_contents($img, file_get_contents($url));
                            $img_name=url($img);
                            $product_img = new ProductImages;
                            $product_img->image = $img_name;
                            $product_img->product_id = $product_id;
                            $product_img->save();                       
                        }
                    }
                }
            }
    }
    
    public function curlTest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://Fav]}}' -X POST https://pradeep-6342.myshopify.com/admin/api/2022-10/products.json -H X-Shopify-Access-Token:prtapi_761cfde0a831dfaf6d5ee99bfef8846b");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"product":{"title":"Burton Custom Freestyle 151","body_html":"<strong>Good snowboard!</strong>","vendor":"Burton","product_type":"Snowboard","tags":["Barnes & Noble","Big Air","Johns');
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response."==----";
    }
}
