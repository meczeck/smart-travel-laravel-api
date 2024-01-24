<?php

namespace App\Http\Controllers\API\BusCompany;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusCompany\CreateBusCompanyRequest;
use App\Http\Requests\BusCompany\UpdateBusCompanyRequest;
use App\Models\BusCompany;
use App\Models\Image;
use App\Models\User;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class BusCompanyController extends Controller
{
    use FileTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $busCompanies = BusCompany::with(['companyAdmin'])->orderByDesc('created_at')->paginate();
            return response()->json(['data' => $busCompanies], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in BusCompanyController.index'
            ]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBusCompanyRequest $request)
    {
        try {

            if (BusCompany::find(Auth::user()->bus_company_id)) {
                return response()->json(['message' => 'Company Informations already registered. You can edit'], 400);
            }
            //Decoding the files from request
            $logo = base64_decode($request->input('logo'));
            $business_licence = base64_decode($request->input('business_licence'));

            $logoPath = $this->storeBase64Image($logo, 'company-logos');
            $licencePath = $this->storeBase64Image($business_licence, 'business-licences');

            //Save bus company information
            $busCompany = $this->saveBusCompanyData($request, new BusCompany());

            Image::create([
                'source_id' => $busCompany->id,
                'url' => $logoPath
            ]);

            Image::create([
                'source_id' => $busCompany->id,
                'url' => $licencePath
            ]);

            $companyAdmin = User::findOrFail(Auth::user()->id);
            $companyAdmin->bus_company_id = $busCompany->id;
            $companyAdmin->save();

            return response()->json(['message' => 'Bus Company Information added successfully'], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in BusCompanyController.store'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $busCompany = BusCompany::with('companyAdmin')->find($id);

            if ($busCompany) {
                return response()->json(['data' => $busCompany], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in BusCompanyController.show'
            ]);
        }
    }
    public function getSingleCompany(string $id)
    {
        try {
            $companyAdmin = User::find($id);
            $busCompany = BusCompany::with('companyAdmin')->find($companyAdmin->bus_company_id);

            if ($busCompany) {
                return response()->json(['data' => $busCompany], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'something went wrong in BusCompanyController.show'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusCompanyRequest $request, string $id)
    {
        try {
            $busCompany = BusCompany::findOrFail($id);

            //Delete old files from storage  if exists
            $this->deleteFileFromStorage($busCompany->logo);
            $this->deleteFileFromStorage($busCompany->business_licence);

            //Save Bus Company informations
            $this->saveBusCompanyData($request, $busCompany);

            return response()->json(['message' => 'Company Information updated successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'BusCompany not found'], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong in BusCompanyController.update'
            ], 500);
        }
    }

    private function saveBusCompanyData($request, $busCompany)
    {
        //Encoding the files from request and storing into storage
        $logo = base64_decode($request->input('logo'));
        $business_licence = base64_decode($request->input('business_licence'));

        $logoPath = $this->storeBase64Image($logo, 'company-logos');
        $licencePath = $this->storeBase64Image($business_licence, 'business-licences');

        $busCompany->email = $request->input('email');
        $busCompany->phone_one = $request->input('phone_one');
        $busCompany->phone_two = $request->input('phone_two');
        $busCompany->name = $request->input('name');
        $busCompany->logo = $logoPath;
        $busCompany->description = $request->input('description');
        $busCompany->policy = $request->input('policy');
        $busCompany->business_licence = $licencePath;
        $busCompany->save();

        return $busCompany;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $company = BusCompany::findOrFail($id);
            $company->delete();

            return response()->json(['message' => 'Bus Company Deleted Successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'error ' => $e->getMessage(),
                'message' => 'Something went wrong in BusCompanyController.destroy'
            ]);
        }
    }

    public function verifyCompanyReg(string $id)
    {
        try {
            $companyAdmin = User::where('bus_company_id', $id)->first();

            $companyAdmin->registration_verification = true;
            $companyAdmin->save();

            $user = User::where('bus_company_id', $id)->first();
            $user->assignRole(['company-agents-management', 'company-admin']);

            return response()->json(['message' => 'Bus Company Verified Successfully'], 202);
        } catch (Exception $e) {
            return response()->json([
                'error ' => $e->getMessage(),
                'message' => 'Something went wrong in BusCompanyController.validateCompanyReg'
            ]);
        }
    }
    public function disproveCompanyReg(string $id)
    {
        try {
            $companyAdmin = User::where('bus_company_id', $id)->first();

            $companyAdmin->registration_verification = false;
            $companyAdmin->save();

            $companyAdmin->revokeRole(['company-admin']);

            return response()->json(['message' => 'Bus Company invalidated Successfully'], 202);
        } catch (Exception $e) {
            return response()->json([
                'error ' => $e->getMessage(),
                'message' => 'Something went wrong in BusCompanyController.validateCompanyReg'
            ]);
        }
    }
}