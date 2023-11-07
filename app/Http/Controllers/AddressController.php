<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ContactResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressController extends Controller
{
    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = auth()->user();
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'User Contact Not Found'
                ]
            ])->setStatusCode(404));
        }
        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = auth()->user();
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'Contact Not Found'
                ]
            ])->setStatusCode(404));
        }

        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if(!$address){
            throw new HttpResponseException(response()->json([
                'error_message'=> [
                    'Address Not Found'
                    ]
            ])->setStatusCode(404));
        }
        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = auth()->user();
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'Contact Not Found'
                ]
            ])->setStatusCode(404));
        }

        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if(!$address){
            throw new HttpResponseException(response()->json([
                'error_message'=> [
                    'Address Not Found'
                    ]
            ])->setStatusCode(404));
        }
        
        $data = $request->validated();
        $address->fill($data);
        $address->contact_id = $contact->id;
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $user = auth()->user();
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'Contact Not Found'
                ]
            ])->setStatusCode(404));
        }

        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if(!$address){
            throw new HttpResponseException(response()->json([
                'error_message'=> [
                    'Address Not Found'
                    ]
            ])->setStatusCode(404));
        }

        $address->delete();
        return response()->json([
            'message' => [
                'Address Successfully Deleted'
            ]
        ])->setStatusCode(200);
    }

    public function list(int $idContact): JsonResponse
    {
        $user = auth()->user();
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'error_message' => [
                    'Contact Not Found'
                ]
            ])->setStatusCode(404));
        }

        $address = Address::where('contact_id', $contact->id)->get();
        if(!$address){
            throw new HttpResponseException(response()->json([
                'error_message'=> [
                    'Address Not Found'
                    ]
            ])->setStatusCode(404));
        }

        return AddressResource::collection($address)->response()->setStatusCode(200);

    }
}
