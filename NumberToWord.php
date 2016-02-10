<?php 
	class NumberToWord{

		private $number = 0;
		private $words = array();
		private $word = '';
		private $caseNumbers = array();
		private $currencyWord = array('zł.','gr.');
		private $isFraction = false;

		private $counts = array(
			array(
				'zero','jeden','dwa','trzy','cztery','pięć','sześć','siedem','osiem','dziewięć','dziesieć',
				'jedenaście','dwanaście','trzynaście','czternaście','piętnaście','szesnaście','siedemnaście',
				'osiemnaście','dziewiętnaście'
			),
			array(
				'dwadzieścia','trzydzieści','czterdzieści','piećdziesiąt','sześcdziesiąt','siedemdziesiąt',
				'osiemdziesiąt','dziewięćdziesiąt'
			),
			array(
				'sto','dwieście','trzysta','czterysta','pięćset','sześcset','siedemset','osiemset','dziewięćset'
			)
		);

		private $cases = array(
			array('tysiąc','tysiące','tysięcy'),
			array('milion','miliony','milionów'),
			array('miliard','miliardy','miliardów'),
			array('bilion','biliony','bilionów'),
		);

		public function __construct($number){
			$this->number = number_format($number,2,',','.');
			$this->transform();
		}

		public function show(){
			return $this->word;
		}

		private function transform(){
			$number = explode(',',$this->number);					
			$number[0] = array_reverse(explode('.',$number[0]));
			$number[1] = array_reverse(array($number[1]));
			$this->run($number);					
		}

		private function run($number){
			foreach($number as $i => $num){
				$this->words = array();				
				if($i){
					$this->isFraction = true;
				}				
				foreach($num as $n){
					$this->createWords(str_split(str_pad($n,3,0,STR_PAD_LEFT)));
				}				
				$this->words = array_chunk($this->words,3);	
				$this->joinWords();			
			}
		}

		private function createWords($num){			
			$size = count($num);						

			foreach ($num as $j => $n) {				
				$number = $n;
				if($n){					
					if($j == 2 && $num[1] == 1){
						$n = 0;						
					}
					
					if($j == 1 && $n == 1){
						$n = 10 + $num[2];						
					}
					$number = str_pad($n, ($size-$j), 0, STR_PAD_RIGHT);					
				}			
				
				$this->caseNumbers[] = $number;								

				if($number == 0){					
					$this->words[] = null;						
					continue;										
				}

				if($number < 20){
					$this->words[] = $this->counts[0][$number];
				}elseif($number < 100){
					$this->words[] = $this->counts[1][($number/10)-2];
				}else{					
					$this->words[] = $this->counts[2][($number/100)-1];
				}				
			}
		}

		private function joinWords(){
			$size = count($this->words);
			$this->caseNumbers = array_reverse(array_chunk($this->caseNumbers,3));			
			
			foreach(array_reverse($this->words) as $i => $word){				
				foreach ($word as $w) {					
					if($w){
						$this->word .= ' '.$w;
					}
				}
				if($this->isFraction){
					if(!count(array_filter($word))){
						$this->word .= ' zero';
					}
				}								
				if($size > 1){
					$caseIndex = $this->getCaseIndex($i);					
					$this->word .= ' '.$this->cases[$size-2][$caseIndex];	
				}				
				$size--;				
			}

			if(!$this->isFraction){
				$this->word .= ' '.$this->currencyWord[0].' i ';	
			}else{
				$this->word .= ' '.$this->currencyWord[1];	
			}
		}

		private function getCaseIndex($i){			
			$caseSum = array_sum($this->caseNumbers[$i]);						
			if($caseSum == 1){
				$index = 0;
			}elseif($caseSum < 5){
				$index = 1;
			}else{
				$index = 2;
				$splittedSum = str_split($caseSum);
				$num = $splittedSum[count($splittedSum)-1];
				if($num > 2 && $num < 5){
					$index = 1;
				}				
			}
			return $index;
		}
	}
?>