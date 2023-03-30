<?php

namespace FormViewCreation;

class BuildeForm
{
    public static function build($Form){

        if (empty($Form)){
            return false;
        }
        if(gettype($Form) !== "array"){
            return false;
        }

        $formHtml = "<form ";

        if(isset($Form['form']['action'])){
            $formHtml .= "action='{$Form['form']['action']}' ";
        }
        if(isset($Form['form']['method'])){
            $formHtml .= "method='{$Form['form']['method']}' ";
        }
        if(isset($Form['form']['enctype'])){
            $formHtml .= "enctype='{$Form['form']['enctype']}' ";
        }
        if(isset($Form['form']['class'])){
            $formHtml .= "class='{$Form['form']['class']}' ";
        }
   $formHtml .= ">";
        if(isset($Form['fields'])){

            $fields = "";
            foreach ($Form['fields'] as $field){
                extract($field);

                if($type !== "textarea" && $type !== "select" && $type !== "radio" && $type !== "checkbox"){
                    $labels = "<div class='form-group'>";
                    if(!empty($label)){
                        $labels .= "<label for='{$id}'>{$label}</label>";
                    }
                    $input = "<input type='{$type}' name='{$name}' id='{$id}' placeholder='{$placeholder}' class='{$class}'>";
                    $labels .= $input."</div>";

                    $fields .= $labels;
                }

                if($type === "select"){
                    $optionline = "";
                    foreach ($options as $option){
                        $op = "<option value='{$option['value']}'>{$option['text']}</option>";
                        $optionline .= $op;
                    }
                    $lables = "";
                    if(!empty($label)){
                        $lables .="<label for='{$id}'>{$label}</label>";
                    }

                   $select = "<div class='form-group'>".$lables."<select name='{$name}' id='{$id}' class='{$class}'>{$optionline}</select></div>";
                    $fields .= $select;

                }

                if($type === "textarea"){
                    $input = "<textarea name='{$name}' id='{$id}' cols='{$cols}' rows='{$rows}' class='{$class}'></textarea>";
                    $lables = "<div class='form-group'>";
                    if(!empty($label)){
                        $lables .="<label for='{$id}'>{$label}</label>";
                    }
                    $lables .=$input.'</div>';
                    $fields .= $lables;
                }

                if($type === "radio"){
                    $lables = "<div class='form-group'><label>{$label}</label>";
                    foreach ($radios as $radio){
                        $input = "<input type='{$radio['type']}' name='{$radio['name']}' id='{$radio['id']}' class='{$radio['class']}' value='{$radio['value']}'>";
                        $inputlabel = "";
                        if(!empty($radio['label'])){
                            $inputlabel .="<label for={$radio['id']}>{$radio['label']}</label>";
                        }
                        $lables .= $inputlabel.$input;
                    }
                    $fields .= $lables."</div>";
                }
                if($type === "checkbox"){
                    $lables = "<div class='form-group'><label>{$label}</label>";
                    foreach ($checkboxes as $checkbox){
                        $input = "<input type='{$checkbox['type']}' name='{$checkbox['name']}' id='{$checkbox['id']}' class='{$checkbox['class']}' value='{$checkbox['value']}'>";
                        $inputlabel = "";
                        if(!empty($radio['label'])){
                            $inputlabel .="<label for={$checkbox['id']}>{$checkbox['label']}</label>";
                        }
                        $lables .= $inputlabel.$input;
                    }
                    $fields .= $lables."</div>";
                }
            }

            $fields .= "<div class='input-group'><button type='submit' name='{$Form['button']['name']}' class='{$Form['button']['class']}'>{$Form['button']['text']}</button></div>";
            $formHtml .= $fields ."</form>";
        }
        return self::formatForm($formHtml);
    }

    public static function formatForm($Form){
        if(empty($Form)){
            return NULL;
        }

        $Form = explode('</div>', $Form);

        $line = "";
        foreach ($Form as $form){
            $line .= $form."</div>\n";
        }
        $line = substr($line, 0, strlen($line) - 7);
        return trim($line);
    }
}