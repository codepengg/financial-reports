<?php

if (! function_exists('numberToIdr')) {
    function numberToIdr(int|float $number): string {
        return \Illuminate\Support\Number::currency($number, 'IDR', 'id');
    }
}
