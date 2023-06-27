<?php

namespace App\Http\Controllers;

use auth;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    public function index(Request $req)
    {
       // dd(Listing::latest()->filter(request(['tag']))->get());
        return view('listings.index', [
            "cord"=>Listing::latest()->filter(request(['tag','search']))->paginate(4)
        ]);
    }
    public function show($id)
    {
    $listing=Listing::find($id);
    if($listing)
    {return view("listings.show",[
        "listing"=>Listing::find($id)
    ]);
    }
    else{
        abort('404');
    }
    }
    public function create()
    {
        return view("listings.create");
    }
    public function store(Request $req)
    {
        $formFields=$req->validate([
            'title'=>'required',
            'company'=>["required",Rule::unique('listings','company')],
            'location'=>'required',
            "website"=>'required',
            "email"=>['required','email'],
            "tags"=>'required',
            "description"=>'required'
        ]);
        if($req->hasFile('logo')==true)
        {
            $formFields['logo']=$req->file('logo')->store('logos','public');
        }
        $formFields['user_id']=auth()->id();
        //dd(  $formFields);
        Listing::create($formFields);
        return redirect('/')->with('message',"gig added successfully");
    }
    public function edit(Listing $listing)
    {
        return view('listings.edit',['listing'=>$listing]);
    }
    public function update(Request $req,Listing $listing)
    {
        if(auth()->id()!=$listing->user_id)
            abort(403,'Unauthorized user');
        $formFields=$req->validate([
            'title'=>'required',
            'company'=>["required"],
            'location'=>'required',
            "website"=>'required',
            "email"=>['required','email'],
            "tags"=>'required',
            "description"=>'required'
        ]);
        $formFields['logo']=$listing->logo;
        if($req->hasFile('logo')==true)
        {
            $formFields['logo']=$req->file('logo')->store('logos','public');
        }
        //dd(  $formFields);
        $listing->create($formFields);
        return redirect('listing/'.$listing->id)->with('message',"gig updated successfully");
    }
    public function destroy(Listing $listing)
    {
        if(auth()->id()!=$listing->user_id)
            abort(403,'Unauthorized user');
        $listing->delete();
        return redirect('/')->with('message','gig deleted successfully');
    }
    public function manage()
    {
        return view('listings.manage',[
            'listings'=>auth()->user()->listings()->get()
        ]);
    }
}
