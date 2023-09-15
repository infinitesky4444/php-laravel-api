<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use League\Csv\Statement;
use Carbon\Carbon;
use Nette\Utils\DateTime;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // $request->validate([
        //     'file' => 'required|file|mimes:csv,txt',
        // ]);

        // // Process the CSV file
        // if ($request->hasFile('file')) {
        //     $file = $request->file('file');

        //     // Get the path to store the file
        //     $path = storage_path('app/public/uploads');

        //     // Generate a unique filename for the uploaded file
        //     $filename = uniqid('import_') . '.' . $file->getClientOriginalExtension();

        //     // Move the uploaded file to the storage location
        //     $file->move($path, $filename);

        //     // Read and process the CSV file
        //     $filePath = $path . '/' . $filename;
        //     $this->processCSV($filePath);

        //     // Return a success response
        //     return response()->json(['message' => 'Employee batch import successful']);
        // }

        // // Return an error response if no file was uploaded
        // return response()->json(['error' => 'No file provided'], 400);

        $filePath = storage_path('app/public/uploads/import.csv');
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $stmt = Statement::create();
        $records = $stmt->process($csv);

        foreach ($records as $data) {
            try {
                $dateOfBirth = Carbon::createFromFormat('m/d/Y', $data["Date of Birth"]);
        
                if (!$dateOfBirth) {
                    throw new \Exception('Invalid date format');
                }
        
            } catch (\Exception $e) {
                continue;
            }

            try {
                $timeOfBirth = Carbon::createFromFormat('h:i:s A', $data["Time of Birth"]);
        
                if (!$timeOfBirth) {
                    throw new \Exception('Invalid date format');
                }
        
            } catch (\Exception $e) {
                continue;
            }

            try {
                $dateOfJoin = Carbon::createFromFormat('m/d/Y', $data["Date of Joining"]);
        
                if (!$dateOfJoin) {
                    throw new \Exception('Invalid date format');
                }
        
            } catch (\Exception $e) {
                continue;
            }

            $validator = Validator::make([
                'employee_id' => $data["Emp ID"],
                'user_name'  => $data["User Name"],
                'name_prefix' =>  $data["Name Prefix"],
                'first_name' =>  $data["First Name"],
                'middle_initial' =>  $data["Middle Initial"],
                'last_name' =>  $data["Last Name"],
                'gender' =>  $data["Gender"],
                'email' =>  $data["E Mail"],
                'date_of_birth' => Carbon::createFromFormat('m/d/Y', $data["Date of Birth"]) ? Carbon::createFromFormat('m/d/Y', $data["Date of Birth"])->format('Y-m-d') : null,
                'time_of_birth' =>   Carbon::createFromFormat('h:i:s A', $data["Time of Birth"])->format('H:i:s'),
                'age_in_years' =>  $data["Age in Yrs."],
                'date_of_joining' =>  Carbon::createFromFormat('m/d/Y', $data["Date of Joining"])->format('Y-m-d'),
                'age_in_company' =>  $data["Age in Company (Years)"],
                'phone_no' =>  $data["Phone No. "],
                'place_name' =>  $data["Place Name"],
                'county' =>  $data["County"],
                'city' =>  $data["City"],
                'zip' =>  $data["Zip"],
                'region' =>  $data["Region"],
            ], [
                'employee_id' => 'required|unique:employees',
                'user_name' => 'required',
                'name_prefix' => 'required',
                'first_name' => 'required',
                'middle_initial' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'email' => 'required|email',
                'date_of_birth' => 'required|date',
                'time_of_birth' => 'required|date_format:H:i:s',
                'age_in_years' => 'required|numeric',
                'date_of_joining' => 'required|date',
                'age_in_company' => 'required|numeric',
                'phone_no' => 'required',
                'place_name' => 'required',
                'county' => 'required',
                'city' => 'required',
                'zip' => 'required',
                'region' => 'required',
            ]);

            if ($validator->fails()) {
                continue;
            }

            // // Create a new Employee model and save it to the database
            Employee::create([
                'employee_id' => $data["Emp ID"],
                'user_name'  => $data["User Name"],
                'name_prefix' =>  $data["Name Prefix"],
                'first_name' =>  $data["First Name"],
                'middle_initial' =>  $data["Middle Initial"],
                'last_name' =>  $data["Last Name"],
                'gender' =>  $data["Gender"],
                'email' =>  $data["E Mail"],
                'date_of_birth' =>  Carbon::createFromFormat('m/d/Y', $data["Date of Birth"])->format('Y-m-d'),
                'time_of_birth' =>  Carbon::createFromFormat('h:i:s A', $data["Time of Birth"])->format('H:i:s'),
                'age_in_years' =>  $data["Age in Yrs."],
                'date_of_joining' =>  Carbon::createFromFormat('m/d/Y', $data["Date of Joining"])->format('Y-m-d'),
                'age_in_company' =>  $data["Age in Company (Years)"],
                'phone_no' =>  $data["Phone No. "],
                'place_name' =>  $data["Place Name"],
                'county' =>  $data["County"],
                'city' =>  $data["City"],
                'zip' =>  $data["Zip"],
                'region' =>  $data["Region"],
            ]);
        }


        $employees = Employee::all();
        return response()->json($employees);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::where('employee_id', $id)->first();
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::where('employee_id', $id)->first();
        $employee->delete();
        return response()->json("Successfully deleted");
    }
}
