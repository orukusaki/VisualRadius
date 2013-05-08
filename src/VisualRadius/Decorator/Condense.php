<?php
Namespace VisualRadius\Decorator;

class Condense implements \VisualRadius\IDecorator
{
    public function decorate($data)
    {
    for ($date=$firstDate;$date<=$lastDate;$date=strtotime('+1 day', $date)){
    If (!array_key_exists($date, $map)){ // There were no sessions today.

        If ($i>0 and $slots[$i-1]['type']=="gap"){
            $slots[$i-1]['days']++;
        } else {
            $slots[$i++]=array('type'=>'gap', 'days'=>1);
        }
    } elseif (sizeof($map[$date])==1 and $map[$date][0]['type']=='Box') { // There's only one thing in this array and it's a continuous connection
        If ($i>0 and $slots[$i-1]['type']=="cont"){
            $slots[$i-1]['days']++;
        } else {
            $slots[$i++]=array('type'=>'cont', 'days'=>1, 'service'=>$map[$date][0]['service']);
        }
    } else {
        $slots[$i++]=array('type'=>'draw', 'date'=>$date);
    }
}
    }
}