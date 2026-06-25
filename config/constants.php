<?php
//file : app/config/constants.php

return [
 
    /* Messages */
    'RECORD_FETCHED' => 'Record Fetched Successfully',
    'RECORD_REMOVED' => 'Record Removed Successfully',
    'RECORD_ADDED' => 'Record Added Successfully',
    'RECORD_UPDATED' => 'Record Updated Successfully',
    'RECORD_NOT_FOUND' => 'Record Not Found',
    'RECORD_EXISTS' => 'Record Already Exists',
    'RECORD_CREATED'=>'Record Created Successfully',
    'RECORD_UPDATE'=>'Record Updated Successfully',
    'INVALID_ARGUMENT_PASSED' => 'Invalid Argument Passed',
    'UNABLE_CHANGE_STATUS' => 'Unable to Change Status',
    'OTP_REQUIRED'=> 'OTP is required',
    'STATUS_UPDATED_SUCCESSFULLY' => 'Status Updated Successfully',
 
 
    /* General */
    'ALL_RECORDS' => 10000,
 
    /* OTP */
    'OTP_GEN' => 'OTP generated',
    'OTP_NULL' => 'No Value provided in OTP',
    'OTP_INVALID' => 'Invalid OTP',
    'OTP_VERIFIED' => 'OTP verification successful',
    'OTP_NOT_VERIFIED' => 'OTP is not yet validated',
 
    /* Auth */
    'RESET_PASSWORD_SUCCESS' => 'Reset Password is successful',
    'UNREGISTERED' => 'User is not registered',
    'ROLE_MISMATCH' => 'Role Mismatches',
    'PWD_MISMATCH' => 'Incorrect Password',
    'INVALID_EMAIL' => 'Email ID does not exist',
    'INACTIVE_USER' => 'User Not Active',
    'LOGIN_SUCCESSFUL' => 'Login successful',
    'REGT_SUCCESS' => 'Registration Successful',
    'INVALID_OTP' => 'OTP is Invalid',
    'OTP_EXPIRED' => 'OTP is Expired',
 
 
    /* URLs */
    'CONSUMER_API_URL' => 'https://testing.odbus.co.in/api/',
    'CONSUMER_FRONT_URL' => 'https://odtestingssr.odbus.co.in/',
    
 
    /* HTTP Status Codes */
    'HTTP_OK' => 200,
    'HTTP_CREATED' => 201,
    'HTTP_ACCEPTED' => 202,
    'HTTP_NO_CONTENT' => 204,
    'HTTP_BAD_REQUEST' =>400,
    '                                                                                                                                                                                                                                                                                                                                                                      ' => 400,
    'HTTP_UNAUTHORIZED' => 401,
    'HTTP_CONFLICT' => 409,
    'HTTP_FORBIDDEN' => 403,
    'HTTP_NOT_FOUND' => 404,
    'HTTP_METHOD_NOT_ALLOWED' => 405,
    'HTTP_UNPROCESSABLE_ENTITY' => 422,
    'HTTP_TOO_MANY_REQUESTS' => 429,
    'HTTP_INTERNAL_SERVER_ERROR' => 500,
    'HTTP_SERVICE_UNAVAILABLE' => 503,
 
    'SERVER_ERROR_MESSAGE' => 'Something went wrong! Please try again later.',
 
    /* PhonePe */
    'MID' => env('MID', 'ODBUSUAT'),
    'CLIENT_ID' => env('CLIENT_ID', 'ODBUSUAT_251114164525072'),
    'CLIENT_VERSION' => env('CLIENT_VERSION', 1),
    'CLIENT_SECRET' => env('CLIENT_SECRET', 'NGYyMjVmYTAtMjU2My00NWIxLTg1MzItZjhjNjRjZDQwNDRk'),
    'GRANT_TYPE' => env('GRANT_TYPE', 'client_credentials'),
    'PHONPE_API_URL' => env('PHONPE_API_URL', 'https://api-preprod.phonepe.com/apis/pg-sandbox/'),
    'PHONPE_REDIRECT_URL' => env('PHONPE_REDIRECT_URL', 'http://localhost:4200/payment-status'),
];
 