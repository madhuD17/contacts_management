<?php
/*
@Description    : Function to print with <pre> and exit code execution.
@Author         : Madhu Dewda
@Date           : 03-01-2026
 */
function pr($var = '')
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    exit;
}

function pr1($var = '')
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}
/*
@Description    : Function to print with <pre> and exit code execution.
@Author         : Madhu Dewda
@Date           : 03-01-2026
 */
function get_input_types()
{
    return ['text','date','number','textarea'];
}

function get_json_decode($json)
{
    $data = json_decode($json, true);
    return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : [];
}

function remove_from_array($value, $array)
{
    return array_values(array_filter($array, function ($key) use ($value) {
        return $key !== $value;
    }));
}

 
