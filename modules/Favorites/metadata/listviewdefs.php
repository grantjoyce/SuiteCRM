<?php
$module_name = 'Favorites';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '20%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'DATE_START' =>
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_DATE',
    'width' => '15%',
    'default' => true,
  ),
  'DATE_END' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_DATE_END',
    'width' => '15%',
    'default' => true,
  ),
  'BUDGET' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_BUDGET',
    'currency_format' => true,
    'width' => '15%',
    'default' => true,
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
);
