<?php

class Export {
    
    private $id;
    private $start_date;
    private $end_date;
    private $dashboard_id;
    private $url;
    private $email;
    private $format;
    private $type;
    private $date_demand;
    private $date_execute;

    
    public function getId(){
            return $this->id;
    }

    public function setId($id){
            $this->id = $id;
    }

    public function getStart_date(){
            return $this->start_date;
    }

    public function setStart_date($start_date){
            $this->start_date = $start_date;
    }

    public function getEnd_date(){
            return $this->end_date;
    }

    public function setEnd_date($end_date){
            $this->end_date = $end_date;
    }

    public function getDashboard_id(){
            return $this->dashboard_id;
    }

    public function setDashboard_id($dashboard_id){
            $this->dashboard_id = $dashboard_id;
    }

    public function getUrl(){
            return $this->url;
    }

    public function setUrl($url){
            $this->url = $url;
    }

    public function getEmail(){
            return $this->email;
    }

    public function setEmail($email){
            $this->email = $email;
    }

    public function getFormat(){
            return $this->format;
    }

    public function setFormat($format){
            $this->format = $format;
    }

    public function getType(){
            return $this->type;
    }

    public function setType($type){
            $this->type = $type;
    }

    public function getDate_demand(){
            return $this->date_demand;
    }

    public function setDate_demand($date_demand){
            $this->date_demand = $date_demand;
    }

    public function getDate_execute(){
            return $this->date_execute;
    }

    public function setDate_execute($date_execute){
            $this->date_execute = $date_execute;
    }    

    
}
