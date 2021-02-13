<?php
namespace AppBundle\Service;

class CommonUtils 
{
    public static function findTraitValue($id, $traits)
    {
        $c = count($traits);
        for ($i=0; $i<$c; $i++) {
            if ($traits[$i]->getTrait() == $id) {
                return $traits[$i]->getValue();
            }
        }
        return null;
    }

}