<?php

namespace com\github\tncrazvan\AsciiTable;

use com\github\tncrazvan\CatPaw\Tools\Arrays;

class AsciiTable{
    private $rows;
    private $numberOfCols=0;
    private $masterRow;
    private $options;
    private $width=null;
    private $styles = [];
    public function __construct(array $options=[]){
        $this->options = $options;
    }

    public static function resolve($input,bool $countLines=false, array $options=[]):AsciiTable{
        $table = new AsciiTable($options);
        if(\is_object($input))
            $input = get_object_vars($input);

        $table->add("Name","Value");
        if(($isArray = \is_array($input)) && Arrays::isAssociative($item,$length)){
            for($i=0;$i<$length;$i++){
                $table->add($key,AsciiTable::resolve($item)->toString($countLines));
            }
        }else{
            foreach($input as $key => &$item){
                if($isArray || \is_object($item)){
                    $table->add($key,AsciiTable::resolve($item)->toString($countLines));
                }else{
                    $table->add($key,$item);
                }
            }
        }
        
        return $table;
    }

    public function style(int $index, array $options):void{
        $this->styles[$index] = $options;
    }

    public function getWidth():int{
        return $this->width;
    }

    public function add(...$inputCels):void{
        $cels = [];
        for($i=0,$end=count($inputCels)-1;$i<=$end;$i++){
            if($inputCels[$i] instanceof AsciiCel){
                $cels[] = $inputCels[$i];
            }else{
                $cels[] = new AsciiCel($inputCels[$i],isset($this->styles[$i])?$this->styles[$i]:$this->options);
            }
        }
        $row = new AsciiRow($this->options,$this->styles,...$cels);
        $this->rows[] = $row;

        $this->toString();
    }

    public function toString(bool $countLines = false):string{
        $this->findRowWithMostCels();
        $this->fixWidths();
        $result="";
        $numberOfRows = count($this->rows);
        for($i=0;$i<$numberOfRows;$i++){
            if($i>0){
                $result .= preg_replace('/^.+\n/', '', $this->rows[$i]->toString()).($i+1 === $numberOfRows?"":"\n");
            }else{
                $result .= $this->rows[$i]->toString().($i+1 === $numberOfRows?"":"\n");
                $this->width = \strlen($result);
            }
        }
        if($countLines){
            $tmp = preg_split('/\n/',$result);
            $length = count($tmp);
            $result = "";
            $rowNumber = 1;
            for($i=0;$i<$length;$i++){
                if($tmp[$i][0] !== '|'){
                    $rowNumber = 1;
                    continue;
                }
                $tmp[$i] = $tmp[$i]." <= [$rowNumber]";
                $rowNumber++;
            }
            $result = implode("\n",$tmp);
        }
        return $result;
    }

    private function findRowWithMostCels():void{
        $length = count($this->rows);
        $num=0;
        for($i=0;$i<$length;$i++){
            $num = $this->rows[$i]->getNumberOfCels();
            if($num > $this->numberOfCols){
                $this->numberOfCols = $num;
                $this->masterRow = $this->rows[$i];
            }
        }
    }

    private function fixWidths():void{
        $numberOfCols = $this->numberOfCols;
        $widestCel;
        $cel;
        $width;
        for($i=0;$i<$numberOfCols;$i++){
            $widestCel = $this->getWidestCelByIndex($i);
            $widestCelWidth = $widestCel->getWidth();
            $numberOfRows = count($this->rows);
            for($j=0;$j<$numberOfRows;$j++){
                if(($cel = $this->rows[$j]->getCel($i))){
                    $width = $widestCelWidth - $cel->getWidth();
                    if($width > 0){
                        $this->rows[$j]->extendCelBy($i,$width);
                    }
                }
            }
        }
    }

    private function getWidestCelByIndex(int $index):AsciiCel{
        $length = count($this->rows);
        $cel = null;
        for($i=0;$i<$length;$i++){
            if($cel === null || $cel->getWidth() < $this->rows[$i]->getCel($index)->getWidth()){
                $cel = $this->rows[$i]->getCel($index);
            }
        }
        return $cel;
    }
}