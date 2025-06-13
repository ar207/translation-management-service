<?php

function error_processor($validator)
{
    $err_keeper = [];
    foreach ($validator->errors()->getMessages() as $index => $error) {
        $err_keeper[] = [
            'code' => $index,
            'message' => $error[0],
        ];
    }
    return $err_keeper;
}
