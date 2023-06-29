<?php

function phcPostType()
{
    global $post;
    $postTypes = unserialize(get_option('post_type_hits_cout_'));
    $postType = $post->post_type;

    return (isset($postTypes[$postType]) && $postTypes[$postType] == 1) ? true : false;
}


function phcReadArray($ar, $key)
{
    return (!empty($ar[$key])) ? $ar[$key] : false;
}


if (!function_exists('phcLoadTemplate'))
{
    function phcLoadTemplate($view, $fields = array())
    {
        if (!empty($fields))
        {
            foreach ($fields as $key => $field)
            {
                $$key = $field;
            }
        }

        $view = dirname(__FILE__) . '/templates/' . $view . '.php';
        if (!file_exists($view))
        {
            echo 'Template not found!';
            return false;
        }
        require_once $view;
    }
}

if (!function_exists('handlePostData'))
{
    function handlePostData($key)
    {
        if (!is_array($key))
        {
            if (isset($_POST[$key]))
            {
                return htmlspecialchars(trim($_POST[$key]));
            }
        }
        else
        {
            $out = [];
            foreach ($_POST as $k => $v)
            {
                $out[$k] = htmlspecialchars(trim($v));
            }
            return $out;
        }
    }
}
