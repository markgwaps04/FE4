<?php



abstract class ajax {

    static public function post_strict(Array $request_params,bool $remove_empty = false)
    {

        if (!constraint::isAjaxRequest()) show_404() and exit();

        /** @var $request Array */

        $request = $_POST;

        $valid = constraint::strict($request, $request_params, $remove_empty);

        if (!$valid)

            throw new Error("Invalid paramters");

        return $request;

    }

}



