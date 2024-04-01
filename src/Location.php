<?php

namespace Dantepiazza\GeoLocationData;

class Location {
    protected $_locations = [];
    protected $_cache = __DIR__.'/data/cache.json';

    public function __construct(){
        if(!file_exists($this -> _cache)){
            $this -> cache();
        }

        $this -> _locations = $this -> load_json_data();
    }

    public function cache(){
        $countries = $this -> load_json_data('countries');
        $states = $this -> load_json_data('states');
        $counties = [];
        $cities = [];

        foreach($countries as $country){
            $counties = array_merge($counties, $this -> load_json_data($country -> code.'/counties'));
            $cities = array_merge($cities, $this -> load_json_data($country -> code.'/cities'));
        }

        $this -> _locations = [];
        
        foreach($cities as $city){
            $country = $this -> _get($countries, 'id', $city -> country_id);
            $state = $this -> _get($states, 'id', $city -> state_id);
            $county = $this -> _get($counties, 'id', $city -> county_id);
            
            $this -> _locations[$city -> id] =  (object) [
                'country_id' => $country -> id,
                'country_name' => $country -> name,
                'country_code' => $country -> code,
    
                'state_id' => $state -> id,
                'state_name' => $state -> name,
    
                'county_id' => $county -> id,
                'county_name' => $county -> name,
    
                'city_id' => $city -> id,
                'city_name' => $city -> name,
                'zip_code' => $city -> zip_code,
                'name' => $city -> name.' ('.$city -> zip_code.'), '.$state -> name.', '.$country -> name,
            ];
        }

        $fileData = json_encode($this -> _locations, JSON_PRETTY_PRINT);
        
        $save = fopen($this -> _cache, 'w');        
        fwrite($save, $fileData);
        fclose($save);
    }

    private function load_json_data($source = null) {
        $file = !is_null($source) ? __DIR__.'/data/'.strtolower($source).'.json' : $this -> _cache;
        
        if(file_exists($file)){
            return json_decode(file_get_contents($file));
        }

        return [];
    }

    private function _get($items, $key, $value){
        return array_reduce($items, static function($carry, $item) use ($key, $value) {
            return $carry === false && $item -> {$key} === $value ? $item : $carry;
        }, false);
    }

    private function _search($items, $key, $value, $strict = true){
         return array_filter($items, static function($item) use ($key, $value) {
            return str_contains(strtolower($item -> {$key}), strtolower($value));
        });
    }
    
    public function all(){
        return $this -> _locations;
    }

    public function search(string $query){
        return $this -> _search((array) $this -> _locations, 'name', $query, false);
    }

    public function get(int $city){
        return $this -> _get((array) $this -> _locations, 'city_id', $city);
    }

    public function countries(){
        return $this -> load_json_data('countries');
    }
    
    public function country(string $key, $value){
        return $this -> _get((array) $this -> countries(), $key, $value);
    }

    public function states(){
        return $this -> load_json_data('states');
    }
    
    public function state(int $id){
        return $this -> _get((array) $this -> states(), 'id', $id);
    }

    public function counties(string $code){
        return $this -> load_json_data($code.'/counties');
    }
    
    public function county(string $code, int $id){
        return $this -> _get($this -> counties($code), 'id', $id);
    }

    public function cities(string $code){
        return $this -> load_json_data($code.'/cities');
    }
    
    public function city(string $code, int $id){
        return $this -> _get((array) $this -> cities($code), 'id', $id);
    }
}