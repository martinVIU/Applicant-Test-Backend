<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;
use App\Models\Access;

class DeviceController extends Controller
{
    /**
     * Retrieve all devices assigned to the authenticated user. 
     */
    public function getDevicesAccesed(Request $request)
    {
        $user = $request->user();

        // Get devices with specific columns as an array
        //$devices = $user->devices()->get(['name', 'model', 'device_unique_id']);
    
        // Avoid showing the "pivot" field
        $devices = $user->devices->map(function ($device) {
            return [
                'name' => $device->name,
                'model' => $device->model,
                'device_unique_id' => $device->device_unique_id,
            ];
        });


        return response()->json([
            'devices' => $devices,
        ], 200);
    }

    /**
     * Retrieve all devices assigned to the authenticated user with all their details. 
     */
    public function getDevicesAccesedDetailed(Request $request)
    {
        $user = $request->user();

        // Get devices linked to the authenticated user
        $devices = $user->devices; 

        return response()->json([
            'devices' => $devices,
        ], 200);
    }

    /**
     * Assign a device to a user.
     */
    public function assignDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $device = Device::findOrFail($request->device_id);
        $user = User::findOrFail($request->user_id);

        // Check if the device is already assigned
        if ($user->devices()->where('device_id', $device->id)->exists()) {
            return response()->json([
                'message' => 'Device is already assigned to this user.',
            ], 400);
        }

        // Assign the device
        $user->devices()->attach($device->id);

        return response()->json([
            'message' => 'Device assigned successfully.',
        ], 201);
    }

    /**
     * Get a list of devices the authenticated user has access to.
     */
    public function getAccessibleDevices(Request $request)
    {
        // Get the currently authenticated user
        $user = $request->user();

        // Fetch devices the user has access to
        $devices = $user->devices;

        return response()->json([
            'message' => 'Devices retrieved successfully.',
            'devices' => $devices,
        ], 200);
    }
    public function getDeviceInfo($id)
    {
        // Find the device by its id
        $device = Device::find($id);

        // Check if the device exists
        if (!$device) {
            return response()->json([
                'message' => 'Device not found',
            ], 404);
        }

        // Return the device data
        return response()->json([
            'message' => 'Device retrieved successfully',
            'device' => $device,
        ], 200);
    }
}

