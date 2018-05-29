<?php
$module_name = 'Favorites';
$viewdefs [$module_name] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'useTabs' => true,
      'tabDefs' => 
      array (
        'LBL_PANEL_OVERVIEW' =>
        array (
          'newTab' => true,
          'panelDefault' => 'expanded',
        ),
        'LBL_EMAIL_INVITE' =>
        array (
          'newTab' => true,
          'panelDefault' => 'expanded',
        ),
      ),
      'syncDetailEditViews' => false,
    ),
    'panels' => 
    array (
      'LBL_PANEL_OVERVIEW' =>
      array (
        0 =>
        array (
          0 => 
          array (
            'name' => 'date_start',
            'type' => 'datetimecombo',
            'displayParams' => 
            array (
              'required' => true,
            ),
          ),
          1 => 
          array (
            'name' => 'date_end',
            'type' => 'datetimecombo',
            'displayParams' => 
            array (
              'required' => true,
            ),
          ),
        ),
        1 =>
        array (
          0 => 
          array (
            'name' => 'duration',
            'customCode' => '
                @@FIELD@@
                <input id="duration_hours" name="duration_hours" type="hidden" value="{$fields.duration_hours.value}">
                <input id="duration_minutes" name="duration_minutes" type="hidden" value="{$fields.duration_minutes.value}">
            ',
            'customCodeReadOnly' => '{$fields.duration_hours.value}{$MOD.LBL_HOURS_ABBREV} {$fields.duration_minutes.value}{$MOD.LBL_MINSS_ABBREV} ',
          ),
          1 => 
          array (
            'name' => 'budget',
            'label' => 'LBL_BUDGET',
          ),
        ),
        2 =>
        array (
          0 => 'description',
        ),
        3 =>
        array (
          0 => 'assigned_user_name',
        ),
      ),
      'LBL_EMAIL_INVITE' =>
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'invite_templates',
            'studio' => 'visible',
            'label' => 'LBL_INVITE_TEMPLATES',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'accept_redirect',
            'label' => 'LBL_ACCEPT_REDIRECT',
          ),
          1 => 
          array (
            'name' => 'decline_redirect',
            'label' => 'LBL_DECLINE_REDIRECT',
          ),
        ),
      ),
    ),
  ),
);

