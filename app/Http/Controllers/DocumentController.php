<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Store;
use Auth;
use Illuminate\Support\Facades\Storage;
use file;


class DocumentController extends Controller
{
    public function documentview(){
    	$id = Auth::id();
    	$data = Document::where('vendor_id',$id)->get();
        return view('subadmin.documents',compact('data'));
    }
    public function savedocument(Request $request){
    	$request->validate([
    		'name'=>'required',
    		'email'=>'required|email',
    		'document'=>'required',
        ]);
       $document = new Document;
       $document->name = $request->name;
       $document->email = $request->email;
       $file = $request->document;
       $filename = time().'.'.$file->getClientOriginalExtension();
       $request->document->move('assets',$filename);
       $document->document = $filename;
       $document->vendor_id = Auth::id();
       $document->save();
       //dd($document);
       return redirect()->route('admin.document')->with('success','Documents Saved.');

    }
   public function downloaddocument($file){
    $file_path = public_path('assets/'.$file);
    return response()->download( $file_path);
    return redirect()->route('admin.document');
}
}
