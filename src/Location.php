<?php

namespace Dantepiazza\GeoLocationData;

class Location {
    protected $_countries = [];
    protected $_states = [];
    protected $_counties = [];
    protected $_cities = [];
    protected $_locations = [];

    public function __construct(){
        $this -> _countries = $this -> load_json_data('countries');
        $this -> _states = $this -> load_json_data('states');

        foreach($this -> _countries as $country){
            $this -> _counties = array_merge($this -> _counties, $this -> load_json_data($country -> code.'/counties'));
            $this -> _cities = array_merge($this -> _cities, $this -> load_json_data($country -> code.'/cities'));
        }

        foreach($this -> _cities as $city){
            $country = $this -> country('id', $city -> country_id);
            $state = $this -> state($city -> state_id);
            $county = $this -> county($city -> county_id);
            
            $this -> _locations[] = (object) [
                'country_id' => $country -> id,
                'country_name' => $country -> name,

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
    }

    private function load_json_data($source = null) {
        $file = __DIR__.'/data/'.strtolower($source).'.json';
        
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
            return str_contains($item -> {$key}, $value);
        });
    }
    
    public function all(){
        return $this -> _locations;
    }
    
    public function search(string $query){
        return $this -> _search($this -> _locations, 'name', $query, false);
    }

    public function countries(){
        return $this -> _countries;
    }
    
    public function country(string $key, $value){
        return $this -> _get($this -> _countries, $key, $value);
    }

    public function states(){
        return $this -> _states;
    }
    
    public function state(int $id){
        return $this -> _get($this -> _states, 'id', $id);
    }

    public function counties(){
        return $this -> _counties;
    }
    
    public function county(int $id){
        return $this -> _get($this -> _counties, 'id', $id);
    }

    public function cities(){
        return $this -> _cities;
    }
    
    public function city(int $id){
        return $this -> _get($this -> _cities, 'id', $id);
    }
}
