<?php
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;

    if (!function_exists('htmlEncode')) {

        /**
         * Encode special characters safely
         *
         * @param mixed $value
         * @return string|null
         */
        function htmlEncode($value)
        {
            if (is_null($value)) {
                return null;
            }

            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }

        if (!function_exists('getUserNameById')) {

            function getUserNameById($userId)
            {
                if (empty($userId)) {
                    return '--';
                }

                return \DB::table('users')
                    ->where('id', $userId)
                    ->value('name') ?? '--';
            }
        }
    }

        if (!function_exists('htmlDecode')) {
            
        /**
         * Decode HTML special characters
         *
         * @param mixed $value
         * @return string|null
         */
        function htmlDecode($value)
        {
            if (is_null($value)) {
                return null;
            }

            return htmlspecialchars_decode(trim($value), ENT_QUOTES);
        }

    }
