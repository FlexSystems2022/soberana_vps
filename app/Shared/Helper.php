<?php

namespace App\Shared;

/**
 * @method static int|null hourConvertToMinutes(string|null $text)
 */
final class Helper
{
	/**
	 * Get Minutes from hour
	 * 
	 * @param string|null $text
	 * @return int|null
	 **/
	protected function hourConvertToMinutes(?string $text): ?int
	{
        if(!$text) {
            return null;
        }

		$str = str($text ?? '');

		$hour = intval($str->substr(0, 2)->padLeft(2, '0')->toString());

		$minutes = intval($str->after(':')->padLeft(2, '0')->toString());

        $value = ($hour * 60) + $minutes;

		return $value > 0 ? $value : null;
	}


	/**
	 * Round Abnt Number
	 * 
	 * @param float $value
	 * @return float
	 **/
	protected function roundAbnt(float $value): float
    {
        if(array_key_exists(1, explode('.', $value))){
            $int = explode('.', $value)[0];
            $decimal = explode('.', $value)[1];
            $preserv_ = substr($decimal, 0, 2);
            $preserv_1 = substr($preserv_, 0, 1);
            $preserv_2 = substr($preserv_, 1, 1);

            if(intval(substr($decimal, 2, 1)) < 5){ 
                $preserv_2 = floatval($preserv_2) + 0;
            }
            
            if(intval(substr($decimal, 2, 1)) > 5){
                if ($preserv_2 == '9') {
                    $preserv_2 = 0;
                    if ($preserv_1 == '9') {
                        $preserv_1 = 0;
                        $int = floatval($int) + 1;
                    } else {
                        $preserv_1 = floatval($preserv_1) + 1;
                    }
                }else{
                    $preserv_2 = floatval($preserv_2) + 1;
                }
            }

            if(intval(substr($decimal, 2, 1)) == 5){
                if(intval(substr($decimal, 3, 1)) > 0){
                    if ($preserv_2 == '9') {
                        $preserv_2 = 0;
                        if ($preserv_1 == '9') {
                            $preserv_1 = 0;
                            $int = floatval($int) + 1;
                        } else {
                            $preserv_1 = floatval($preserv_1) + 1;
                        }
                    }else{
                        $preserv_2 = floatval($preserv_2) + 1;
                    }
                }elseif(intval(substr($decimal, 3, 1)) == 0){
                    if(intval(substr($decimal, 0, 2)) % 2 == 1){
                        if ($preserv_2 == '9') {
                            $preserv_2 = 0;
                            if ($preserv_1 == '9') {
                                $preserv_1 = 0;
                                $int = floatval($int) + 1;
                            } else {
                                $preserv_1 = floatval($preserv_1) + 1;
                            }
                        }else{
                            $preserv_2 = floatval($preserv_2) + 1;
                        }
                    }else{
                        $preserv_2 = floatval($preserv_2) + 0;
                    }
                }
            }
            $preserv_ = $preserv_1 . $preserv_2;
            $value = $int . '.' . $preserv_;
        }
        return floatval($value);
    }

    /**
     * Get the minutes to hours
     * 
     * @param int $minutes
     * @return string
     */
    protected function minutesToHours(int $minutes): string
    {
        if($minutes <= 0) {
            return '00:00';
        }

        $prefix = '';
        if($minutes < 0) {
            $minutes = $minutes * -1;
            $prefix = '-';
        }

        $data = [
            'hours' => floor($minutes / 60),
            'minutes' => str_pad(($minutes % 60), 2, "0", STR_PAD_LEFT),
        ];

        return sprintf('%s%02d:%02d', $prefix, $data['hours'], $data['minutes']);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     *
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = new static;

        return $instance->$method(...$args);
    }
}