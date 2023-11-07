<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);

    }

    public function get(int $id): ContactResource
    {
        $user = auth()->user();

        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first(); // validasi bahwa kontaknya sesuai denga pemiliknya
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'User Contact Not Found'
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = auth()->user();

        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first(); // validasi bahwa kontaknya sesuai denga pemiliknya
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'User Contact Not Found'
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = auth()->user();

        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first(); // validasi bahwa kontaknya sesuai denga pemiliknya
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'User Contact Not Found'
                ]
            ])->setStatusCode(404));
        }
        $contact->delete();
        return response()->json([
            'message' => [
                'Contact successfully deleted'
            ]
        ])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection
    {
         $user = auth()->user();
         $page = $request->input('page', 1);
         $size = $request->input('size', 10);

        //  $contacts = Contact::where('user_id', $user->id)->first();  
            $contacts = DB::table('contacts')
            ->where('user_id', $user->id)
            ->first();
         if(!$contacts){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'User Contact Not Found'
                ]
            ])->setStatusCode(404));
        }
$contacts = $contacts = Contact::where('user_id', $user->id)->where(function(Builder $builder) use ($request) {
        //  $contacts = $contacts->where(function(Builder $builder) use ($request) {
            $id = $request->input('id');
            if($id) {
                $builder->where('id', 'like' , '%' . $id . '%');
            }
            $name = $request->input('name');
            if($name) {
                $builder->orWhere(function(Builder $builder) use ($name) {
                    $builder->orWhere('first_name','like','%'. $name .'%');
                    $builder->orWhere('last_name','like','%'. $name .'%'); 
                });
            }
            $email = $request->input('email');
            if($email) {
                $builder->where('email', 'like', '%' . $email . '%');    
            }

            $phone = $request->input('phone');
            if($phone) {
                $builder->where('phone', 'like' , '%' . $phone . '%');
            }

         });

         if(!$contacts->exists()) {
            Log::info("Data tidak ditemukan");
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'Contact Not Found'
                ]
            ])->setStatusCode(404));
         }else{
            $contacts = $contacts->paginate(perPage: $size, page: $page);
            return new ContactCollection($contacts);
         }
            // Log::info(json_encode($contacts, JSON_PRETTY_PRINT));
    }
}
