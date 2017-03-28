<?php

class HelpController extends BaseController{

    public function getIndex()
    {
        return View::make('help.index');
    }



}