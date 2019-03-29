<?php

class KeyWordDensityComponent{

    protected $_wordDensityHashTable = array();
    protected $_splitPattern = "/[\s,\+]+/";
    protected $_stopWords = array();
    protected $_schema = 'redbeard';
    protected $_trimCharacters = " \t\r\n.!?:\x0B\0";

    public function loadStopWordsText($pathToStopWords){

        $this->_stopWords = explode("\n", file_get_contents($pathToStopWords));
    }

    public function setTrimCharacters($trimCharacters){
        $this->_trimCharacters = $trimCharacters;
    }

    protected function tokenize($string){
        return preg_split($this->_splitPattern, strtolower($string));
    }

    public function getKeyWordDensityTable($sort = false){
        if($sort)
        {
            foreach ($this->_wordDensityHashTable as &$table)
            {
                arsort($table);
            }
        }
        return $this->_wordDensityHashTable;
    }

    public function clearKeyWordDensityTable(){
        unset($this->_wordDensityHashTable);
        $this->_wordDensityHashTable = array();
    }

    public function setSplitPattern($pattern){
        $this->_splitPattern = $pattern;
    }

    public function keyWordDensityAnalysis($arrayOfWords = array(), $maxWords = 2){
        for($start = 0; $start < $maxWords; $start++){

            if(!isset($this->_wordDensityHashTable[$start])){
                $this->_wordDensityHashTable[$start] = array();
            }

            for($index = $start; $index < count($arrayOfWords)+$start; $index++){

                $words = array_slice($arrayOfWords,$index - $start, $start+1);

                if(count($words) < $start+1){
                    continue;
                }

                $phrase = trim( implode(" ",$words), $this->_trimCharacters);

                if(empty($this->_wordDensityHashTable[$start][$phrase])){
                    $this->_wordDensityHashTable[$start][$phrase] = 1;
                }
                else{
                    $this->_wordDensityHashTable[$start][$phrase]++;
                }
            }
        }
    }

    public function analyzeString($string, $maxWords){
        $this->keyWordDensityAnalysis( $this->filterStopWords( $this->tokenize( $string ) ), $maxWords);
    }

    public function addStopWords($stopWords){

        if(is_array($stopWords)){
            $this->_stopWords = array_unique(array_merge($stopWords, $this->_stopWords));
        }

    }

    public function filterStopWords($arrayOfWords){
        $nonStopWords = array();
        for($index = 0; $index < count($arrayOfWords); $index++){

            if(!$this->isStopWord($arrayOfWords[$index])){
                $nonStopWords[] = $arrayOfWords[$index];
            }
        }

        unset($arrayOfWords);
        return $nonStopWords;
    }

    public function getStopWords(){
        return $this->_stopWords;
    }

    protected function isStopWord($word){
        return in_array($word,$this->_stopWords);
    }

}
