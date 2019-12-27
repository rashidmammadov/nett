<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class DataController extends ApiController {

    /**
     * DataController constructor.
     */
    public function __construct() {}

    /**
     * Returns data.
     * @param Request $request
     * @return json: User info
     */
    public function get(Request $request) {
        $data = json_decode("{}");
        if ($request['regions'] == 'true') {
            $regions = json_decode($this->fetchRegions());
            $data->regions = $regions;
        }
        if ($request['taxOffices'] == 'true') {
            $taxOffices = json_decode($this->fetchTaxOffices());
            $data->taxOffices = $taxOffices;
        }
        return $this->respondCreated('', $data);
    }

    /**
     * Open and return lectures file.
     * @return mixed
     */
    private function fetchTaxOffices() {
        $path = storage_path() . '/taxOfficesTurkey.json';
        return File::get($path);
    }

    /**
     * Open and return regions file.
     * @return mixed
     */
    private function fetchRegions() {
        $path = storage_path() . '/citiesTurkey.json';
        return File::get($path);
    }

}
