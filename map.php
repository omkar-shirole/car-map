<?php
$map = new CarMap($argv);

$map->run();
$map->showDetails();

class CarMap
{

    private $roadLength;
    private $roadType;
    private $garageDistance;
    private $fullTankDistance;
    private $totDis;
    private $fualDetour;
    private $refilled;

    public function CarMap($arguments)
    {

        foreach ($arguments as $arg) {
            if (strpos($arg, "--road_type=") !== false) {
                $this->roadType = str_replace("--road_type=", "", $arg);
            }
            if (strpos($arg, "--road_length=") !== false) {
                $this->roadLength = str_replace("--road_length=", "", $arg);
            }
        }

        if (!in_array($this->roadType, ['urban', 'rural'])) {
            echo PHP_EOL . "Invalid --road_type. Allowed values are [urban, rural]." . PHP_EOL;
        }

        if (!(is_numeric($this->roadLength) && $this->roadLength > 0)) {
            echo PHP_EOL . "Invalid --road_length. Allowed values are [positive number]." . PHP_EOL;
        }

        // The car can drive itself up to 200km after refueling
        $this->fullTankDistance = 200;
        
        if ($this->roadType == "urban") {

            // The garage is 20km from urban areas
            $this->garageDistance = 20;

            // On urban roads, the maximum range of the car drops by 25% due to traffic
            $this->fullTankDistance = $this->fullTankDistance - ($this->fullTankDistance * 25 / 100);
        } else if ($this->roadType == "rural") {

            // The garage is 50km from rural areas
            $this->garageDistance = 50;
        }

        $this->totDis = 0;

        // From any point, the car can refuel itself by taking a detour that is a round-trip of 10km
        $this->fualDetour = 10;

        $this->refilled = 0;
    }

    public function showDetails()
    {

        $refillTime = $this->refilled * 30;
        $travelTIme = ($this->totDis/75) * 60;

        echo PHP_EOL."##########################################".PHP_EOL;
        echo PHP_EOL."Total Time: Refill Time: ".$refillTime." Minutes + Travel Time (75KM/H): ".$travelTIme." Minutes = ".(($refillTime+$travelTIme)/60)." Hours".PHP_EOL;
        echo PHP_EOL."No. of times refilled: ".$this->refilled.PHP_EOL;
        echo PHP_EOL."Total Distance Travelled: ".$this->totDis.PHP_EOL;
        echo PHP_EOL."##########################################".PHP_EOL;
    }

    public function run()
    {

        $this->garageToStartPoint();

        $this->startPointToEndPoint();
    }

    public function garageToStartPoint()
    {
        $this->totDis += $this->garageDistance;

        echo PHP_EOL."Garage------->Start Point (".$this->garageDistance." KM Travelled)".PHP_EOL;
    }

    public function startPointToEndPoint()
    {
        for($i = 0, $refilled = 0; $i < $this->roadLength;) {

            $refilAfter = 0;

            if($i == 0) {
                $refilAfter = $this->fullTankDistance - $this->garageDistance - $this->fualDetour;
            } else {
                $refilAfter = $this->fullTankDistance - $this->fualDetour;
            }

            $travelled = $refilAfter + $this->fualDetour;
            
            $i += $travelled;
            
            $this->totDis += $travelled;

            $this->refilled++;

            echo PHP_EOL."Start Point------->End Point (".$travelled." KM Travelled, Refilled: ".$this->refilled.")".PHP_EOL;
            sleep(1);

        }
    }
}
