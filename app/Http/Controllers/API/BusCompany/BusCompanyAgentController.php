<?php

namespace App\Http\Controllers\API\BusCompany;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\CompanyAdminRegistrationRequest;
use App\Http\Resources\BusCompany\AdminRegistrationResource;
use App\Mail\Registration\SendAgentPasswordMail;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BusCompanyAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $companyAgents = User::role('bus-company-agent')->where('bus_company_id', Auth::user()->bus_company_id)->paginate();
            return response()->json(['data' => $companyAgents]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "Something Went wrong in BusCompanyAgentController.index"
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(string $id)
    {
        $companyAgent = User::find($id);
        if ($companyAgent) {
            return response()->json(['data' => $companyAgent], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyAdminRegistrationRequest $request)
    {
        try {
            $agentPassword = Str::random(6);

            $busAgent = User::create([
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'name' => $request->input('name'),
                'password' => Hash::make($agentPassword),
                'bus_company_id' => Auth::user()->bus_company_id,
                'status' => 1
            ]);

            //Assign the bus company agent role here
            $busAgent->assignRole(['bus-company-agent', 'mobile-user', 'profile-management']);

            //the generated password should be sent to the user via email here
            Mail::to($request->input('email'))->send(new SendAgentPasswordMail($agentPassword));


            if (true) {
                return response()->json(['message' => "success"], 201);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BusCompanyAgentController.store'
            ], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $companyAgent = User::find($id);

        if ($companyAgent) {
            $companyAgent->delete();
            return response()->json(['message' => 'Company Agent deleted successully'], 200);
        }
    }
}