<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Company');
    }

    public function paginate(Request $request) {
        $companies = Company::orderBy(
            $request->input('sort_field', 'updated_at'),
            $request->input('sort_order', 'desc')
        );

        if ($request->filled('search')) {
            $companies = $companies->where('name', 'ilike', "%{$request->input('search')}%");
        }
        $companies = $companies->paginate($request->per_page)
            ->withQueryString();
        return response()->json($companies);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("CompanyCreate");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $company = new Company();
        $company->name = $request->name;
        if ($request->filled('email')) {
            $company->email = $request->email;
        }
        if ($request->filled('website')) {
            $company->website = $request->website;
        }
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads', 'public');
            $company->logo = url("/storage").'/'.$path;
        }

        $company->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $company_id)
    {
        $company = Company::find($company_id);
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(String $company_id, UpdateCompanyRequest $request)
    {
        $company = Company::find($company_id);
        $company->name = $request->name;
        if ($request->filled('email')) {
            $company->email = $request->email;
        }
        if ($request->filled('website')) {
            $company->website = $request->website;
        }
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads', 'public');
            $company->logo = url("/storage").'/'.$path;
        }
        $company->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $company_id)
    {
        Company::destroy($company_id);
    }
}
