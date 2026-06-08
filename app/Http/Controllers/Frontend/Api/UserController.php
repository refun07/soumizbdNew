<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserController extends Controller
{
    # get user addresses
    public function addresses($message = '')
    {
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => $message,
            'result'    => [
                'addresses' => AddressResource::collection(apiUser()->addresses()->latest()->get()),
                'countries' => Country::where('is_active', 1)->get()
            ]
        ];
    }

    # get states
    public function states($countryId)
    {
        $states = State::where('is_active', 1)->where('country_id', $countryId)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $states
        ];
    }

    # get cities
    public function cities($stateId)
    {
        $cities = City::where('is_active', 1);
        if ($stateId && $stateId != 0) {
            $cities = $cities->where('state_id', $stateId);
        }
        $cities = $cities->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $cities
        ];
    }

    // # get areas
    // public function areas($cityId)
    // {
    //     $areas = Area::where('is_active', 1)->where('city_id', $cityId)->get();
    //     return [
    //         'success'   => true,
    //         'status'    => 200,
    //         'message'   => '',
    //         'result'    => $areas
    //     ];
    // }

    # add new address
    public function addNewAddress(Request $request)
    {
        if (!$request->countryId) {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Please select a country'),
                    'result'    => null
                ],
                403
            );
        }

        if (!$request->stateId) {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Please select an state'),
                    'result'    => null
                ],
                403
            );
        }

        if (!$request->cityId) {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Please select a city'),
                    'result'    => null
                ],
                403
            );
        }

        // if (!$request->areaId) {
        //     return response()->json(
        //         [
        //             'success'   => false,
        //             'status'    => 403,
        //             'message'   => translate('Please select an area'),
        //             'result'    => null
        //         ],
        //         403
        //     );
        // }

        $address = new UserAddress;
        $address->user_id       = userId();
        $address->country_id    = $request->countryId;
        $address->state_id      = $request->stateId;
        $address->city_id       = $request->cityId;
        $address->area_id       = $request->areaId;
        $address->postal_code   = $request->postalCode;
        $address->address       = $request->address;
        $address->type          = $request->type;
        $address->direction     = $request->direction;
        $address->is_default    = 0;
        $address->save();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Address has been added successfully'),
            'result'    => new AddressResource($address)
        ];
    }

    # update address
    public function updateAddress(Request $request)
    {
        if (!$request->countryId) {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Please select a country'),
                    'result'    => null
                ],
                403
            );
        }

        if (!$request->stateId) {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Please select an state'),
                    'result'    => null
                ],
                403
            );
        }

        if (!$request->cityId) {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Please select a city'),
                    'result'    => null
                ],
                403
            );
        }

        // if (!$request->areaId) {
        //     return response()->json(
        //         [
        //             'success'   => false,
        //             'status'    => 403,
        //             'message'   => translate('Please select an area'),
        //             'result'    => null
        //         ],
        //         403
        //     );
        // }

        $address                = UserAddress::find((int) $request->id);
        $address->user_id       = userId();
        $address->country_id    = $request->countryId;
        $address->state_id      = $request->stateId;
        $address->city_id       = $request->cityId;
        $address->area_id       = $request->areaId;
        $address->postal_code   = $request->postalCode;
        $address->address       = $request->address;
        $address->type          = $request->type;
        $address->direction     = $request->direction;
        $address->save();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Address has been updated successfully'),
            'result'    => new AddressResource($address)
        ];
    }

    # default address
    public function setDefaultAddress(Request $request)
    {
        $address        = UserAddress::find((int) $request->id);

        $prevDefault    = UserAddress::where('user_id', userId())->where('is_default', 1)->first();
        if ($prevDefault) {
            $prevDefault->is_default = 0;
            $prevDefault->save();
        }

        $address->is_default = 1;
        $address->save();
        return $this->addresses(translate('Default address has been updated'));
    }

    # destroy address
    public function destroy(Request $request)
    {
        $address = UserAddress::find((int) $request->id);
        if ($address) {
            $address->delete();
            return $this->addresses(translate('Address has been deleted successfully'));
        } else {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 404,
                    'message'   => translate('Address not found'),
                    'result'    => null
                ],
                404
            );
        }
    }
}
