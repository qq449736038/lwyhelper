<?php


namespace lwy\helpers;

use InvalidArgumentException;
use RuntimeException;

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

    private $outTimestamp = false;

    /**
     * DateHelper constructor.
     * @param mixed $config 数组或者时间戳
     * @throws \Exception
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
            //throw new \Exception('config param except ArrayHelper but give one '.gettype($config));
            throw new InvalidArgumentException('config param except ArrayHelper but give one '.gettype($config));
        }
    }

    public static function now($config = []){
        return (new self($config))->result();
    }

    private function setValue($name,$val){
        if(!property_exists($this,$name)){
            throw new RuntimeException("trying to set unknown property '{$name}'");
            //throw new \Exception("trying to set unknown property '{$name}'");
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

    private function result($timestamp = 0){
        if(!$timestamp) $timestamp = $this->timestamp;
        if($this->outTimestamp){
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
        $this->outTimestamp = true;
    }

}