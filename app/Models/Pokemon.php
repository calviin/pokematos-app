<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model {

    protected $fillable = [ 'pokedex_id', 'niantic_id', 'name_fr', 'name_ocr', 'base_att', 'base_def', 'base_sta', 'parent_id', 'boss', 'boss_level'];
    protected $table = 'pokemons';
    protected $appends = ['thumbnail_url', 'cp', 'name', 'boss_cp'];
    protected $casts = [
        'boss' => 'boolean',
        'shiny' => 'boolean',
    ];

    public function getNameAttribute() {
        return $this->name_fr;
    }

    public function getThumbnailUrlAttribute() {
        return 'https://assets.profchen.fr/img/pokemon/pokemon_icon_'.$this->pokedex_id.'_'.$this->form_id.'.png';
    }

    public function getCpAttribute() {
        return [
            'lvl20' => [
                'min' => $this->getCp(20, 10, 10, 10),
                'max' => $this->getCp(20, 15, 15, 15),
            ],
            'lvl25' => [
                'min' => $this->getCp(25, 10, 10, 10),
                'max' => $this->getCp(25, 15, 15, 15),
            ],
        ];
    }

    public function getBossCpAttribute() {
        if( empty( $this->boss_level ) ) return false;
        $levels = [
            1 => 600,
            2 => 1800,
            3 => 3600,
            4 => 9000,
            5 => 15000,
            6 => 9000,
        ];
        $rStamina = $levels[$this->boss_level];
        $cp = ( ($this->base_att+15) * sqrt($this->base_def+15) * sqrt($rStamina) ) / 10;
        return floor($cp);
    }

    public function getCp( $lvl, $ivAttack, $ivDefense, $ivStamina ) {
        $cp_multiplier = Helpers::getCpScalar($lvl);
        $calc_attack = $this->base_att + $ivAttack;
        $calc_defense = $this->base_def + $ivDefense;
        $calc_stamina = $this->base_sta + $ivStamina;
        $cp = (int)($calc_attack * pow($calc_defense, 0.5) * pow($calc_stamina, 0.5) * pow($cp_multiplier, 2) / 10);
        return $cp;
    }




}
