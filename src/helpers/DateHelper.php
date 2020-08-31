<?php


namespace lwyhelper\helpers;

class DateHelper
{
    /**
     * @var string 年月日之间的分隔符
     */
    private $dateDelimiter = '-';

    /**
     * @var string 时分秒之间的分隔符
     */
    private $timeDelimiter = ':';

    /**
     * @var string 年份格式化方式，可选值参照date函数
     */
    private $yearFmt = 'Y';

    /**
     * @var string 月份格式化方式，可选值参照date函数
     */
    private $monFmt = 'm';

    /**
     * @var string 日期格式化方式，可选值参照date函数
     */
    private $dayFmt = 'd';

    /**
     * @var string 小时格式化方式，可选值参照date函数
     */
    private $hourFmt = 'H';

    /**
     * @var string 分钟格式化方式，可选值参照date函数
     */
    private $minFmt = 'i';

    /**
     * @var string 秒格式化方式，可选值参照date函数
     */
    private $secFmt = 's';

    /**
     * @var integer 时间戳
     */
    private $timestamp = 0;

    /**
     * @var string 输出结果中包含的元素，ymdhis分别代表年月日时分秒
     */
    private $out = 'ymdhis';

    private $isOutTimestamp = false;

    /**
     * DateHelper constructor.
     * @param array $config
     */
    public function __construct($config = []){
        if(is_array($config)){
            foreach ($config as $key => $val){
                $this->setValue($key,$val);
            }
            //如果没有传入时间戳配置，默认当前时间
            if($this->timestamp  == 0){
                $this->timestamp = time();
            }
        }elseif(is_numeric($config)){
            $this->timestamp = $config;
        }else{
            throw new \InvalidArgumentException('config param except Array but '.gettype($config).' giving');
        }
    }

    /**
     * @param array $config
     * @return int|string
     */
    public static function now($config = []){
        return (new self($config))->result();
    }

    /**
     * @param $name
     * @param $val
     */
    private function setValue($name,$val){
        if(!property_exists($this,$name)){
            throw new \InvalidArgumentException("trying to set unknown property '{$name}'");
        }
        $this->$name = $val;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setYearFmt($val){
        $this->yearFmt = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setMonFmt($val){
        $this->monFmt = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setDayFmt($val){
        $this->dayFmt = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setHourFmt($val){
        $this->hourFmt = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setMinFmt($val){
        $this->minFmt = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setSecFmt($val){
        $this->secFmt = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setDateDmt($val){
        $this->dateDelimiter = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setTimeDmt($val){
        $this->timeDelimiter = $val;
        return $this;
    }

    /**
     * @param integer $val
     * @return $this
     */
    public function setTimestamp($val){
        $this->timestamp = $val;
        return $this;
    }

    /**
     * @param int $timestamp
     * @return int|string
     */
    private function result($timestamp = 0){
        if(!$timestamp) $timestamp = $this->timestamp;
        if($this->isOutTimestamp){
            return $timestamp;
        }
        return date($this->outputFormatter(),$timestamp);
    }

    private function outputFormatter(){
        $dateEle = [];
        $timeEle = [];
        $sort = true;
        if(in_array($this->out[0],['h','i','s'])){
            $sort = false;
        }
        foreach (str_split($this->out) as $v){
            switch ($v){
                case 'y': $dateEle[] = $this->yearFmt;break;
                case 'm': $dateEle[] = $this->monFmt;break;
                case 'd': $dateEle[] = $this->dayFmt;break;
                case 'h': $timeEle[] = $this->hourFmt;break;
                case 'i': $timeEle[] = $this->minFmt;break;
                case 's': $timeEle[] = $this->secFmt;break;
                default : break;
            }
        }
        if($sort){
            return implode($this->dateDelimiter,$dateEle).' '.implode($this->timeDelimiter,$timeEle);
        }
        return implode($this->timeDelimiter,$timeEle).' '.implode($this->dateDelimiter,$dateEle);
    }

    private function getTimestamp(){
        $this->isOutTimestamp = true;
    }

    public function shift($str){
        $str = explode(' ',strtolower(trim($str)));
        $formatStr = [];
        foreach ($str as $s){
            $type = $s[strlen($s)-1];
            $val = substr($s,0,-1);
            if(!is_numeric($val)){
                throw new \InvalidArgumentException('the value mast be a number but '.$val.' giving');
            }
            switch ($type){
                case 'y':array_push($formatStr,$val,'year');break;
                case 'm':array_push($formatStr,$val,'month');break;
                case 'd':array_push($formatStr,$val,'day');break;
                case 'h':array_push($formatStr,$val,'hour');break;
                case 'i':array_push($formatStr,$val,'minute');break;
                case 's':array_push($formatStr,$val,'second');break;
                case 'w':array_push($formatStr,$val,'week');break;
            }
        }
        return $this->result(strtotime(implode(' ',$formatStr),$this->timestamp));
    }

    public function lastMonday(){
        return $this->result(strtotime('last monday',$this->timestamp));
    }

    public function lastSunday(){
        return $this->result(strtotime('last sunday',$this->timestamp));
    }

    public function lastMonthBegin(){
        $time = strtotime(date('Y-m',$this->timestamp).' -1 month');
        return $this->result($time);
    }

    public function lastMonthEnd(){
        $time = strtotime(date('Y-m',$this->timestamp).' -1 second');
        return $this->result($time);
    }

    public function lastYearBegin(){
        $time = strtotime(date('Y-01-01',$this->timestamp).' -1 year');
        return  $this->result($time);
    }

    public function lastYearEnd(){
        $time = strtotime(date('Y-12-31 23:59:59',$this->timestamp).' -1 year');
        return  $this->result($time);
    }

    public function nextMonday(){
        return $this->result(strtotime('next monday',$this->timestamp));
    }

    public function nextSunday(){
        return $this->result(strtotime('next sunday',$this->timestamp));
    }

    public function nextMonthBegin(){
        $time = strtotime(date('Y-m',$this->timestamp).' +1 month');
        return $this->result($time);
    }

    public function nextMonthEnd(){
        $time = strtotime(date('Y-m',$this->timestamp).' +2 month -1 second');
        return $this->result($time);
    }

    public function nextYearBegin(){
        $time = strtotime(date('Y-01-01',$this->timestamp).' +1 year');
        return  $this->result($time);
    }
    public function nextYearEnd(){
        $time = strtotime(date('Y-12-31 23:59:59',$this->timestamp).' +1 year');
        return  $this->result($time);
    }

}