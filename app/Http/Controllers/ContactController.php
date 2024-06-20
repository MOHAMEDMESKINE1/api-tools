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
            // Handle file upload if 'profile' file exists in the request
            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/profiles', $fileName, 'public');
                $profilePath = '/storage/' . $filePath;
            } else {
                $profilePath = null; // Set default profile path if no file is uploaded
            }

            // Create a new contact record using Eloquent

            $contact = Contact::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'profile' => $profilePath,
                'user_id' => auth()->id(),
            ]);

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
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'profile' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'user_id' => 'nullable',
            ]);

            $data = $request->only(['name', 'email', 'phone', 'user_id']);

            // Handle file upload if 'profile' file exists in the request
            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/profiles', $fileName, 'public');
                $profilePath = '/storage/' . $filePath;
                $data['profile'] = $profilePath;
            }

            // Update the contact record
            $contact->update($data);

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
}
