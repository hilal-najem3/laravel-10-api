<?php

namespace App\Containers\Common\Helpers;

use App\Containers\Common\Models\Region;
use App\Containers\Common\Models\RegionType;


use Exception;

class RegionsHelper
{
    /**
     * get all countries and regions
     * 
     * @return Region[] $regions
     */
    public static function allCountries()
    {
        try {
            $regions = Region::where('type_id', 1)
            ->orderBy('name', 'asc')->get()
            ->each(function(Region $region) {
                $region = $region->load(['states'])->orderBy('name', 'asc');
            });
    
            return $regions;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get all country types
     * 
     * @return RegionType[] $types
     */
    public static function types()
    {
        try {
            $types = RegionType::all();
            return $types;
        } catch (Exception $e) {
            throw $e;
        }
    }
}