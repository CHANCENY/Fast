<?php

namespace Modules;

class MakerModular
{
    /**
     * @var array[] add all columns on table that need to be check if exist in table before attempt to insert, update
     * delete, select
     */
  public static $tableSchemas =[
        "users" =>[
            "firstname","lastname","mail","phone", "password", "role"
        ],
      //Expand from here your table definitons
      ];
}