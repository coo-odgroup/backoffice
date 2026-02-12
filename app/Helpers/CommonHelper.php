<?php

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
    }
