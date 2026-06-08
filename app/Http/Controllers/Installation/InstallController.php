<?php

namespace App\Http\Controllers\Installation;

use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Shop;
use App\Models\State;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use DB;
use URL;
use Hash;
use mysqli;

class InstallController extends Controller
{
    # init installation
    public function index()
    {
        overWriteEnvFile('APP_URL', URL::to('/'));
        sleep(2);
        return view('installation.index');
    }

    # checklist
    public function checklist()
    {
        $permissions['curlEnabled']                 = function_exists('curl_version');
        $permissions['envFileWritePermission']      = is_writable(base_path('.env'));
        $permissions['routesFileWritePermission']   = is_writable(base_path('app/Providers/RouteServiceProvider.php'));

        return response()->json([
            'success' => true,
            'status'    => 200,
            'message' => '',
            'result' => $permissions,
        ], 200);
    }

    # db store
    public function storeDatabaseSetup(Request $request)
    {
        try {
            if ($this->checkDatabaseConnection($request->DB_HOST, $request->DB_DATABASE, $request->DB_USERNAME, $request->DB_PASSWORD)) {
                $path = base_path('.env');
                if (file_exists($path)) {
                    foreach ($request->types as $type) {
                        overWriteEnvFile($type, $request[$type]);
                    }

                    return response()->json([
                        'success' => false,
                        'status'    => 200,
                        'message' => 'Database connected successfully',
                        'result' => null,
                    ], 200);
                } else {
                    // fallback 
                    return response()->json([
                        'success' => false,
                        'status'    => 500,
                        'message' => 'Database connection error',
                        'result' => null,
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'status'    => 500,
                    'message' => 'Database connection error',
                    'result' => null,
                ], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status'    => 500,
                'message' => 'Database connection error',
                'result' => null,
            ], 500);
        }
    }

    # check db connection
    function checkDatabaseConnection($db_host = "", $db_name = "", $db_user = "", $db_pass = "")
    {
        if (@mysqli_connect($db_host, $db_user, $db_pass, $db_name)) {
            return true;
        } else {
            return false;
        }
    }

    # run db migration
    public function runDbMigration(Request $request)
    {
        if ($this->checkDatabaseConnection(env('DB_HOST'), env('DB_DATABASE'), env('DB_USERNAME'), env('DB_PASSWORD'))) {

            try {
                $db = new mysqli(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
                $db->query('SET @@global.max_allowed_packet = ' . (500 * 1024 * 1024));
            } catch (\Throwable $th) {
                //throw $th;
            }

            if ($request->demo == true) {
                $this->runDemoDbMigration();
            } else {
                # run migrations  here
                Artisan::call('migrate:refresh --seed');

                ini_set('memory_limit', '-1');
                $sql_path = base_path('/sql/cities.sql');
                DB::unprepared(file_get_contents($sql_path));

                $sql_path = base_path('/sql/states.sql');
                DB::unprepared(file_get_contents($sql_path));
            }

            cacheClear();

            $countries = Country::orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'status'    => 200,
                'message' => '',
                'result' => [
                    'countries' => $countries
                ],
            ], 200);
        } else {
            // db connection error
            return response()->json([
                'success' => false,
                'status'    => 500,
                'message' => 'Database connection error',
                'result' => null,
            ], 500);
        }
    }

    # run Demo db migration
    public function runDemoDbMigration($name = 'demo')
    {
        // TODO:: [update version] demo seeders 
        ini_set('memory_limit', '-1');
        // overWriteEnvFile('DEMO_MODE', 'On');
        $sql_path = base_path('/sql' . '/' . $name . '.sql');
        DB::unprepared(file_get_contents($sql_path));
    }

    # admin configuration
    public function storeAdmin(Request $request)
    {
        $user = User::where('user_type', 'admin')->first();
        $user->name      = $request->name;
        $user->email     = $request->email;
        $user->password  = Hash::make($request->password);
        $user->email_verified_at = date('Y-m-d H:m:s');
        $user->save();

        $country = Country::findOrFail((int)$request->countryId);
        $country->is_active = !$country->is_active;
        if ($country->save()) {
            $stateQuery = State::where('country_id', $country->id);
            $stateQuery->update([
                'is_active' => $country->is_active
            ]);
            $stateIds   = $stateQuery->where('is_active', 1)->pluck('id');

            $cityQuery  = City::whereIn('state_id', $stateIds);
            $cityQuery->update([
                'is_active' => $country->is_active
            ]);
            $cityIds   = $cityQuery->where('is_active', 1)->whereIn('state_id', $stateIds)->pluck('id');

            $areaQuery  = Area::whereIn('city_id', $cityIds);
            $areaQuery->update([
                'is_active' => $country->is_active
            ]);
        }

        overWriteEnvFile('APP_MODE', $request->appMode);
        // currency code 
        $businessSettings = SystemSetting::where('type', 'currencyCode')->first();
        if (is_null($businessSettings)) {
            $businessSettings = new SystemSetting;
            $businessSettings->type = 'currencyCode';
        }
        $businessSettings->value = $request->currencyCode;
        $businessSettings->save();
        // currency symbol 
        $businessSettings = SystemSetting::where('type', 'currencySymbol')->first();
        if (is_null($businessSettings)) {
            $businessSettings = new SystemSetting;
            $businessSettings->type = 'currencySymbol';
        }
        $businessSettings->value = $request->currencySymbol;
        $businessSettings->save();

        // shop 
        $shop       = Shop::find(1);
        $shop->name = $request->shopName;
        $shop->manage_stock_by = $request->useInventory == 1 ? 'inventory' : 'default';
        $shop->save();

        // warehouse 
        $warehouse = new Warehouse;
        $warehouse->name                 = 'Default Warehouse';
        $warehouse->shop_id              = $shop->id;
        $warehouse->is_default           = 1;
        $warehouse->save();

        $oldRouteServiceProvider        = base_path('app/Providers/RouteServiceProvider.php');
        $setupRouteServiceProvider      = base_path('app/Providers/SetupServiceComplete.php');
        copy($setupRouteServiceProvider, $oldRouteServiceProvider);

        (new LanguageController)->insertTranslationKeysToDatabase('en-US');

        return response()->json([
            'success' => true,
            'status'    => 200,
            'message' => '',
            'result' => null,
        ], 200);
    }
}
