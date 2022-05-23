<?php

return [
    'backoffice' => [
        'guestCategory' => [
            'title' => 'Category',
            'plural' => 'Categories',
            'fields' => [
                'name' => 'Name',
                'createdAt' => 'CreatedAt',
                'updatedAt' => 'UpdatedAt',
            ],
            'new' => [
                'category' => 'New Category',
            ],
            'add' =>  'New Category',
            'edit' =>  'Edit Category',
            'list' => 'Category List',
        ],
        'guestUser' => [
            'title' => 'Guest User',
            'plural' => 'Guest Users',
            'fields' => [
                'firstName' => 'First Name',
                'lastName' => 'Last Name',
                'country' => 'Country',
                'description' => 'Descriptions',
                'birthday' => 'Birthday',
                'active' => 'Activated',
                'admissionDate' => 'Admission Date',
                'phoneNumber' => 'Phone Number',
                'address' => 'Address',
                'photo' => 'Photo',
                'record' => 'Record',
                'wishToBeContacted' => 'Wish To Be Contacted',
                'canBeEdited' => 'Can Be Edited',
                'createdAt' => 'CreatedAt',
                'updatedAt' => 'UpdatedAt',
                'categories' => 'Categories',
            ],
            'new' => [
                'user' => 'New Guest User',
            ],
            'add' =>  'New Guest User',
            'edit' =>  'Edit Guest User',
            'list' => 'Guest User List',
        ],
    ],
];