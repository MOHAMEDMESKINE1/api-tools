<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use Exception;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $contacts = Contact::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->get();
        } else {
            $contacts = Contact::all();
        }

        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Handle file upload and get the file path
            $profilePath = $this->handleProfileUpload($request);

            // Add profile path to validated data
            $validatedData['profile'] = $profilePath;
            $validatedData['user_id'] = auth()->id();

            // Create a new contact record using Eloquent
            $contact = Contact::create($validatedData);

            return new ContactResource($contact);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
    /**}
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return ContactResource::collection($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactRequest $request, Contact $contact)
    {
        try {
            $validatedData = $request->validated();

            // Handle file upload and get the new file path
            $newProfilePath = $this->handleProfileUpload($request, $contact->profile);

            // Add new profile path to validated data if a new file was uploaded
            if ($newProfilePath) {
                $validatedData['profile'] = $newProfilePath;
            }

            // Update the contact record
            $contact->update($validatedData);

            return new ContactResource($contact);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        try {
            // Delete old profile image if exists
            if ($contact->profile) {
                $profilePath = str_replace('/storage/', '', $contact->profile);
                Storage::disk('public')->delete($profilePath);
            }

            // Delete the contact record
            $contact->delete();

            return response()->noContent();
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    private function handleProfileUpload(Request $request, $oldProfilePath = null)
    {
        if ($request->hasFile('profile')) {
            // Delete the old profile if it exists
            if ($oldProfilePath) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldProfilePath));
            }

            $file = $request->file('profile');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/profiles', $fileName, 'public');

            return '/storage/' . $filePath;
        }

        return null; // Set default profile path if no file is uploaded
    }
}
