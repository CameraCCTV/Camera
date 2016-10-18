<?php

namespace RatCam\Services;

use RatCam\Models\Plug;
use RatCam\Models\PlugBeat;

class PlugService extends Service{

    public function findPlugByHardwareId(int $hardware_id){
        $plug = Plug::search()->where('hardware_id', $hardware_id)->execOne();
        if(!$plug){
            $plug = new Plug();
            $plug->hardware_id = $hardware_id;
            $plug->first_seen = date("Y-m-d H:i:s");
        }
        $plug->last_seen = date("Y-m-d H:i:s");
        $plug->save();
        return $plug;
    }

    public function beat(Plug $plug){
        $beat = new PlugBeat();
        $beat->created = date("Y-m-d H:i:s");
        $beat->plug_id = $plug->plug_id;
        $beat->save();
        return $beat;
    }
}