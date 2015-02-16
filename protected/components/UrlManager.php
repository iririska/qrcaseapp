<?php

class UrlManager extends CUrlManager
{
    public function createUrl($route,$params=array(),$ampersand='&')
    {
        if(preg_match('/[A-Z]/',$route)!==0)
        {
            $route=strtolower(preg_replace('/(?<=\\w)([A-Z])/','-\\1',$route));
        }
        return parent::createUrl($route,$params,$ampersand);
    }

    public function parseUrl($request)
    {
        $route=parent::parseUrl($request);
        if(substr_count($route,'-')>0)
        {
            $route=lcfirst(str_replace(' ','',ucwords(str_replace('-',' ',$route))));
        }
        return $route;
    }
}
