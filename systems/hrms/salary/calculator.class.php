<?php
/** Calculator
*
*
*/

class Calculator{
	
	public $salary_basic = 0,
	$salary_var = 0,
	$day_per_month = 30;
	
	public function __construct($emp=''){
		if($emp!= ''){
			$this->emp = $emp;
			$this->salary_basic = $emp->basic;
			$this->salary_var = $emp->var;
			$this->allowances = $emp->allowances;
		}
	}

	public function calculate($equation){
		if(is_int($equation)){
			return $equation;
		} else {
			$equation = preg_replace('/\s+/', '', $equation);
			if(strpos($equation, 'basic') !== false){
				$equation = str_replace('basic', $this->salary_basic, $equation);
			}
			if(strpos($equation, 'var') !== false){
				$equation = str_replace('var', $this->salary_var, $equation);
			}
			if(strpos($equation, 'allowances') !== false){
				$equation = str_replace('allowances', $this->allowances, $equation);
			}
			if(strpos($equation, 'bonus') !== false){
				$equation = str_replace('bonus', $this->calcBonus(), $equation);
			}
			if(strpos($equation, 'extra') !== false){
				$equation = str_replace('extra', $this->calcExtras(), $equation);
			}
			if(strpos($equation, 'absent') !== false){
				$equation = str_replace('absent', $this->calcAbsents(), $equation);
			}
			if(strpos($equation, 'discount') !== false){
				$equation = str_replace('discount', $this->calcDiscounts(), $equation);
			}
			if(strpos($equation, 'tax_gain') !== false){
				$equation = str_replace('tax_gain', $this->calcGainTax(), $equation);
			}
			if(strpos($equation, 'tax_stamp') !== false){
				$equation = str_replace('tax_stamp', $this->calcStampTax(), $equation);
			}		
			if(strpos($equation, 'insur_tot') !== false){
				$equation = str_replace('insur_tot', $this->calcInsur(), $equation);
			}
			if(strpos($equation, 'insur_soc') !== false){
				$equation = str_replace('insur_soc', $this->calcInsur('soc'), $equation);
			}
			$Cal = new Field_calculate();		
			return round($Cal->calculate($equation), 2);
		}
	}
		
	public function calcInsur($con='', $salary_type=''){ // con=('emp', 'soc',' all', '') // salary_type=('basic', 'var', '')
		global $this_system;
		if($this->emp->insur_no == ''){
			return 0;
		}
		$all_per = ($this->salary_basic * $this_system->getSettings('insur_basic_per')/100) + ($this->salary_var * $this_system->getSettings('insur_var_per')/100);

		if($all_per > $this_system->getSettings('insur_min_total') && $all_per < $this_system->getSettings('insur_max_total')){
			if($con == 'soc'){
				return ($this->salary_basic * $this_system->getSettings('insur_basic_share')/100) +($this->salary_var * $this_system->getSettings('insur_var_share')/100);
			} elseif($con == 'emp'){
				return ($this->salary_basic * (($this_system->getSettings('insur_basic_per') - $this_system->getSettings('insur_basic_share'))/100)) + ($this->salary_var * (($this_system->getSettings('insur_var_per') - $this_system->getSettings('insur_var_share'))/100));
			} else {
				return $all_per;
			}
		} else {
			if($all_per <= $this_system->getSettings('insur_min_total')){
				$sum = $this_system->getSettings('insur_min_total');
			} elseif($all_per > $this_system->getSettings('insur_max_total')){
				$sum = $this_system->getSettings('insur_max_total');
			}
			
			if($con == 'soc'){
				//$percent = $this_system->getSettings('insur_basic_share') / $this_system->getSettings('insur_basic_per');
				return $sum * ($this_system->getSettings('insur_basic_per') + $this_system->getSettings('insur_var_per')) /100;
			} elseif($con == 'emp'){
				return $sum * ($this_system->getSettings('insur_basic_per') - $this_system->getSettings('insur_basic_share')) + ($this_system->getSettings('insur_basic_per') - $this_system->getSettings('insur_var_share')) /100;
			} else {
				return $sum;
			}
		}
	}
	
	public function getTotalCredit(){
		return $this->salary_basic + $this->salary_var + + $this->allowances + $this->calcBonus() + $this->calcExtras() - $this->calcAbsents() - $this->calcDiscounts();
	}
	
	public function calcTax($total, $per, $exclude){
		if($total > 0){
			$result = ($total - $exclude) * $per/100;
			return $result>0 ? $result : 0;
		} else {
			return 0;
		}
	}
	
	public function calcGainTax(){
		global $this_system;
		return $this->calcTax($this->getTotalCredit() , $this_system->getSettings('tax_worker'), $this_system->getSettings('tax_worker_exclude')/12) ;
	}

	public function calcStampTax(){
		global $this_system;
		return $this->calcTax($this->getTotalCredit() , $this_system->getSettings('tax_stamp_per'), $this_system->getSettings('tax_stamp_exclude')) ;
	}
	
	public function calcAbsents(){
		if(!isset($this->emp)){
			return false;
		} else {
			$emp = $this->emp;
			$values = do_query_obj("SELECT SUM(value) as total FROM absents WHERE emp_id=$emp->id AND day>=$this->begin AND day<=$this->end");
			if($values != false ){
				$profil = $this->emp->getProfil();
				return $values->total  * $profil->getEmpDayValue($emp); 
			} else {
				return 0;
			}
		}
	}
	
	public function calcBonus(){
		if(!isset($this->emp)){
			return false;
		} else {
			$emp = $this->emp;
			$values = do_query_obj("SELECT SUM(cash) as cash, SUM(value) as days FROM bonus WHERE emp_id=$emp->id AND day>=$this->begin AND day<=$this->end");
			if($values != false){
				$profil = $this->emp->getProfil();
				return $values->cash + ($values->days * $profil->getEmpDayValue($emp));		
			} else {
				return 0;
			}
		}
	}

	public function calcExtras(){
		if(!isset($this->emp)){
			return false;
		} else {
			$emp = $this->emp;
			$values = do_query_obj("SELECT SUM(value) as total FROM overtime WHERE emp_id=$emp->id AND day>=$this->begin AND day<=$this->end");
			if($values != false){
				return $values->total;	
			} else {
				return 0;
			}
		}
	}

	public function calcDiscounts(){
		if(!isset($this->emp)){
			return false;
		} else {
			$emp = $this->emp;
			$values = do_query_obj("SELECT SUM(cash) as cash, SUM(value) as days FROM discounts WHERE emp_id=$emp->id AND day>=$this->begin AND day<=$this->end");
			if($values != false){				
				$profil = $this->emp->getProfil();
				return $values->cash + ($values->days * $profil->getEmpDayValue($emp));		
			} else {
				return 0;
			}
		}
	}

}

class Field_calculate {
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';

    const PARENTHESIS_DEPTH = 10;

    public function calculate($input){
        if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);

            //  Calculate each of the parenthesis from the top
            $i = 0;
            while(strpos($input, '(') || strpos($input, ')')){
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);

                $i++;
                if($i > self::PARENTHESIS_DEPTH){
                    break;
                }
            }

            //  Calculate the result
            if(preg_match(self::PATTERN, $input, $match)){
                return $this->compute($match[0]);
            }

            return 0;
        }

        return $input;
    }

    private function compute($input){
        $compute = create_function('', 'return '.$input.';');

        return 0 + $compute();
    }

    private function callback($input){
        if(is_numeric($input[1])){
            return $input[1];
        }
        elseif(preg_match(self::PATTERN, $input[1], $match)){
            return $this->compute($match[0]);
        }

        return 0;
    }
}