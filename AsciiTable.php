<?php

namespace com\github\tncrazvan\AsciiTable;

class AsciiTable{
    private $rows;
    private $numberOfCols=0;
    private $masterRow;
    private $options;
    public function __construct(array $options=[]){
        $this->options = $options;
    }

    public function add(string ...$strings):void{
        $cels = [];
        foreach($strings as &$string){
            $cels[] = new AsciiCel($string,$this->options);
        }
        $row = new AsciiRow($this->options,...$cels);
        $this->rows[] = $row;
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
            }
        }
        if($countLines){
            $tmp = preg_split('/\n/',$result);
            $length = count($tmp);
            $result = "";

            for($i=0;$i<$length;$i++){
                $tmp[$i] = $tmp[$i]." <= [$i]";
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