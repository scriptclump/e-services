<?php

return [

    /*
      |--------------------------------------------------------------------------
      | Validation Language Lines
      |--------------------------------------------------------------------------
      |
      | The following language lines contain the default error messages used by
      | the validator class. Some of these rules have multiple versions such
      | as the size rules. Feel free to tweak each of these messages here.
      |
     */
    'roles_title' => [
        'index_page_title' => 'Roles',
        'add_role_page_title' => 'Add Role',
        'edit_role_page_title' => 'Edit Role',
    ],

    'role_tab' => [
        'role' => 'Role',
        'role_add' => 'Add Role',
        'role_edit' => 'Edit Role',
        'role_permissions' => 'Permissions',
        'role_users' => 'Users',
        'role_name' => 'Role Name',
        'role_code' => 'Role Code',
        'role_description' => 'Description',
        'is_support_role' => 'Is Support Role',
        'parent_role' => 'Parent Role',
        'role_inherit' => 'Inherit Form',
        'role_select_users' => 'Select User',
        'role_assign_users' => 'Assign User',
    ],
    'add_role_form' => [
        'validate' => [
            'parent_role' => 'The parent role is required and cannot be empty.',
            'role_name' => 'The role name is required and cannot be empty.',
            'role_description' => 'The description is required and cannot be empty.',
            'role_reg_string' => 'The role name can consist only characters.',
            'role_exist' => 'Role name already exist.',
        ],
        'role_created' => 'Role is created successfully.',
        'role_users' => 'Users for role added successfully.',
    ],
    'edit_role_form' => [
        'role_update' => 'Role is updated successfully.',
        'role_users' => 'Users for role updated Successfull.',
        'role_exist' => 'Role name already exist.',
    ]
];
